<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\Comment;

use common_exception_Unauthorized;
use common_session_Session;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use oat\generis\model\data\Ontology;
use oat\oatbox\session\SessionService;
use oat\tao\model\accessControl\PermissionCheckerInterface;
use oat\tao\model\session\Context\UserDataSessionContext;
use Ramsey\Uuid\Uuid;

class ItemCommentService
{
    private ItemCommentPersistenceInterface $persistence;
    private SessionService $sessionService;
    private Ontology $ontology;
    private PermissionCheckerInterface $permissionChecker;
    private CommentRichTextSanitizer $commentRichTextSanitizer;

    public function __construct(
        ItemCommentPersistenceInterface $persistence,
        SessionService $sessionService,
        Ontology $ontology,
        PermissionCheckerInterface $permissionChecker,
        CommentRichTextSanitizer $commentRichTextSanitizer
    ) {
        $this->persistence = $persistence;
        $this->sessionService = $sessionService;
        $this->ontology = $ontology;
        $this->permissionChecker = $permissionChecker;
        $this->commentRichTextSanitizer = $commentRichTextSanitizer;
    }

    /**
     * @return array{comments: array<int, array<string, mixed>>, count: int}
     */
    public function list(string $resourceUri, string $resourceType): array
    {
        [$resourceUri, $resourceType] = $this->assertAuthorizedResource($resourceUri, $resourceType, false);

        $comments = $this->persistence->findByResource($resourceUri, $resourceType);
        $currentAuthorId = $this->tryResolveAuthorId();

        return [
            'comments' => array_map(
                static function (ItemComment $comment) use ($currentAuthorId): array {
                    return $comment->toArray(
                        $currentAuthorId !== null && $comment->getAuthorId() === $currentAuthorId
                    );
                },
                $comments
            ),
            'count' => count($comments),
        ];
    }

    public function create(string $resourceUri, string $resourceType, string $body): ItemComment
    {
        [$resourceUri, $resourceType] = $this->assertAuthorizedResource($resourceUri, $resourceType, true);
        $body = $this->assertBody($body);

        $session = $this->sessionService->getCurrentSession();
        if ($session === null || $session->getUser() === null) {
            throw new common_exception_Unauthorized('Authenticated session required to create item comments');
        }

        [$authorId, $authorLabel] = $this->resolveAuthorFromSession($session);

        $comment = new ItemComment(
            Uuid::uuid4()->toString(),
            $resourceUri,
            $resourceType,
            $authorId,
            $authorLabel,
            $body,
            (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DATE_ATOM)
        );

        return $this->persistence->create($comment);
    }

    public function update(string $commentId, string $body): ItemComment
    {
        $commentId = trim($commentId);
        if ($commentId === '') {
            throw new InvalidArgumentException('Comment id is required');
        }

        $body = $this->assertBody($body);

        $session = $this->sessionService->getCurrentSession();
        if ($session === null || $session->getUser() === null) {
            throw new common_exception_Unauthorized('Authenticated session required to update item comments');
        }

        [$authorId] = $this->resolveAuthorFromSession($session);

        $existing = $this->persistence->findById($commentId);
        if ($existing === null) {
            throw new InvalidArgumentException('Comment not found');
        }

        $this->assertAuthorizedResource($existing->getResourceUri(), $existing->getResourceType(), true);

        if ($existing->getAuthorId() !== $authorId) {
            throw new common_exception_Unauthorized('Only the comment author can edit this comment');
        }

        return $this->persistence->update($existing->withEditedBody($body));
    }

    public function resolve(string $commentId, bool $resolved): ItemComment
    {
        $commentId = trim($commentId);
        if ($commentId === '') {
            throw new InvalidArgumentException('Comment id is required');
        }

        $session = $this->sessionService->getCurrentSession();
        if ($session === null || $session->getUser() === null) {
            throw new common_exception_Unauthorized('Authenticated session required to resolve item comments');
        }

        $existing = $this->persistence->findById($commentId);
        if ($existing === null) {
            throw new InvalidArgumentException('Comment not found');
        }

        $this->assertAuthorizedResource($existing->getResourceUri(), $existing->getResourceType(), true);

        if ($existing->isResolved() === $resolved) {
            return $existing;
        }

        return $this->persistence->update($existing->withResolved($resolved));
    }

    public function delete(string $commentId): void
    {
        $commentId = trim($commentId);
        if ($commentId === '') {
            throw new InvalidArgumentException('Comment id is required');
        }

        $session = $this->sessionService->getCurrentSession();
        if ($session === null || $session->getUser() === null) {
            throw new common_exception_Unauthorized('Authenticated session required to delete item comments');
        }

        [$authorId] = $this->resolveAuthorFromSession($session);

        $existing = $this->persistence->findById($commentId);
        if ($existing === null) {
            throw new InvalidArgumentException('Comment not found');
        }

        $this->assertAuthorizedResource($existing->getResourceUri(), $existing->getResourceType(), true);

        if ($existing->getAuthorId() !== $authorId) {
            throw new common_exception_Unauthorized('Only the comment author can delete this comment');
        }

        $this->persistence->delete($commentId);
    }

    /**
     * Prefer LTI UserDataSessionContext when present on TaoLtiSession:
     * authorId from userId; authorLabel from userName, falling back to userLogin.
     *
     * @return array{0: string, 1: string}
     */
    private function resolveAuthorFromSession(common_session_Session $session): array
    {
        $authorId = (string) $session->getUser()->getIdentifier();
        $authorLabel = (string) $session->getUserLabel();

        /** @var UserDataSessionContext $context */
        foreach ($session->getContexts(UserDataSessionContext::class) as $context) {
            if ($context->getUserId()) {
                $authorId = (string) $context->getUserId();
            }

            $userName = $context->getUserName();
            if ($userName !== null && $userName !== '') {
                $authorLabel = $userName;
            } elseif ($context->getUserLogin() !== null && $context->getUserLogin() !== '') {
                $authorLabel = (string) $context->getUserLogin();
            }
        }

        if ($authorId === '') {
            throw new common_exception_Unauthorized('Unable to resolve comment author from session');
        }

        return [$authorId, $authorLabel];
    }

    private function tryResolveAuthorId(): ?string
    {
        $session = $this->sessionService->getCurrentSession();
        if ($session === null || $session->getUser() === null) {
            return null;
        }

        try {
            [$authorId] = $this->resolveAuthorFromSession($session);

            return $authorId;
        } catch (common_exception_Unauthorized $exception) {
            return null;
        }
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function assertAuthorizedResource(
        string $resourceUri,
        string $resourceType,
        bool $requireWriteAccess
    ): array {
        $resourceUri = $this->assertResourceUri($resourceUri);
        $resourceType = ResourceCommentType::assertValid($resourceType);

        $resource = $this->ontology->getResource($resourceUri);
        if (!$resource->exists()) {
            throw new InvalidArgumentException('Resource not found');
        }

        $expectedClass = $this->ontology->getClass(ResourceCommentType::classUri($resourceType));
        if (!$resource->isInstanceOf($expectedClass)) {
            throw new InvalidArgumentException(
                sprintf('Resource type does not match resourceType "%s"', $resourceType)
            );
        }

        $hasAccess = $requireWriteAccess
            ? $this->permissionChecker->hasWriteAccess($resourceUri)
            : $this->permissionChecker->hasReadAccess($resourceUri);

        if (!$hasAccess) {
            throw new common_exception_Unauthorized('Not authorized to access comments for this resource');
        }

        return [$resourceUri, $resourceType];
    }

    private function assertResourceUri(string $resourceUri): string
    {
        $resourceUri = trim($resourceUri);
        if ($resourceUri === '') {
            throw new InvalidArgumentException('resourceUri is required');
        }

        return $resourceUri;
    }

    private function assertBody(string $body): string
    {
        $body = $this->commentRichTextSanitizer->sanitize($body);
        if (!$this->commentRichTextSanitizer->hasMeaningfulText($body)) {
            throw new InvalidArgumentException('Comment body must not be empty');
        }

        return $body;
    }
}

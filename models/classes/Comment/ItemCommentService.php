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

    public function __construct(
        ItemCommentPersistenceInterface $persistence,
        SessionService $sessionService,
        Ontology $ontology,
        PermissionCheckerInterface $permissionChecker
    ) {
        $this->persistence = $persistence;
        $this->sessionService = $sessionService;
        $this->ontology = $ontology;
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * @return array{comments: array<int, array<string, string>>, count: int}
     */
    public function list(string $itemUri): array
    {
        $itemUri = $this->assertAuthorizedItemUri($itemUri, false);

        $comments = $this->persistence->findByItemUri($itemUri);

        return [
            'comments' => array_map(
                static function (ItemComment $comment): array {
                    return $comment->toArray();
                },
                $comments
            ),
            'count' => count($comments),
        ];
    }

    public function create(string $itemUri, string $body): ItemComment
    {
        $itemUri = $this->assertAuthorizedItemUri($itemUri, true);
        $body = $this->assertBody($body);

        $session = $this->sessionService->getCurrentSession();
        if ($session === null || $session->getUser() === null) {
            throw new common_exception_Unauthorized('Authenticated session required to create item comments');
        }

        [$authorId, $authorLabel] = $this->resolveAuthorFromSession($session);

        $comment = new ItemComment(
            Uuid::uuid4()->toString(),
            $itemUri,
            $authorId,
            $authorLabel,
            $body,
            (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DATE_ATOM)
        );

        return $this->persistence->create($comment);
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

    private function assertAuthorizedItemUri(string $itemUri, bool $requireWriteAccess): string
    {
        $itemUri = $this->assertItemUri($itemUri);

        $item = $this->ontology->getResource($itemUri);
        if (!$item->exists()) {
            throw new InvalidArgumentException('Item not found');
        }

        $hasAccess = $requireWriteAccess
            ? $this->permissionChecker->hasWriteAccess($itemUri)
            : $this->permissionChecker->hasReadAccess($itemUri);

        if (!$hasAccess) {
            throw new common_exception_Unauthorized('Not authorized to access comments for this item');
        }

        return $itemUri;
    }

    private function assertItemUri(string $itemUri): string
    {
        $itemUri = trim($itemUri);
        if ($itemUri === '') {
            throw new InvalidArgumentException('itemUri is required');
        }

        return $itemUri;
    }

    private function assertBody(string $body): string
    {
        $body = trim($body);
        if ($body === '') {
            throw new InvalidArgumentException('Comment body must not be empty');
        }

        return $body;
    }
}

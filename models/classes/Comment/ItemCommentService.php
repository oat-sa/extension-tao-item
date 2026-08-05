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
use oat\oatbox\session\SessionService;
use oat\tao\model\session\Context\UserDataSessionContext;
use Ramsey\Uuid\Uuid;

class ItemCommentService
{
    private ItemCommentPersistenceInterface $persistence;
    private SessionService $sessionService;

    public function __construct(
        ItemCommentPersistenceInterface $persistence,
        SessionService $sessionService
    ) {
        $this->persistence = $persistence;
        $this->sessionService = $sessionService;
    }

    /**
     * @return array{comments: array<int, array<string, string>>, count: int}
     */
    public function list(string $itemUri): array
    {
        $itemUri = $this->assertItemUri($itemUri);

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

    public function count(string $itemUri): int
    {
        $itemUri = $this->assertItemUri($itemUri);

        return $this->persistence->countByItemUri($itemUri);
    }

    public function create(string $itemUri, string $body): ItemComment
    {
        $itemUri = $this->assertItemUri($itemUri);
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
            (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DATE_ATOM),
            ItemComment::STATUS_ACTIVE
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

            if ($context->getUserName() !== null) {
                $authorLabel = (string) $context->getUserName();
            } elseif ($context->getUserLogin() !== null) {
                $authorLabel = (string) $context->getUserLogin();
            }
        }

        if ($authorId === '') {
            throw new common_exception_Unauthorized('Unable to resolve comment author from session');
        }

        return [$authorId, $authorLabel];
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

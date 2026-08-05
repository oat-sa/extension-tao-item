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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\Comment;

use common_exception_Unauthorized;
use common_session_Session;
use common_session_SessionManager;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use oat\oatbox\service\ConfigurableService;
use oat\tao\model\session\Context\UserDataSessionContext;
use Ramsey\Uuid\Uuid;

class ItemCommentService extends ConfigurableService
{
    public const SERVICE_ID = 'taoItems/ItemCommentService';

    /**
     * @return array{comments: array<int, array<string, string>>, count: int}
     */
    public function list(string $itemUri): array
    {
        $itemUri = $this->assertItemUri($itemUri);

        $comments = $this->getPersistence()->findByItemUri($itemUri);

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

        return $this->getPersistence()->countByItemUri($itemUri);
    }

    public function create(string $itemUri, string $body): ItemComment
    {
        $itemUri = $this->assertItemUri($itemUri);
        $body = $this->assertBody($body);

        $session = common_session_SessionManager::getSession();
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

        return $this->getPersistence()->create($comment);
    }

    /**
     * Prefer LTI UserDataSessionContext (userId / userLogin) when present on TaoLtiSession.
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
            if ($context->getUserLogin()) {
                $authorLabel = (string) $context->getUserLogin();
            }
        }

        if ($authorId === '') {
            throw new common_exception_Unauthorized('Unable to resolve comment author from session');
        }

        return [$authorId, $authorLabel];
    }

    private function getPersistence(): ItemCommentPersistenceInterface
    {
        return $this->getServiceLocator()->get(ItemCommentPersistenceProxy::SERVICE_ID);
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

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
use common_session_SessionManager;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use oat\oatbox\service\ConfigurableService;
use oat\tao\model\featureFlag\FeatureFlagChecker;
use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;
use Ramsey\Uuid\Uuid;

class ItemCommentService extends ConfigurableService
{
    public const SERVICE_ID = 'taoItems/ItemCommentService';

    /**
     * @return array{comments: array<int, array<string, string>>, count: int}
     */
    public function list(string $itemUri): array
    {
        $this->assertFeatureEnabled();
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
        $this->assertFeatureEnabled();
        $itemUri = $this->assertItemUri($itemUri);

        return $this->getPersistence()->countByItemUri($itemUri);
    }

    public function create(string $itemUri, string $body): ItemComment
    {
        $this->assertFeatureEnabled();
        $itemUri = $this->assertItemUri($itemUri);
        $body = $this->assertBody($body);

        $session = common_session_SessionManager::getSession();
        if ($session === null || $session->getUser() === null) {
            throw new common_exception_Unauthorized('Authenticated session required to create item comments');
        }

        $comment = new ItemComment(
            Uuid::uuid4()->toString(),
            $itemUri,
            (string) $session->getUser()->getIdentifier(),
            (string) $session->getUserLabel(),
            $body,
            (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DATE_ATOM),
            ItemComment::STATUS_ACTIVE
        );

        return $this->getPersistence()->create($comment);
    }

    private function getPersistence(): ItemCommentPersistenceInterface
    {
        return $this->getServiceLocator()->get(ItemCommentPersistenceProxy::SERVICE_ID);
    }

    private function getFeatureFlagChecker(): FeatureFlagCheckerInterface
    {
        return $this->getServiceLocator()->get(FeatureFlagChecker::class);
    }

    private function assertFeatureEnabled(): void
    {
        if (
            !$this->getFeatureFlagChecker()->isEnabled(
                FeatureFlagCheckerInterface::FEATURE_FLAG_ITEM_COMMENTS_ENABLED
            )
        ) {
            throw new common_exception_Unauthorized('Item comments feature is disabled');
        }
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

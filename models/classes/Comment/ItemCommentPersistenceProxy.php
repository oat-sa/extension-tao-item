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

use oat\oatbox\service\ConfigurableService;

/**
 * Delegates to the active adapter (RDF by default; Elasticsearch when AS replaces it).
 */
class ItemCommentPersistenceProxy extends ConfigurableService implements ItemCommentPersistenceInterface
{
    public const SERVICE_ID = 'taoItems/ItemCommentPersistence';
    public const OPTION_ACTIVE_ADAPTER = 'activeAdapter';

    public function create(ItemComment $comment): ItemComment
    {
        return $this->getActiveAdapter()->create($comment);
    }

    public function findByItemUri(string $itemUri): array
    {
        return $this->getActiveAdapter()->findByItemUri($itemUri);
    }

    public function countByItemUri(string $itemUri): int
    {
        return $this->getActiveAdapter()->countByItemUri($itemUri);
    }

    private function getActiveAdapter(): ItemCommentPersistenceInterface
    {
        $adapterId = $this->getOption(
            self::OPTION_ACTIVE_ADAPTER,
            RdfItemCommentAdapter::SERVICE_ID
        );

        /** @var ItemCommentPersistenceInterface $adapter */
        $adapter = $this->getServiceLocator()->get($adapterId);

        return $adapter;
    }
}

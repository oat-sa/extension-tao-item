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

/**
 * Comment @mention autocomplete. Portal/Dynamic API implementation lives in taoDeliverConnect.
 */
interface CommentMentionUserSearchServiceInterface
{
    /**
     * @return array{
     *     users: array<int, array{id: string, login: string, displayName: string}>,
     *     limit: int
     * }
     */
    public function search(
        string $resourceUri,
        string $resourceType,
        string $query,
        int $limit
    ): array;
}

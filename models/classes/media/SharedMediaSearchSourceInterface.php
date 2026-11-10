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
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA.
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\media;

/**
 * Shared (ontology-backed) media libraries: scope + paginated instance listing for Generis fallback.
 *
 * Implemented by taoMediaManager {@see \oat\taoMediaManager\model\MediaSource} when that extension is installed.
 */
interface SharedMediaSearchSourceInterface
{
    /**
     * Ontology class URI for Resource Manager search/browse scope.
     */
    public function resolveScopeClassUri(string $parentLink): string;

    /**
     * Recursive scoped instance query (browse-style empty text search).
     *
     * @param string[] $mimeFilters
     * @return array{rows: array<int, array{id: string, label: string}>, total: int}
     */
    public function searchScopedMediaInstances(
        string $scopeClassUri,
        array $mimeFilters,
        int $offset,
        int $limit,
        string $sortBy,
        string $sortDir
    ): array;
}

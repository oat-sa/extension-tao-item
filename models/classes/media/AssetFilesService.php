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

use oat\tao\model\media\MediaAsset;

/**
 * Orchestrates Resource Manager browse vs scoped search and current-asset context.
 */
final class AssetFilesService
{
    public const DEFAULT_SORT_BY = 'label';
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PAGE_SIZE = 10;

    /** @var AssetSearchBuilder */
    private $searchBuilder;

    /** @var AssetTreeBuilderInterface */
    private $treeBuilder;

    /** @var CurrentAssetResolver */
    private $currentAssetResolver;

    public function __construct(
        AssetSearchBuilder $searchBuilder,
        AssetTreeBuilderInterface $treeBuilder,
        CurrentAssetResolver $currentAssetResolver
    ) {
        $this->searchBuilder = $searchBuilder;
        $this->treeBuilder = $treeBuilder;
        $this->currentAssetResolver = $currentAssetResolver;
    }

    /**
     * @param array<string, mixed> $params validated request params
     * @param array<int, string> $filters MIME filters
     * @return array<string, mixed>
     * @throws AssetSearchUnavailableException
     */
    public function listFiles(
        MediaAsset $asset,
        string $itemUri,
        string $itemLang,
        array $params,
        array $filters
    ): array {
        $childrenOffset = (int)($params['childrenOffset'] ?? AssetTreeBuilder::DEFAULT_PAGINATION_OFFSET);

        $searchQuery = new AssetSearchQuery(
            $asset,
            $itemUri,
            $itemLang,
            $filters,
            1,
            $childrenOffset
        );

        $searchQuery
            ->setSortBy((string)($params['sortBy'] ?? self::DEFAULT_SORT_BY))
            ->setSortDir((string)($params['sortDir'] ?? 'asc'))
            ->setMetadataCriteria(is_array($params['metadata'] ?? null) ? $params['metadata'] : []);

        $queryText = trim((string)($params['query'] ?? ''));
        if ($queryText !== '' || $searchQuery->hasMetadataCriteria()) {
            $searchQuery
                ->setQuery($queryText)
                ->setPage((int)($params['page'] ?? self::DEFAULT_PAGE))
                ->setPageSize((int)($params['pageSize'] ?? self::DEFAULT_PAGE_SIZE));

            $response = $this->searchBuilder->search($searchQuery);
        } else {
            $response = $this->treeBuilder->build($searchQuery);
        }

        return $this->attachCurrentAssetContext($response, $itemUri, $itemLang, $params, $filters);
    }

    /**
     * @param array<string, mixed> $response
     * @param array<string, mixed> $params
     * @param array<int, string> $filters
     * @return array<string, mixed>
     */
    private function attachCurrentAssetContext(
        array $response,
        string $itemUri,
        string $itemLang,
        array $params,
        array $filters
    ): array {
        $currentAssetUrl = trim((string)($params['currentAsset'] ?? ''));
        if ($currentAssetUrl === '') {
            return $response;
        }

        $resolved = $this->currentAssetResolver->resolve(
            $itemUri,
            $itemLang,
            $currentAssetUrl,
            $filters
        );

        $response['parentPath'] = $resolved['parentPath'];
        $response['currentAsset'] = $resolved['currentAsset'];

        return $response;
    }
}

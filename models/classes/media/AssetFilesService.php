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
use oat\tao\model\media\mediaSource\DirectorySearchQuery;

/**
 * Entry point for item Resource Manager file listing (browse and scoped search).
 *
 * Browse supports {@see self::PART_TREE} / {@see self::PART_LIST} split requests; omit
 * {@code part} for legacy combined tree+list payload.
 */
final class AssetFilesService
{
    public const PART_TREE = 'tree';
    public const PART_LIST = 'list';

    public const DEFAULT_SORT_BY = 'label';
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PAGE_SIZE = 11;

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
        $searchQuery = $this->createSearchQuery($asset, $itemUri, $itemLang, $params, $filters);
        $part = $this->normalizeBrowsePart($params['part'] ?? null);

        $queryText = trim((string)($params['query'] ?? ''));
        if ($queryText !== '' || $searchQuery->hasMetadataCriteria()) {
            $searchQuery
                ->setQuery($queryText)
                ->setPage((int)($params['page'] ?? self::DEFAULT_PAGE))
                ->setPageSize((int)($params['pageSize'] ?? self::DEFAULT_PAGE_SIZE));

            return $this->attachCurrentAssetContext(
                $this->searchBuilder->search($searchQuery),
                $itemUri,
                $itemLang,
                $params,
                $filters
            );
        }

        if ($part === self::PART_TREE) {
            $searchQuery
                ->setDepth(1)
                ->setChildrenLimit(AssetSearchQuery::CHILDREN_DIRECTORIES_ONLY);

            $treeResponse = $this->treeBuilder instanceof AssetTreeBrowseBuilderInterface
                ? $this->treeBuilder->buildTree($searchQuery)
                : $this->treeBuilder->build($searchQuery);

            return $this->attachCurrentAssetContext(
                $treeResponse,
                $itemUri,
                $itemLang,
                $params,
                $filters
            );
        }

        if ($part === self::PART_LIST) {
            [$page, $pageSize] = $this->resolveListPagination($params);
            $searchQuery->setPage($page)->setPageSize($pageSize);

            return $this->attachCurrentAssetContext(
                $this->buildBrowseAssetList($searchQuery),
                $itemUri,
                $itemLang,
                $params,
                $filters
            );
        }

        return $this->attachCurrentAssetContext(
            $this->treeBuilder->build($searchQuery),
            $itemUri,
            $itemLang,
            $params,
            $filters
        );
    }

    /**
     * @param array<string, mixed> $params
     * @param array<int, string> $filters
     */
    private function createSearchQuery(
        MediaAsset $asset,
        string $itemUri,
        string $itemLang,
        array $params,
        array $filters
    ): AssetSearchQuery {
        $depth = max(1, (int)($params['depth'] ?? 1));
        $childrenOffset = (int)($params['childrenOffset'] ?? AssetTreeBuilder::DEFAULT_PAGINATION_OFFSET);

        $searchQuery = new AssetSearchQuery(
            $asset,
            $itemUri,
            $itemLang,
            $filters,
            $depth,
            $childrenOffset
        );

        $searchQuery
            ->setSortBy((string)($params['sortBy'] ?? self::DEFAULT_SORT_BY))
            ->setSortDir((string)($params['sortDir'] ?? 'asc'))
            ->setMetadataCriteria(is_array($params['metadata'] ?? null) ? $params['metadata'] : []);

        return $searchQuery;
    }

    /**
     * @return array<string, mixed>
     * @throws AssetSearchUnavailableException
     */
    private function buildBrowseAssetList(AssetSearchQuery $searchQuery): array
    {
        if ($this->treeBuilder instanceof AssetBrowseListBuilderInterface) {
            return $this->treeBuilder->buildAssetList($searchQuery);
        }

        $searchQuery->setQuery('');

        return $this->searchBuilder->search($searchQuery);
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function resolveListPagination(array $params): array
    {
        $pageSize = (int)($params['pageSize'] ?? 0);
        if ($pageSize <= 0) {
            $pageSize = (int)($params['childrenLimit'] ?? 0);
        }
        if ($pageSize <= 0) {
            $pageSize = self::DEFAULT_PAGE_SIZE;
        }
        $pageSize = min($pageSize, AssetSearchQuery::MAX_PAGE_SIZE);

        if (isset($params['page'])) {
            $page = max(1, (int)$params['page']);
        } else {
            $offset = max(0, (int)($params['childrenOffset'] ?? 0));
            $page = $pageSize > 0 ? (int)floor($offset / $pageSize) + 1 : self::DEFAULT_PAGE;
        }

        return [$page, $pageSize];
    }

    private function normalizeBrowsePart(?string $part): ?string
    {
        if ($part === null) {
            return null;
        }

        $part = strtolower(trim($part));
        if ($part === '') {
            return null;
        }

        if ($part !== self::PART_TREE && $part !== self::PART_LIST) {
            throw new \InvalidArgumentException(
                sprintf('Invalid browse part "%s"; expected "tree" or "list".', $part)
            );
        }

        return $part;
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

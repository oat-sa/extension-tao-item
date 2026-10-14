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

use oat\tao\model\accessControl\AccessControlEnablerInterface;
use oat\tao\model\media\MediaManagement;
use oat\tao\model\search\ResultSet;
use oat\tao\model\search\Search;
use tao_models_classes_FileNotFoundException;

/**
 * Builds Resource Manager search payloads from the existing media browse sources.
 *
 * Prefers the indexed search gateway when available. Without Elasticsearch, shared media
 * ({@see SharedMediaSearchSourceInterface}) uses paginated Generis label search like the Assets backoffice.
 * Other sources fall back to filesystem subtree traversal with trailing-token prefix matching.
 */
class AssetSearchBuilder
{
    public const SERVICE_ID = 'taoItems/AssetSearchBuilder';

    private const FULL_SUBTREE_DEPTH = PHP_INT_MAX;
    /**
     * No children cap on filesystem fallback (0 = unlimited in MediaSource::searchDirectories).
     * Full subtree load keeps page-1 totals and later pages consistent when paginating in memory.
     */
    private const FALLBACK_CHILDREN_LIMIT = 0;
    /**
     * Batch size for Generis text search and for browse over-fetch when ACL drops rows from a DB page.
     * Upgrade path: indexed gateway or ACL-aware SQL paging when libraries outgrow batch scans.
     */
    private const SHARED_MEDIA_GENERIS_BATCH_SIZE = 200;
    private const SHARED_MEDIA_BROWSE_OVERFETCH_MAX_ITERATIONS = 20;
    private const SORT_LOCATION = 'location';
    private const SORT_UPDATED_AT = 'updatedAt';

    private ?AssetIndexedSearchGatewayInterface $indexedSearchGateway = null;

    private ?AssetUpdatedAtResolverInterface $updatedAtResolver = null;

    private ?AssetListingReadAccessChecker $listingReadAccessChecker = null;

    private ?Search $generisMediaSearch = null;

    public function __construct(
        ?AssetIndexedSearchGatewayInterface $indexedSearchGateway = null,
        ?AssetListingReadAccessChecker $listingReadAccessChecker = null,
        ?AssetUpdatedAtResolverInterface $updatedAtResolver = null,
        ?Search $generisMediaSearch = null
    ) {
        $this->indexedSearchGateway = $indexedSearchGateway;
        $this->listingReadAccessChecker = $listingReadAccessChecker;
        $this->updatedAtResolver = $updatedAtResolver;
        $this->generisMediaSearch = $generisMediaSearch;
    }

    public function isIndexedGatewayAvailable(): bool
    {
        if ($this->indexedSearchGateway === null) {
            return false;
        }

        try {
            return $this->indexedSearchGateway->isAvailable();
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public function search(AssetSearchQuery $search): array
    {
        if ($this->indexedSearchGateway !== null) {
            try {
                $available = $this->indexedSearchGateway->isAvailable();
            } catch (\Throwable $exception) {
                $available = false;
            }

            if ($available) {
                try {
                    return $this->indexedSearchGateway->search($search);
                } catch (AssetSearchUnavailableException $exception) {
                    throw $exception;
                } catch (\Throwable $exception) {
                    throw new AssetSearchUnavailableException(
                        'Indexed asset search gateway failed: ' . $exception->getMessage(),
                        0,
                        $exception
                    );
                }
            }
        }

        // Metadata filters require the indexed gateway; surface 503 via controller.
        if ($search->hasMetadataCriteria()) {
            throw new AssetSearchUnavailableException(
                'Metadata asset search requires an available indexed search gateway'
            );
        }

        $asset = $search->getAsset();
        $mediaSource = $asset->getMediaSource();

        if ($mediaSource instanceof AccessControlEnablerInterface) {
            $mediaSource->enableAccessControl();
        }

        if ($mediaSource instanceof SharedMediaSearchSourceInterface) {
            return $this->searchSharedMediaViaGeneris($search, $mediaSource);
        }

        // Do not mutate the caller's query. Indexed path preferred for very large scopes.
        $fetchQuery = new AssetSearchQuery(
            $search->getAsset(),
            $search->getItemUri(),
            $search->getItemLang(),
            $search->getFilter(),
            self::FULL_SUBTREE_DEPTH,
            0,
            self::FALLBACK_CHILDREN_LIMIT
        );

        $tree = $mediaSource->getDirectories($fetchQuery);
        $scopePath = (string)($tree['path'] ?? $search->getParentLink());
        $scopeLabel = (string)($tree['locationPath'] ?? $tree['label'] ?? $scopePath);
        $sourceTotal = array_key_exists('total', $tree) ? (int)$tree['total'] : null;

        $items = $this->flattenAssets($tree, $scopePath, $scopeLabel);
        $flattenedCount = count($items);
        $items = $this->filterReadableAssets($items, $search);
        $truncated = $sourceTotal !== null && $sourceTotal > $flattenedCount;

        $items = $this->filterByQuery($items, $search->getQuery());
        $items = $this->sortItems($items, $search->getSortBy(), $search->getSortDir());

        $total = count($items);
        $pageSize = $search->getPageSize();
        $maxPage = max(1, (int)ceil($total / $pageSize) ?: 1);
        $page = min($search->getPage(), $maxPage);
        $offset = ($page - 1) * $pageSize;

        return [
            'items' => array_values(array_slice($items, $offset, $pageSize)),
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'truncated' => $truncated,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function searchSharedMediaViaGeneris(
        AssetSearchQuery $search,
        SharedMediaSearchSourceInterface $mediaSource
    ): array {
        $page = $search->getPage();
        $pageSize = $search->getPageSize();
        $scopeClassUri = $mediaSource->resolveScopeClassUri($search->getParentLink());
        $queryString = trim($search->getQuery());

        if ($queryString === '' && !$this->sharedMediaGenerisRequiresFullScan($search)) {
            return $this->searchSharedMediaBrowseViaGenerisPaged($search, $mediaSource, $scopeClassUri);
        }

        $rows = $this->collectSharedMediaGenerisRows($scopeClassUri, $queryString);
        $items = $this->hydrateSharedMediaRows($rows, $search, $mediaSource);
        $items = $this->filterReadableAssets($items, $search);
        $items = $this->sortItems($items, $search->getSortBy(), $search->getSortDir());

        $total = count($items);
        $maxPage = max(1, (int)ceil($total / $pageSize) ?: 1);
        $page = min($page, $maxPage);
        $offset = ($page - 1) * $pageSize;

        return [
            'items' => array_values(array_slice($items, $offset, $pageSize)),
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'truncated' => false,
            'includesSubfolders' => true,
        ];
    }

    private function sharedMediaGenerisRequiresFullScan(AssetSearchQuery $search): bool
    {
        return in_array($search->getSortBy(), [self::SORT_LOCATION, self::SORT_UPDATED_AT], true);
    }

    /**
     * @return array<string, mixed>
     */
    private function searchSharedMediaBrowseViaGenerisPaged(
        AssetSearchQuery $search,
        SharedMediaSearchSourceInterface $mediaSource,
        string $scopeClassUri
    ): array {
        $page = max(1, $search->getPage());
        $pageSize = max(1, $search->getPageSize());
        $dbOffset = ($page - 1) * $pageSize;
        $listOffset = $dbOffset;

        $items = [];
        $scopedTotal = 0;

        for ($iteration = 0; $iteration < self::SHARED_MEDIA_BROWSE_OVERFETCH_MAX_ITERATIONS; $iteration++) {
            if (count($items) >= $pageSize) {
                break;
            }

            $need = $pageSize - count($items);
            $fetchLimit = min(max($need, 1), self::SHARED_MEDIA_GENERIS_BATCH_SIZE);

            $scoped = $mediaSource->searchScopedMediaInstances(
                $scopeClassUri,
                $search->getFilter(),
                $dbOffset,
                $fetchLimit,
                $search->getSortBy(),
                $search->getSortDir()
            );
            if ($dbOffset === $listOffset && array_key_exists('total', $scoped)) {
                $scopedTotal = (int)$scoped['total'];
            }
            $batch = [];
            foreach ($scoped['rows'] ?? [] as $row) {
                if (is_array($row)) {
                    $batch[] = $row;
                }
            }
            if ($batch === []) {
                break;
            }

            $pageItems = $this->filterReadableAssets(
                $this->hydrateSharedMediaRows($batch, $search, $mediaSource),
                $search
            );
            foreach ($pageItems as $item) {
                $items[] = $item;
                if (count($items) >= $pageSize) {
                    break;
                }
            }

            $dbOffset += count($batch);
            if (count($batch) < $fetchLimit) {
                break;
            }
        }

        $maxPage = max(1, (int)ceil($scopedTotal / $pageSize) ?: 1);
        $page = min($page, $maxPage);

        return [
            'items' => array_values($items),
            'total' => $scopedTotal,
            'page' => $page,
            'pageSize' => $pageSize,
            'truncated' => false,
            'includesSubfolders' => true,
            'totalIsApproximate' => $this->listingReadAccessChecker !== null,
        ];
    }

    /**
     * @return array<int, array{id?: string, label?: string}>
     */
    private function collectSharedMediaGenerisRows(
        string $scopeClassUri,
        string $queryString
    ): array {
        $rows = [];
        $offset = 0;
        $batchSize = self::SHARED_MEDIA_GENERIS_BATCH_SIZE;

        if ($queryString === '') {
            throw new \LogicException('Empty shared-media browse must use paged Generis listing');
        }

        if ($this->generisMediaSearch === null) {
            throw new AssetSearchUnavailableException(
                'Text asset search requires GenerisSearch when the indexed gateway is unavailable'
            );
        }

        while (true) {
            $resultSet = $this->generisMediaSearch->query(
                $queryString,
                $scopeClassUri,
                $offset,
                $batchSize
            );
            $batch = [];
            foreach ($resultSet as $row) {
                if (is_array($row) && !empty($row['id'])) {
                    $batch[] = $row;
                }
            }
            if ($batch === []) {
                break;
            }
            foreach ($batch as $row) {
                $rows[] = $row;
            }
            $generisTotal = (int)$resultSet->getTotalCount();
            if ($generisTotal <= $offset + count($batch)) {
                break;
            }
            $offset += count($batch);
        }

        return $rows;
    }

    /**
     * @param array<int, array{id?: string, label?: string}> $rows
     * @return array<int, array<string, mixed>>
     */
    private function hydrateSharedMediaRows(
        array $rows,
        AssetSearchQuery $search,
        SharedMediaSearchSourceInterface $mediaSource
    ): array {
        if (!$mediaSource instanceof MediaManagement) {
            return [];
        }

        $items = [];
        foreach ($rows as $row) {
            if (empty($row['id'])) {
                continue;
            }
            try {
                $fileInfo = $mediaSource->getFileInfo((string)$row['id']);
            } catch (tao_models_classes_FileNotFoundException $exception) {
                continue;
            }
            if (!$this->matchesMimeFilter($fileInfo, $search->getFilter())) {
                continue;
            }
            $items[] = $this->normalizeAsset($fileInfo, (string)($fileInfo['location'] ?? ''));
        }

        return $items;
    }

    /**
     * @param array<string, mixed> $fileInfo
     * @param string[] $filters
     */
    private function matchesMimeFilter(array $fileInfo, array $filters): bool
    {
        if ($filters === []) {
            return true;
        }

        $mime = (string)($fileInfo['mime'] ?? '');

        return $mime !== '' && in_array($mime, $filters, true);
    }

    private function getUpdatedAtResolver(): AssetUpdatedAtResolverInterface
    {
        if ($this->updatedAtResolver === null) {
            throw new \RuntimeException('AssetUpdatedAtResolverInterface is not configured');
        }

        return $this->updatedAtResolver;
    }

    /**
     * @param array<int, array> $items
     * @return array<int, array>
     */
    private function filterReadableAssets(array $items, AssetSearchQuery $search): array
    {
        if ($this->listingReadAccessChecker === null) {
            return $items;
        }

        $mediaSource = $search->getAsset()->getMediaSource();
        $itemUri = $search->getItemUri();

        return array_values(
            array_filter(
                $items,
                function (array $item) use ($mediaSource, $itemUri): bool {
                    return $this->listingReadAccessChecker->canReadListedAsset($mediaSource, $item, $itemUri);
                }
            )
        );
    }

    /**
     * @param array $node
     * @return array<int, array>
     */
    private function flattenAssets(array $node, string $scopePath, string $scopeLabel, string $location = ''): array
    {
        $items = [];
        $currentLocation = $location !== ''
            ? $location
            : ($scopeLabel !== '' ? $scopeLabel : $scopePath);

        foreach ($node['children'] ?? [] as $child) {
            if (!is_array($child)) {
                continue;
            }

            if ($this->isDirectoryNode($child)) {
                $childLabel = trim((string)($child['label'] ?? ''));
                if ($childLabel === '') {
                    $path = trim((string)($child['path'] ?? ''), '/');
                    if ($path !== '' && str_contains($path, '/')) {
                        $path = substr($path, (int)strrpos($path, '/') + 1);
                    }
                    $childLabel = $path;
                }
                $childLocation = $currentLocation === ''
                    ? $childLabel
                    : ($childLabel === ''
                        ? $currentLocation
                        : trim($currentLocation . '/' . $childLabel, '/'));
                foreach ($this->flattenAssets($child, $scopePath, $scopeLabel, $childLocation) as $nestedItem) {
                    $items[] = $nestedItem;
                }
                continue;
            }

            if (!$this->isAssetNode($child)) {
                continue;
            }

            $items[] = $this->normalizeAsset($child, $currentLocation);
        }

        return $items;
    }

    private function isDirectoryNode(array $node): bool
    {
        return array_key_exists('children', $node)
            || (isset($node['path']) && !isset($node['mime']) && !isset($node['uri']));
    }

    private function isAssetNode(array $node): bool
    {
        return isset($node['mime']) || isset($node['uri']) || isset($node['name']) || isset($node['label']);
    }

    private function normalizeAsset(array $asset, string $location): array
    {
        $label = (string)($asset['label'] ?? $asset['name'] ?? $asset['alt'] ?? '');
        $name = (string)($asset['name'] ?? $label);
        $normalized = $asset;
        $normalized['label'] = $label !== '' ? $label : $name;
        $normalized['name'] = $name !== '' ? $name : $label;
        $normalized['location'] = (string)($asset['location'] ?? $location);
        $normalized['updatedAt'] = $this->getUpdatedAtResolver()->resolveForAsset($normalized);
        unset($normalized['updated_at']);

        return $normalized;
    }

    /**
     * @param array<int, array> $items
     * @return array<int, array>
     */
    private function filterByQuery(array $items, string $query): array
    {
        $trimmedQuery = trim($query);
        $queryTokens = $this->tokenize($query);
        if ($queryTokens === []) {
            return $trimmedQuery !== '' ? [] : $items;
        }

        return array_values(array_filter($items, function (array $item) use ($queryTokens): bool {
            $haystack = $this->tokenize(
                implode(
                    ' ',
                    array_filter([
                        $item['label'] ?? '',
                        $item['name'] ?? '',
                        $item['location'] ?? '',
                        $item['alt'] ?? '',
                    ])
                )
            );

            foreach ($queryTokens as $token) {
                $matched = false;
                foreach ($haystack as $candidate) {
                    if (str_starts_with($candidate, $token)) {
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    return false;
                }
            }

            return true;
        }));
    }

    /**
     * @return string[]
     */
    private function tokenize(string $value): array
    {
        $normalized = mb_strtolower(trim($value), 'UTF-8');
        if ($normalized === '') {
            return [];
        }

        $parts = preg_split('/[^\p{L}\p{N}]+/u', $normalized) ?: [];
        return array_values(array_filter($parts, static function (string $part): bool {
            return $part !== '';
        }));
    }

    /**
     * @param array<int, array> $items
     * @return array<int, array>
     */
    private function sortItems(array $items, string $sortBy, string $sortDir): array
    {
        $nullsLast = in_array($sortBy, [self::SORT_LOCATION, self::SORT_UPDATED_AT], true);

        usort($items, function (array $left, array $right) use ($sortBy, $sortDir, $nullsLast): int {
            if ($nullsLast) {
                $leftMissing = $this->isMissingSortValue($left, $sortBy);
                $rightMissing = $this->isMissingSortValue($right, $sortBy);
                if ($leftMissing || $rightMissing) {
                    if ($leftMissing && $rightMissing) {
                        $result = 0;
                    } else {
                        return $leftMissing ? 1 : -1;
                    }
                } else {
                    $result = $this->sortValue($left, $sortBy) <=> $this->sortValue($right, $sortBy);
                }
            } else {
                $result = $this->sortValue($left, $sortBy) <=> $this->sortValue($right, $sortBy);
            }

            if ($result === 0 && $sortBy !== AssetSearchQuery::SORT_LABEL) {
                $result = $this->sortValue($left, AssetSearchQuery::SORT_LABEL)
                    <=> $this->sortValue($right, AssetSearchQuery::SORT_LABEL);
            }

            if ($result === 0) {
                $result = (string)($left['uri'] ?? '') <=> (string)($right['uri'] ?? '');
            }

            return $sortDir === 'desc' ? -$result : $result;
        });

        return $items;
    }

    private function isMissingSortValue(array $item, string $sortBy): bool
    {
        if ($sortBy === self::SORT_LOCATION) {
            return trim((string)($item['location'] ?? '')) === '';
        }
        if ($sortBy === self::SORT_UPDATED_AT) {
            return !isset($item['updatedAt']) && !isset($item['updated_at']);
        }

        return false;
    }

    private function sortValue(array $item, string $sortBy): string
    {
        if ($sortBy === self::SORT_LOCATION) {
            return mb_strtolower((string)($item['location'] ?? ''), 'UTF-8');
        }
        if ($sortBy === self::SORT_UPDATED_AT) {
            return (string)($item['updatedAt'] ?? $item['updated_at'] ?? '');
        }

        return mb_strtolower((string)($item['label'] ?? $item['name'] ?? ''), 'UTF-8');
    }
}

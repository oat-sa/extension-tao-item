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
 * Copyright (c) 2020-2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\media;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;
use oat\tao\model\accessControl\AccessControlEnablerInterface;
use oat\tao\model\media\mediaSource\DirectorySearchQuery;
use tao_helpers_Uri;

class AssetTreeBuilder extends ConfigurableService implements
    AssetTreeBuilderInterface,
    AssetTreeBrowseBuilderInterface,
    AssetBrowseListBuilderInterface
{
    public const SERVICE_ID = 'taoItems/AssetTreeBuilder';

    public const OPTION_PAGINATION_LIMIT = 'pagination_limit';
    public const DEFAULT_PAGINATION_OFFSET = 0;
    private const DEFAULT_PAGINATION_LIMIT = 15;
    private const SORT_LABEL = 'label';
    private const SORT_LOCATION = 'location';
    private const SORT_UPDATED_AT = 'updatedAt';

    /**
     * Descendants of the active folder (media source scopes the root class). Flat table
     * lists nested files via collectFiles(); media fetch uses unlimited childrenLimit, then sort+slice.
     */
    private const BROWSE_SUBTREE_DEPTH = PHP_INT_MAX;
    /** Finite ceiling for childrenOffset so offset+pageSize stays an int (no float overflow). */
    protected const MAX_CHILDREN_OFFSET = 10000;

    /** @see \oat\taoMediaManager\model\MediaSource::SCHEME_NAME */
    private const MEDIA_BROWSER_SCHEME = 'taomedia://mediamanager/';

    /** @var ResourceUpdatedAtResolver|null */
    private $updatedAtResolver;

    public function build(DirectorySearchQuery $search): array
    {
        $pageSize = $this->resolveBrowsePageSize($search);
        $offset = $this->clampBrowseOffset($search->getChildrenOffset(), $pageSize);

        $mediaSource = $search->getAsset()->getMediaSource();

        if ($mediaSource instanceof AccessControlEnablerInterface) {
            $mediaSource->enableAccessControl();
        }

        $fetchQuery = $this->createFetchQuery($search);
        $data = $mediaSource->getDirectories($fetchQuery);
        $sourceReportedTotal = array_key_exists('total', $data) ? (int)$data['total'] : null;
        $children = $data['children'] ?? [];

        $scopeLabel = (string)($data['locationPath'] ?? $data['label'] ?? $data['path'] ?? '');
        $directories = [];
        $files = [];
        $totalFiles = 0;

        foreach ($children as $child) {
            if (!is_array($child)) {
                continue;
            }

            if ($this->isFileChild($child)) {
                $totalFiles++;
                $files[] = $this->normalizeFile($child, $scopeLabel);
                continue;
            }

            // Directories and other non-file nodes stay as stubs for tree expand.
            $directories[] = $this->toDirectoryStub($child, $search);
            $this->collectFiles(
                $child['children'] ?? [],
                $this->childLocation($scopeLabel, $child),
                $files,
                $totalFiles
            );
        }
        $files = $this->sortFiles($files, $this->resolveSortBy($search), $this->resolveSortDir($search));
        $loadedCount = count($files);
        // Prefer source recursive total when present (counts beyond the enrichment window).
        $data['total'] = $sourceReportedTotal !== null
            ? max($sourceReportedTotal, $totalFiles)
            : $totalFiles;
        $data['truncated'] = $data['total'] > $loadedCount;
        $data['childrenLimit'] = $pageSize;
        $data['children'] = array_merge($directories, array_slice($files, $offset, $pageSize));

        return $data;
    }

    public function buildAssetList(DirectorySearchQuery $search): array
    {
        $search = $this->toAssetSearchQuery($search);

        $pageSize = max(1, min($search->getPageSize(), AssetSearchQuery::MAX_PAGE_SIZE));
        $page = min(max(1, $search->getPage()), $this->maxBrowsePage($pageSize));
        $search
            ->setChildrenOffset(($page - 1) * $pageSize)
            ->setChildrenLimit($pageSize);

        $data = $this->build($search);

        $items = [];
        foreach ($data['children'] ?? [] as $child) {
            if (is_array($child) && isset($child['uri'])) {
                $items[] = $child;
            }
        }

        return [
            'items' => array_values(array_slice($items, 0, $pageSize)),
            'total' => (int)($data['total'] ?? count($items)),
            'page' => $page,
            'pageSize' => $pageSize,
            'totalIsApproximate' => false,
        ];
    }

    public function buildTree(DirectorySearchQuery $search): array
    {
        $mediaSource = $search->getAsset()->getMediaSource();

        if ($mediaSource instanceof AccessControlEnablerInterface) {
            $mediaSource->enableAccessControl();
        }

        $fetchQuery = (new AssetSearchQuery(
            $search->getAsset(),
            $search->getItemUri(),
            $search->getItemLang(),
            $search->getFilter(),
            1,
            0,
            AssetSearchQuery::CHILDREN_DIRECTORIES_ONLY
        ))
            ->setSortBy($this->resolveSortBy($search))
            ->setSortDir($this->resolveSortDir($search));

        $data = $mediaSource->getDirectories($fetchQuery);

        return $this->stripFileChildrenFromBrowseNode($data, $search);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function stripFileChildrenFromBrowseNode(array $data, DirectorySearchQuery $search): array
    {
        $directories = [];
        foreach ($data['children'] ?? [] as $child) {
            if (is_array($child) && $this->isDirectoryChild($child)) {
                $directories[] = $this->toDirectoryStub($child, $search);
            }
        }
        $data['children'] = $directories;
        unset($data['total'], $data['truncated'], $data['childrenLimit']);

        return $data;
    }

    private function maxBrowsePage(int $pageSize): int
    {
        $pageSize = max(1, $pageSize);

        return max(1, intdiv(self::MAX_CHILDREN_OFFSET, $pageSize) + 1);
    }

    private function clampBrowseOffset(int $requestedOffset, int $pageSize): int
    {
        $pageSize = max(1, $pageSize);
        $maxOffset = ($this->maxBrowsePage($pageSize) - 1) * $pageSize;

        return max(0, min($requestedOffset, $maxOffset));
    }

    private function toAssetSearchQuery(DirectorySearchQuery $search): AssetSearchQuery
    {
        if ($search instanceof AssetSearchQuery) {
            return $search;
        }

        $converted = new AssetSearchQuery(
            $search->getAsset(),
            $search->getItemUri(),
            $search->getItemLang(),
            $search->getFilter(),
            $search->getDepth(),
            $search->getChildrenOffset(),
            $search->getChildrenLimit()
        );

        if ($search->hasQuery()) {
            $converted->setQuery($search->getQuery());
        }

        return $converted
            ->setSortBy($search->getSortBy())
            ->setSortDir($search->getSortDir())
            ->setPage($search->getPage())
            ->setPageSize($search->getPageSize());
    }

    private function resolveBrowsePageSize(DirectorySearchQuery $search): int
    {
        $childrenLimit = $search->getChildrenLimit();
        if (
            $childrenLimit > 0
            && $childrenLimit !== AssetSearchQuery::CHILDREN_DIRECTORIES_ONLY
        ) {
            return min($childrenLimit, AssetSearchQuery::MAX_PAGE_SIZE);
        }

        return $this->getPaginationLimit();
    }

    private function createFetchQuery(DirectorySearchQuery $search): AssetSearchQuery
    {
        // Rebuild with offset 0 and unlimited children so sort+slice sees the full subtree payload.
        return (new AssetSearchQuery(
            $search->getAsset(),
            $search->getItemUri(),
            $search->getItemLang(),
            $search->getFilter(),
            self::BROWSE_SUBTREE_DEPTH,
            0,
            0
        ))
            ->setSortBy($this->resolveSortBy($search))
            ->setSortDir($this->resolveSortDir($search));
    }

    protected function resolveSortBy(DirectorySearchQuery $search): string
    {
        if ($search instanceof AssetSearchQuery) {
            return $search->getSortBy();
        }

        // Legacy DirectorySearchQuery (published tao-core) may not expose sort accessors yet.
        if (method_exists($search, 'getSortBy')) {
            return (string)$search->getSortBy();
        }

        return self::SORT_LABEL;
    }

    protected function resolveSortDir(DirectorySearchQuery $search): string
    {
        if ($search instanceof AssetSearchQuery) {
            return $search->getSortDir();
        }

        if (method_exists($search, 'getSortDir')) {
            return (string)$search->getSortDir();
        }

        return 'asc';
    }

    /**
     * @param array<int, mixed> $nodes
     * @param array<int, array> $files
     */
    private function collectFiles(
        array $nodes,
        string $location,
        array &$files,
        int &$totalFiles
    ): void {
        foreach ($nodes as $child) {
            if (!is_array($child)) {
                continue;
            }

            if ($this->isDirectoryChild($child)) {
                $this->collectFiles(
                    $child['children'] ?? [],
                    $this->childLocation($location, $child),
                    $files,
                    $totalFiles
                );
                continue;
            }

            if ($this->isFileChild($child)) {
                $totalFiles++;
                $files[] = $this->normalizeFile($child, $location);
            }
        }
    }

    private function childLocation(string $parentLocation, array $directory): string
    {
        $label = trim((string)($directory['label'] ?? ''));
        if ($label === '') {
            $path = trim((string)($directory['path'] ?? ''), '/');
            if ($path !== '' && str_contains($path, '/')) {
                $path = substr($path, (int)strrpos($path, '/') + 1);
            }
            $label = $path;
        }

        if ($parentLocation === '') {
            return $label;
        }
        if ($label === '') {
            return $parentLocation;
        }

        return trim($parentLocation . '/' . $label, '/');
    }

    /**
     * Keep a one-level directory stub for tree expand; nested files are flattened above.
     *
     * @param array<string, mixed> $directory
     * @return array<string, mixed>
     */
    protected function toDirectoryStub(array $directory, DirectorySearchQuery $search): array
    {
        $lazyLink = isset($directory['parent']) ? (string)$directory['parent'] : '';
        unset($directory['children'], $directory['parent'], $directory['total']);

        if (!isset($directory['path']) || $directory['path'] === '') {
            if ($lazyLink !== '') {
                $directory['path'] = str_starts_with($lazyLink, self::MEDIA_BROWSER_SCHEME)
                    ? $lazyLink
                    : self::MEDIA_BROWSER_SCHEME . tao_helpers_Uri::encode($lazyLink);
            }
        }

        $browsePath = (string)($directory['path'] ?? '');
        if ($browsePath !== '') {
            $itemContentPath = str_starts_with($browsePath, self::MEDIA_BROWSER_SCHEME)
                ? substr($browsePath, strlen(self::MEDIA_BROWSER_SCHEME))
                : $browsePath;
            $directory['url'] = tao_helpers_Uri::url(
                'files',
                'ItemContent',
                'taoItems',
                [
                    'uri' => $search->getItemUri(),
                    'lang' => $search->getItemLang(),
                    'path' => $itemContentPath,
                ]
            );
        }

        return $directory;
    }

    /**
     * @param array<string, mixed> $file
     * @return array<string, mixed>
     */
    protected function normalizeFile(array $file, string $location): array
    {
        // Keep explicit empty location as missing (nulls-last), same as AssetSearchBuilder.
        $file['location'] = (string)($file['location'] ?? $location);
        $file['updatedAt'] = $this->resolveNormalizedUpdatedAt($file);
        unset($file['updated_at']);

        return $file;
    }

    /**
     * @param array<string, mixed> $file
     */
    private function resolveNormalizedUpdatedAt(array $file): string
    {
        $fromFields = AssetUpdatedAtNormalizer::normalize(
            $file['updatedAt'] ?? $file['updated_at'] ?? null
        );
        if ($fromFields !== null) {
            return $fromFields;
        }

        try {
            return $this->getUpdatedAtResolver()->resolveForAsset($file);
        } catch (\Throwable $exception) {
            return '1970-01-01T00:00:00Z';
        }
    }

    private function getUpdatedAtResolver(): ResourceUpdatedAtResolver
    {
        if ($this->updatedAtResolver !== null) {
            return $this->updatedAtResolver;
        }

        $container = ServiceManager::getServiceManager()->getContainer();
        if (!$container->has(ResourceUpdatedAtResolver::class)) {
            throw new \RuntimeException('ResourceUpdatedAtResolver is not configured');
        }

        $this->updatedAtResolver = $container->get(ResourceUpdatedAtResolver::class);

        return $this->updatedAtResolver;
    }

    protected function isDirectoryChild(array $child): bool
    {
        if (array_key_exists('children', $child) || isset($child['parent'])) {
            return true;
        }

        return isset($child['path']) && !isset($child['mime']) && !isset($child['uri']);
    }

    protected function isFileChild(array $child): bool
    {
        if ($this->isDirectoryChild($child)) {
            return false;
        }

        return isset($child['uri']) || isset($child['mime']) || isset($child['name']);
    }

    /**
     * @param array<int, array> $files
     * @return array<int, array>
     */
    protected function sortFiles(array $files, ?string $sortBy, ?string $sortDir): array
    {
        $field = $sortBy ?: self::SORT_LABEL;
        $direction = $sortDir === 'desc' ? 'desc' : 'asc';
        $nullsLast = in_array($field, [self::SORT_LOCATION, self::SORT_UPDATED_AT], true);

        usort($files, function (array $left, array $right) use ($field, $direction, $nullsLast): int {
            if ($nullsLast) {
                $leftMissing = $this->isMissingSortValue($left, $field);
                $rightMissing = $this->isMissingSortValue($right, $field);
                if ($leftMissing || $rightMissing) {
                    if ($leftMissing && $rightMissing) {
                        $result = 0;
                    } else {
                        // Missing values stay last for both asc and desc.
                        return $leftMissing ? 1 : -1;
                    }
                } else {
                    $result = $this->sortValue($left, $field) <=> $this->sortValue($right, $field);
                }
            } else {
                $result = $this->sortValue($left, $field) <=> $this->sortValue($right, $field);
            }

            if ($result === 0 && $field !== self::SORT_LABEL) {
                $result = $this->sortValue($left, self::SORT_LABEL)
                    <=> $this->sortValue($right, self::SORT_LABEL);
            }
            if ($result === 0) {
                $result = (string)($left['uri'] ?? '') <=> (string)($right['uri'] ?? '');
            }

            return $direction === 'desc' ? -$result : $result;
        });

        return $files;
    }

    private function isMissingSortValue(array $item, string $sortBy): bool
    {
        if ($sortBy === self::SORT_LOCATION) {
            return trim((string)($item['location'] ?? $item['path'] ?? '')) === '';
        }
        if ($sortBy === self::SORT_UPDATED_AT) {
            return !isset($item['updatedAt']) && !isset($item['updated_at']);
        }

        return false;
    }

    private function sortValue(array $item, string $sortBy): string
    {
        if ($sortBy === self::SORT_LOCATION) {
            return mb_strtolower((string)($item['location'] ?? $item['path'] ?? ''), 'UTF-8');
        }
        if ($sortBy === self::SORT_UPDATED_AT) {
            return (string)($item['updatedAt'] ?? $item['updated_at'] ?? '');
        }

        return mb_strtolower((string)($item['label'] ?? $item['name'] ?? ''), 'UTF-8');
    }

    protected function getPaginationLimit(): int
    {
        return (int)$this->getOption(self::OPTION_PAGINATION_LIMIT, self::DEFAULT_PAGINATION_LIMIT);
    }
}

<?php

/**
 * SPDX-FileCopyrightText: 2020-2026 Open Assessment Technologies S.A.
 * Copyright (C) 2026 (original work) Open Assessment Technologies S.A.
 *
 * SPDX-License-Identifier: AGPL-3.0-only OR LicenseRef-TAO-Commercial-License
 */

declare(strict_types=1);

namespace oat\taoItems\model\media;

use oat\oatbox\service\ConfigurableService;
use oat\tao\model\accessControl\AccessControlEnablerInterface;
use oat\tao\model\media\mediaSource\DirectorySearchQuery;
use tao_helpers_Uri;

class AssetTreeBuilder extends ConfigurableService implements AssetTreeBuilderInterface
{
    public const SERVICE_ID = 'taoItems/AssetTreeBuilder';

    public const OPTION_PAGINATION_LIMIT = 'pagination_limit';
    public const DEFAULT_PAGINATION_OFFSET = 0;
    private const DEFAULT_PAGINATION_LIMIT = 15;
    private const SORT_LABEL = 'label';
    private const SORT_LOCATION = 'location';
    private const SORT_UPDATED_AT = 'updatedAt';

    /**
     * Full subtree so browse lists files under the selected folder and descendants.
     * Media sources treat childrenLimit 0 as unlimited; cap the in-memory sort window.
     */
    private const FULL_SUBTREE_DEPTH = PHP_INT_MAX;
    private const MAX_BROWSE_LOAD = 500;

    public function build(DirectorySearchQuery $search): array
    {
        $pageSize = $this->getPaginationLimit();
        $offset = $search->getChildrenOffset();

        $mediaSource = $search->getAsset()->getMediaSource();

        if ($mediaSource instanceof AccessControlEnablerInterface) {
            $mediaSource->enableAccessControl();
        }

        $fetchQuery = $this->createFetchQuery($search);
        $data = $mediaSource->getDirectories($fetchQuery);
        $sourceReportedTotal = array_key_exists('total', $data) ? (int)$data['total'] : null;
        $children = $data['children'] ?? [];

        $scopeLabel = (string)($data['label'] ?? $data['path'] ?? '');
        $directories = [];
        $files = [];
        $totalFiles = 0;

        foreach ($children as $child) {
            if (!is_array($child)) {
                continue;
            }

            if ($this->isFileChild($child)) {
                $totalFiles++;
                if (count($files) < self::MAX_BROWSE_LOAD) {
                    $files[] = $this->normalizeFile($child, $scopeLabel);
                }
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
        // Prefer source recursive total when present (e.g. LocalItemSource beyond payload cap).
        $data['total'] = $sourceReportedTotal !== null
            ? max($sourceReportedTotal, $totalFiles)
            : $totalFiles;
        $data['childrenLimit'] = $pageSize;
        $data['children'] = array_merge($directories, array_slice($files, $offset, $pageSize));

        return $data;
    }

    private function createFetchQuery(DirectorySearchQuery $search): AssetSearchQuery
    {
        // Rebuild with offset 0 so media sources do not paginate before we sort+slice.
        // DirectorySearchQuery has no setChildrenOffset; AssetSearchQuery carries the bound.
        return (new AssetSearchQuery(
            $search->getAsset(),
            $search->getItemUri(),
            $search->getItemLang(),
            $search->getFilter(),
            self::FULL_SUBTREE_DEPTH,
            0,
            self::MAX_BROWSE_LOAD
        ))
            ->setSortBy($this->resolveSortBy($search))
            ->setSortDir($this->resolveSortDir($search));
    }

    private function resolveSortBy(DirectorySearchQuery $search): string
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

    private function resolveSortDir(DirectorySearchQuery $search): string
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
    private function collectFiles(array $nodes, string $location, array &$files, int &$totalFiles): void
    {
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
                if (count($files) < self::MAX_BROWSE_LOAD) {
                    $files[] = $this->normalizeFile($child, $location);
                }
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
    private function toDirectoryStub(array $directory, DirectorySearchQuery $search): array
    {
        $parent = (string)($directory['parent'] ?? $directory['path'] ?? '');
        unset($directory['children'], $directory['parent'], $directory['total']);

        if ($parent !== '') {
            $directory['url'] = tao_helpers_Uri::url(
                'files',
                'ItemContent',
                'taoItems',
                [
                    'uri' => $search->getItemUri(),
                    'lang' => $search->getItemLang(),
                    '1' => $parent,
                ]
            );
            if (!isset($directory['path'])) {
                $directory['path'] = $parent;
            }
        }

        return $directory;
    }

    /**
     * @param array<string, mixed> $file
     * @return array<string, mixed>
     */
    private function normalizeFile(array $file, string $location): array
    {
        // Keep explicit empty location as missing (nulls-last), same as AssetSearchBuilder.
        $file['location'] = (string)($file['location'] ?? $location);
        if (!isset($file['updatedAt']) && isset($file['updated_at'])) {
            $file['updatedAt'] = $file['updated_at'];
        }

        return $file;
    }

    private function isDirectoryChild(array $child): bool
    {
        if (array_key_exists('children', $child) || isset($child['parent'])) {
            return true;
        }

        return isset($child['path']) && !isset($child['mime']) && !isset($child['uri']);
    }

    private function isFileChild(array $child): bool
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
    private function sortFiles(array $files, ?string $sortBy, ?string $sortDir): array
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

    private function getPaginationLimit(): int
    {
        return (int)$this->getOption(self::OPTION_PAGINATION_LIMIT, self::DEFAULT_PAGINATION_LIMIT);
    }
}

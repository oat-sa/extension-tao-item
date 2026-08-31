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
 * Copyright (c) 2020-2021 (original work) Open Assessment Technologies SA;
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
        $children = $data['children'] ?? [];

        $scopeLabel = (string)($data['label'] ?? $data['path'] ?? '');
        $directories = [];
        $files = [];

        foreach ($children as $child) {
            if (!is_array($child)) {
                continue;
            }

            if ($this->isFileChild($child)) {
                if (count($files) < self::MAX_BROWSE_LOAD) {
                    $files[] = $this->normalizeFile($child, $scopeLabel);
                }
                continue;
            }

            // Directories and other non-file nodes stay as stubs for tree expand.
            $directories[] = $this->toDirectoryStub($child, $search);
            if (count($files) < self::MAX_BROWSE_LOAD) {
                $this->collectFiles(
                    $child['children'] ?? [],
                    $this->childLocation($scopeLabel, $child),
                    $files
                );
            }
        }
        $files = $this->sortFiles($files, $search->getSortBy(), $search->getSortDir());
        $data['total'] = count($files);
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
            ->setSortBy($search->getSortBy())
            ->setSortDir($search->getSortDir());
    }

    /**
     * @param array<int, mixed> $nodes
     * @param array<int, array> $files
     */
    private function collectFiles(array $nodes, string $location, array &$files): void
    {
        foreach ($nodes as $child) {
            if (count($files) >= self::MAX_BROWSE_LOAD) {
                return;
            }
            if (!is_array($child)) {
                continue;
            }

            if ($this->isDirectoryChild($child)) {
                $this->collectFiles(
                    $child['children'] ?? [],
                    $this->childLocation($location, $child),
                    $files
                );
                continue;
            }

            if ($this->isFileChild($child)) {
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
        if (!isset($file['location']) || $file['location'] === '') {
            $file['location'] = $location;
        }
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

        usort($files, function (array $left, array $right) use ($field, $direction): int {
            $result = $this->sortValue($left, $field) <=> $this->sortValue($right, $field);
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

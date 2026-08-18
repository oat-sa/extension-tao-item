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

use oat\oatbox\service\ConfigurableService;
use oat\tao\model\accessControl\AccessControlEnablerInterface;
use oat\tao\model\media\mediaSource\DirectorySearchQuery;

/**
 * Builds Resource Manager search payloads from the existing media browse sources.
 *
 * When a text query is present, results are collected from the requested folder
 * subtree, filtered with trailing-token prefix matching, then sorted and paginated.
 */
class AssetSearchBuilder extends ConfigurableService
{
    public const SERVICE_ID = 'taoItems/AssetSearchBuilder';

    private const MAX_SEARCH_DEPTH = 32;

    public function search(DirectorySearchQuery $search): array
    {
        $asset = $search->getAsset();
        $mediaSource = $asset->getMediaSource();

        if ($mediaSource instanceof AccessControlEnablerInterface) {
            $mediaSource->enableAccessControl();
        }

        $search
            ->setDepth(self::MAX_SEARCH_DEPTH)
            ->setChildrenLimit(0);

        $tree = $mediaSource->getDirectories($search);
        $scopePath = (string)($tree['path'] ?? $search->getParentLink());
        $scopeLabel = (string)($tree['label'] ?? $scopePath);

        $items = $this->flattenAssets($tree, $scopePath, $scopeLabel);
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
        ];
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
                $childLabel = (string)($child['label'] ?? $child['path'] ?? '');
                $childLocation = trim($currentLocation . '/' . $childLabel, '/');
                $items = array_merge(
                    $items,
                    $this->flattenAssets($child, $scopePath, $scopeLabel, $childLocation)
                );
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
        if (!isset($normalized['updatedAt']) && isset($asset['updated_at'])) {
            $normalized['updatedAt'] = $asset['updated_at'];
        }

        return $normalized;
    }

    /**
     * @param array<int, array> $items
     * @return array<int, array>
     */
    private function filterByQuery(array $items, string $query): array
    {
        $queryTokens = $this->tokenize($query);
        if ($queryTokens === []) {
            return $items;
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
                        $item['uri'] ?? '',
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
        usort($items, function (array $left, array $right) use ($sortBy, $sortDir): int {
            $leftValue = $this->sortValue($left, $sortBy);
            $rightValue = $this->sortValue($right, $sortBy);
            $result = $leftValue <=> $rightValue;
            return $sortDir === 'desc' ? -$result : $result;
        });

        return $items;
    }

    private function sortValue(array $item, string $sortBy): string
    {
        if ($sortBy === DirectorySearchQuery::SORT_LOCATION) {
            return strtolower((string)($item['location'] ?? ''));
        }
        if ($sortBy === DirectorySearchQuery::SORT_UPDATED_AT) {
            return (string)($item['updatedAt'] ?? $item['updated_at'] ?? '');
        }

        return strtolower((string)($item['label'] ?? $item['name'] ?? ''));
    }
}

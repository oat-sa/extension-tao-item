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

    public function build(DirectorySearchQuery $search): array
    {
        $asset = $search->getAsset();
        $pageSize = $this->getPaginationLimit();
        $offset = $search->getChildrenOffset();

        // Load the full folder so files can be sorted before pagination.
        $search->setChildrenLimit(0);

        $mediaSource = $asset->getMediaSource();

        if ($mediaSource instanceof AccessControlEnablerInterface) {
            $mediaSource->enableAccessControl();
        }

        $data = $mediaSource->getDirectories($search);
        $children = $data['children'] ?? [];

        foreach ($children as &$child) {
            if (isset($child['parent'])) {
                $child['url'] = tao_helpers_Uri::url(
                    'files',
                    'ItemContent',
                    'taoItems',
                    [
                        'uri' => $search->getItemUri(),
                        'lang' => $search->getItemLang(),
                        '1' => $child['parent']
                    ]
                );

                unset($child['parent']);
            }
        }
        unset($child);

        $directories = [];
        $files = [];
        foreach ($children as $child) {
            if ($this->isFileChild($child)) {
                $files[] = $child;
            } else {
                $directories[] = $child;
            }
        }

        $files = $this->sortFiles($files, $search->getSortBy(), $search->getSortDir());
        $data['total'] = count($files);
        $data['childrenLimit'] = $pageSize;
        $data['children'] = array_merge($directories, array_slice($files, $offset, $pageSize));

        return $data;
    }

    private function isFileChild(array $child): bool
    {
        return isset($child['uri']) || isset($child['mime']) || isset($child['name']);
    }

    /**
     * @param array<int, array> $files
     * @return array<int, array>
     */
    private function sortFiles(array $files, ?string $sortBy, ?string $sortDir): array
    {
        $field = $sortBy ?: DirectorySearchQuery::SORT_LABEL;
        $direction = $sortDir === 'desc' ? 'desc' : 'asc';

        usort($files, function (array $left, array $right) use ($field, $direction): int {
            $result = $this->sortValue($left, $field) <=> $this->sortValue($right, $field);
            if ($result === 0 && $field !== DirectorySearchQuery::SORT_LABEL) {
                $result = $this->sortValue($left, DirectorySearchQuery::SORT_LABEL)
                    <=> $this->sortValue($right, DirectorySearchQuery::SORT_LABEL);
            }

            return $direction === 'desc' ? -$result : $result;
        });

        return $files;
    }

    private function sortValue(array $item, string $sortBy): string
    {
        if ($sortBy === DirectorySearchQuery::SORT_LOCATION) {
            return strtolower((string)($item['location'] ?? $item['path'] ?? ''));
        }
        if ($sortBy === DirectorySearchQuery::SORT_UPDATED_AT) {
            return (string)($item['updatedAt'] ?? $item['updated_at'] ?? '');
        }

        return strtolower((string)($item['label'] ?? $item['name'] ?? ''));
    }

    private function getPaginationLimit(): int
    {
        return (int)$this->getOption(self::OPTION_PAGINATION_LIMIT, self::DEFAULT_PAGINATION_LIMIT);
    }
}

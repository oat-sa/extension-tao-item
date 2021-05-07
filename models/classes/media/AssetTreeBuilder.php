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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\media;

use oat\oatbox\service\ConfigurableService;
use oat\tao\model\accessControl\AccessControlEnablerInterface;
use oat\tao\model\media\mediaSource\DirectorySearchQuery;
use tao_helpers_Uri;

class AssetTreeBuilder extends ConfigurableService
{
    public const SERVICE_ID = 'taoItems/AssetTreeBuilder';

    public const OPTION_PAGINATION_LIMIT = 'pagination_limit';
    public const DEFAULT_PAGINATION_OFFSET = 0;
    private const DEFAULT_PAGINATION_LIMIT = 15;

    public function build(DirectorySearchQuery $search): array
    {
        $asset = $search->getAsset();

        $search->setChildrenLimit($this->getPaginationLimit());

        $mediaSource = $asset->getMediaSource();

        if ($mediaSource instanceof AccessControlEnablerInterface) {
            $mediaSource->enableAccessControl();
        }

        $data = $mediaSource->getDirectories($search);

        foreach ($data['children'] as &$child) {
            if (isset($child['parent'])) {
                $child['url'] = tao_helpers_Uri::url(
                    'files',
                    'ItemContent',
                    'taoItems',
                    ['uri' => $search->getItemUri(), 'lang' => $search->getItemLang(), '1' => $child['parent']]
                );
                unset($child['parent']);
            }
        }
        return $data;
    }

    private function getPaginationLimit(): int
    {
        return (int)$this->getOption(self::OPTION_PAGINATION_LIMIT, self::DEFAULT_PAGINATION_LIMIT);
    }
}

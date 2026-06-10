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
 * Copyright (c) 2024 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\taoItems\scripts\install;

use oat\oatbox\extension\InstallAction;
use oat\tao\model\menu\SectionVisibilityFilter;

class SetupSectionVisibilityFilters extends InstallAction
{
    private const FEATURE_FLAG_TRANSLATION = 'FEATURE_FLAG_TRANSLATION_ENABLED';
    private const FEATURE_FLAG_MANAGE_ITEMS = 'FEATURE_FLAG_MANAGE_ITEMS';

    public function __invoke($params): void
    {
        /** @var SectionVisibilityFilter $sectionVisibilityFilter */
        $sectionVisibilityFilter = $this->getServiceManager()->get(SectionVisibilityFilter::SERVICE_ID);

        $sectionVisibilityFilter->showSectionByFeatureFlag(
            $sectionVisibilityFilter->createSectionPath(
                [
                    'manage_items',
                    'item-translate',
                ]
            ),
            self::FEATURE_FLAG_TRANSLATION
        );

        foreach (['item-usage', 'item-statistics'] as $actionId) {
            $sectionVisibilityFilter->showSectionByFeatureFlag(
                $sectionVisibilityFilter->createSectionPath(
                    [
                        'manage_items',
                        $actionId,
                    ]
                ),
                self::FEATURE_FLAG_MANAGE_ITEMS
            );
        }

        $this->getServiceManager()->register(SectionVisibilityFilter::SERVICE_ID, $sectionVisibilityFilter);
    }
}

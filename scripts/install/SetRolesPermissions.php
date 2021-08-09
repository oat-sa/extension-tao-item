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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\scripts\install;

use taoItems_actions_Items;
use taoItems_actions_ItemContent;
use taoItems_actions_ItemImport;
use oat\oatbox\extension\InstallAction;
use oat\taoItems\model\user\TaoItemsRoles;
use oat\tao\model\accessControl\ActionAccessControl;
use oat\tao\scripts\tools\accessControl\SetRolesAccess;

class SetRolesPermissions extends InstallAction
{
    private const CONFIG = [
        SetRolesAccess::CONFIG_PERMISSIONS => [
            taoItems_actions_Items::class => [
                'editClassLabel' => [
                    TaoItemsRoles::ITEM_CLASS_NAVIGATOR => ActionAccessControl::READ,
                    TaoItemsRoles::ITEM_CLASS_EDITOR => ActionAccessControl::WRITE,
                ],
                'editItem' => [
                    TaoItemsRoles::ITEM_VIEWER => ActionAccessControl::READ,
                    TaoItemsRoles::ITEM_PROPERTIES_EDITOR => ActionAccessControl::WRITE,
                ],
            ],
            taoItems_actions_ItemContent::class => [
                'files' => [
                    TaoItemsRoles::ITEM_CLASS_NAVIGATOR => ActionAccessControl::DENY,
                ],
                'delete' => [
                    TaoItemsRoles::ITEM_CLASS_NAVIGATOR => ActionAccessControl::DENY,
                ],
                'upload' => [
                    TaoItemsRoles::ITEM_CLASS_NAVIGATOR => ActionAccessControl::DENY,
                ],
            ],
        ],
    ];

    public function __invoke($params = [])
    {
        $setRolesAccess = $this->propagate(new SetRolesAccess());
        $setRolesAccess([
            '--' . SetRolesAccess::OPTION_CONFIG, self::CONFIG,
        ]);
    }
}

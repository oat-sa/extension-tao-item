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

namespace oat\taoItems\migrations;

use taoItems_actions_Items;
use Doctrine\DBAL\Schema\Schema;
use oat\taoItems\model\user\TaoItemsRoles;
use oat\tao\scripts\update\OntologyUpdater;
use oat\tao\model\accessControl\ActionAccessControl;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\tao\scripts\tools\accessControl\SetRolesAccess;

final class Version202106220802152141_taoItems extends AbstractMigration
{
    private const CONFIG = [
        SetRolesAccess::CONFIG_RULES => [
            TaoItemsRoles::ITEM_CLASS_SCHEMA_MANAGER => [
                ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'editItemClass'],
                ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'addClassProperty'],
                ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'removeClassProperty'],
                ['ext' => 'taoItems', 'mod' => 'Category', 'act' => 'getExposedsByClass'],
                ['ext' => 'taoItems', 'mod' => 'Category', 'act' => 'setExposed'],
            ],
            TaoItemsRoles::ITEM_VIEWER => [
                ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'editItem'],
            ],
            TaoItemsRoles::ITEM_MANAGER => [
                ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'cloneInstance'],
                ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'copyInstance'],
            ],
            TaoItemsRoles::ITEM_PREVIEWER => [
                ['ext' => 'taoItems', 'mod' => 'ItemPreview', 'act' => 'index'],
            ],
        ],
        SetRolesAccess::CONFIG_PERMISSIONS => [
            taoItems_actions_Items::class => [
                'editItem' => [
                    TaoItemsRoles::ITEM_VIEWER => ActionAccessControl::READ,
                    TaoItemsRoles::ITEM_PROPERTIES_EDITOR => ActionAccessControl::WRITE,
                ],
            ],
        ],
    ];

    public function getDescription(): string
    {
        return 'Create new item management roles and assign permissions to them';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();

        $setRolesAccess = $this->propagate(new SetRolesAccess());
        $setRolesAccess([
            '--' . SetRolesAccess::OPTION_CONFIG, self::CONFIG,
        ]);
    }

    public function down(Schema $schema): void
    {
        $setRolesAccess = $this->propagate(new SetRolesAccess());
        $setRolesAccess([
            '--' . SetRolesAccess::OPTION_REVOKE,
            '--' . SetRolesAccess::OPTION_CONFIG, self::CONFIG,
        ]);
    }
}

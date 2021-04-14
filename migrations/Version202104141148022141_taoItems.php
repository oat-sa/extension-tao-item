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

use oat\tao\model\TaoOntology;
use Doctrine\DBAL\Schema\Schema;
use oat\taoItems\model\user\TaoItemsRoles;
use oat\taoDacSimple\scripts\tools\AssignPermissions;
use oat\tao\scripts\tools\migrations\AbstractMigration;

final class Version202104141148022141_taoItems extends AbstractMigration
{
    private const PERMISSIONS = [
        TaoItemsRoles::ITEM_CLASS_NAVIGATOR => ['READ'],
        TaoItemsRoles::ITEM_CLASS_EDITOR => ['WRITE'],
    ];

    public function getDescription(): string
    {
        return 'Add permissions to Item Class roles.';
    }

    public function up(Schema $schema): void
    {
        $assignPermissionsAction = $this->propagate(new AssignPermissions());
        $report = $assignPermissionsAction([
            '--' . AssignPermissions::OPTION_CLASS, TaoOntology::CLASS_URI_ITEM,
            '--' . AssignPermissions::OPTION_PERMISSIONS, self::PERMISSIONS,
            '--' . AssignPermissions::OPTION_RECURSIVE,
        ]);

        $this->addReport($report);
    }

    public function down(Schema $schema): void
    {
        $assignPermissionsAction = $this->propagate(new AssignPermissions());
        $report = $assignPermissionsAction([
            '--' . AssignPermissions::OPTION_CLASS, TaoOntology::CLASS_URI_ITEM,
            '--' . AssignPermissions::OPTION_PERMISSIONS, self::PERMISSIONS,
            '--' . AssignPermissions::OPTION_RECURSIVE,
            '--' . AssignPermissions::OPTION_REVOKE,
        ]);

        $this->addReport($report);
    }
}

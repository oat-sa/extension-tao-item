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

use Doctrine\DBAL\Schema\Schema;
use taoItems_actions_ItemContent;
use oat\taoItems\model\user\TaoItemsRoles;
use oat\tao\scripts\update\OntologyUpdater;
use oat\tao\model\accessControl\ActionAccessControl;
use oat\tao\scripts\tools\accessControl\SetRolesAccess;
use oat\tao\scripts\tools\migrations\AbstractMigration;

final class Version202108250549242141_taoItems extends AbstractMigration
{
    private const CONFIG = [
        SetRolesAccess::CONFIG_PERMISSIONS => [
            taoItems_actions_ItemContent::class => [
                'viewAsset' => [
                    TaoItemsRoles::ITEM_CLASS_NAVIGATOR => ActionAccessControl::DENY,
                ],
                'previewAsset' => [
                    TaoItemsRoles::ITEM_CLASS_NAVIGATOR => ActionAccessControl::DENY,
                ],
                'downloadAsset' => [
                    TaoItemsRoles::ITEM_CLASS_NAVIGATOR => ActionAccessControl::DENY,
                ],
                'uploadAsset' => [
                    TaoItemsRoles::ITEM_CLASS_NAVIGATOR => ActionAccessControl::DENY,
                ],
                'deleteAsset' => [
                    TaoItemsRoles::ITEM_CLASS_NAVIGATOR => ActionAccessControl::DENY,
                ],
            ],
        ],
    ];

    private const REVOKE_CONFIG = [
        SetRolesAccess::CONFIG_PERMISSIONS => [
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

    public function getDescription(): string
    {
        return 'Configure asset permissions for Item Content creator role.';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();

        $setRolesAccess = $this->propagate(new SetRolesAccess());
        $setRolesAccess([
            '--' . SetRolesAccess::OPTION_REVOKE,
            '--' . SetRolesAccess::OPTION_CONFIG, self::REVOKE_CONFIG,
        ]);
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
        $setRolesAccess = $this->propagate(new SetRolesAccess());
        $setRolesAccess([
            '--' . SetRolesAccess::OPTION_CONFIG, self::REVOKE_CONFIG,
        ]);
    }
}

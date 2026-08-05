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
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\accessControl\SetRolesAccess;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoItems\model\user\TaoItemsRoles;

/**
 * Item Comments (NYSED-13): grant RestItemComments ACL.
 *
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202608051012062141_taoItems extends AbstractMigration
{
    private const GLOBAL_MANAGER_ROLE = 'http://www.tao.lu/Ontologies/TAO.rdf#GlobalManagerRole';

    private const REST_ITEM_COMMENTS_MASK = [
        'ext' => 'taoItems',
        'mod' => 'RestItemComments',
    ];

    private const CONFIG = [
        SetRolesAccess::CONFIG_RULES => [
            TaoItemsRoles::ITEM_AUTHOR_ABSTRACT => [
                self::REST_ITEM_COMMENTS_MASK,
            ],
            TaoItemsRoles::ITEM_MANAGER => [
                self::REST_ITEM_COMMENTS_MASK,
            ],
            TaoItemsRoles::ITEM_CONTENT_CREATOR => [
                self::REST_ITEM_COMMENTS_MASK,
            ],
            TaoItemsRoles::ITEM_AUTHOR => [
                self::REST_ITEM_COMMENTS_MASK,
            ],
            TaoItemsRoles::ITEM_VIEWER => [
                self::REST_ITEM_COMMENTS_MASK,
            ],
            self::GLOBAL_MANAGER_ROLE => [
                self::REST_ITEM_COMMENTS_MASK,
            ],
        ],
    ];

    public function getDescription(): string
    {
        return 'Grant RestItemComments ACL for authoring roles (NYSED-13)';
    }

    public function up(Schema $schema): void
    {
        $this->runAction(new SetRolesAccess(), [
            '--' . SetRolesAccess::OPTION_CONFIG,
            self::CONFIG,
        ]);
    }

    public function down(Schema $schema): void
    {
        $this->runAction(new SetRolesAccess(), [
            '--' . SetRolesAccess::OPTION_REVOKE,
            '--' . SetRolesAccess::OPTION_CONFIG,
            self::CONFIG,
        ]);
    }
}

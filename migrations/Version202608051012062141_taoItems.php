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
use oat\oatbox\reporting\Report;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\tao\scripts\update\OntologyUpdater;
use oat\taoItems\model\user\TaoItemsRoles;

/**
 * Item Comments (NYSED-13): sync ontology and grant RestItemComments ACL.
 *
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202608051012062141_taoItems extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Sync Item Comment ontology and grant RestItemComments ACL (NYSED-13)';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();

        foreach ($this->getRules() as $rule) {
            AclProxy::applyRule($rule);
        }

        $this->addReport(
            Report::createSuccess('Item Comment ontology synced and RestItemComments ACL applied')
        );
    }

    public function down(Schema $schema): void
    {
        foreach ($this->getRules() as $rule) {
            AclProxy::revokeRule($rule);
        }

        $this->addReport(Report::createSuccess('Revoked RestItemComments ACL rules'));
    }

    /**
     * @return AccessRule[]
     */
    private function getRules(): array
    {
        $mask = [
            'ext' => 'taoItems',
            'mod' => 'RestItemComments',
        ];

        return [
            new AccessRule(AccessRule::GRANT, TaoItemsRoles::ITEM_AUTHOR_ABSTRACT, $mask),
            new AccessRule(AccessRule::GRANT, TaoItemsRoles::ITEM_MANAGER, $mask),
            new AccessRule(AccessRule::GRANT, TaoItemsRoles::ITEM_CONTENT_CREATOR, $mask),
            new AccessRule(AccessRule::GRANT, TaoItemsRoles::ITEM_AUTHOR, $mask),
            new AccessRule(AccessRule::GRANT, TaoItemsRoles::ITEM_VIEWER, $mask),
            new AccessRule(
                AccessRule::GRANT,
                'http://www.tao.lu/Ontologies/TAO.rdf#GlobalManagerRole',
                $mask
            ),
        ];
    }
}

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
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\tao\scripts\update\OntologyUpdater;

/**
 * Sync Item Comment ontology properties edited / resolved (NYSED-19).
 *
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202608051225002141_taoItems extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Sync Item Comment edited/resolved ontology properties (NYSED-19)';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();

        $this->addReport(
            Report::createSuccess('Item Comment ontology synced (edited/resolved properties)')
        );
    }

    public function down(Schema $schema): void
    {
        // Ontology property removal is not reversed automatically.
    }
}

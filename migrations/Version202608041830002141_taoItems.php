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

/**
 * Removes legacy ServiceManager *.conf.php wiring for Item Comments (DI replaces it).
 *
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202608041830002141_taoItems extends AbstractMigration
{
    private const LEGACY_SERVICE_IDS = [
        'taoItems/ItemCommentPersistence',
        'taoItems/ItemCommentService',
        'taoItems/RdfItemCommentAdapter',
    ];

    public function getDescription(): string
    {
        return 'Unregister legacy Item Comment ConfigurableService configs (NYSED-19 DI)';
    }

    public function up(Schema $schema): void
    {
        $serviceManager = $this->getServiceManager();
        foreach (self::LEGACY_SERVICE_IDS as $serviceId) {
            if ($serviceManager->has($serviceId)) {
                $serviceManager->unregister($serviceId);
            }
        }

        $this->addReport(
            Report::createSuccess('Legacy Item Comment ServiceManager configs unregistered')
        );
    }

    public function down(Schema $schema): void
    {
        // Intentionally left empty: DI is the only supported wiring.
    }
}

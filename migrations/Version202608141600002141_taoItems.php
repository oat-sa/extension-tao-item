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
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA.
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\reporting\Report;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoItems\model\media\AssetSearchBuilder;

/**
 * Clears legacy oatbox registration of AssetSearchBuilder.
 *
 * AssetSearchBuilder is constructed in ItemContent with an optional indexed
 * gateway from Symfony DI; ServiceManager::register() requires ConfigurableService.
 *
 * @license GPL-2.0-only
 * @copyright 2026 Open Assessment Technologies SA
 *
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202608141600002141_taoItems extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Unregister legacy oatbox AssetSearchBuilder (constructed via ItemContent DI)';
    }

    public function up(Schema $schema): void
    {
        $serviceManager = $this->getServiceManager();
        if ($serviceManager->has(AssetSearchBuilder::SERVICE_ID)) {
            $serviceManager->unregister(AssetSearchBuilder::SERVICE_ID);
        }

        $this->addReport(Report::createSuccess('Legacy AssetSearchBuilder oatbox registration cleared'));
    }

    public function down(Schema $schema): void
    {
        $this->addReport(Report::createInfo(
            'AssetSearchBuilder is not restored to oatbox; ItemContent constructs it with Symfony gateway DI'
        ));
    }
}

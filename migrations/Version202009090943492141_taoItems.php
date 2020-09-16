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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use oat\taoItems\model\preview\ItemPreviewerRegistryServiceInterface;
use oat\taoItems\scripts\install\RegisterItemPreviewerRegistryService;

/**
 * Class Version202009090943492141_taoItems
 *
 * @package oat\taoItems\migrations
 */
final class Version202009090943492141_taoItems extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Register ' . ItemPreviewerRegistryServiceInterface::class;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->propagate(new RegisterItemPreviewerRegistryService())([]);
    }

    /**
     * @param Schema $schema
     *
     * @throws InvalidServiceManagerException
     */
    public function down(Schema $schema): void
    {
        $this->getServiceManager()->unregister(ItemPreviewerRegistryServiceInterface::SERVICE_ID);
    }
}

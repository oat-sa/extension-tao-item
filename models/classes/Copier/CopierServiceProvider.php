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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA.
 *
 * @author Andrei Shapiro <andrei.shapiro@taotesting.com>
 */

declare(strict_types=1);

namespace oat\taoItems\model\Copier;

use oat\generis\model\data\Ontology;
use oat\tao\model\resources\Service\InstanceCopierProxy;
use oat\tao\model\TaoOntology;
use oat\oatbox\event\EventManager;
use oat\taoItems\model\TaoItemOntology;
use taoItems_models_classes_ItemsService;
use oat\tao\model\resources\Service\ClassCopier;
use oat\tao\model\resources\Service\InstanceCopier;
use oat\tao\model\resources\Service\ClassCopierProxy;
use oat\tao\model\resources\Service\ClassMetadataCopier;
use oat\tao\model\resources\Service\ClassMetadataMapper;
use oat\tao\model\resources\Service\InstanceMetadataCopier;
use oat\tao\model\resources\Service\RootClassesListService;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

/**
 * @codeCoverageIgnore
 */
class CopierServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services
            ->set(taoItems_models_classes_ItemsService::class, taoItems_models_classes_ItemsService::class)
            ->factory(taoItems_models_classes_ItemsService::class . '::singleton');

        $services
            ->get(InstanceMetadataCopier::class)
            ->call(
                'addPropertyUriToBlacklist',
                [
                    TaoItemOntology::PROPERTY_ITEM_CONTENT
                ]
            );

        $services
            ->set(ItemContentCopier::class, ItemContentCopier::class)
            ->args(
                [
                    service(FileReferenceSerializer::SERVICE_ID),
                    service(taoItems_models_classes_ItemsService::class),
                    service(EventManager::SERVICE_ID),
                ]
            );

        $services
            ->set(InstanceCopier::class . '::ITEMS', InstanceCopier::class)
            ->args(
                [
                    service(InstanceMetadataCopier::class),
                    service(Ontology::SERVICE_ID)
                ]
            )
            ->call(
                'withInstanceContentCopier',
                [
                    service(ItemContentCopier::class),
                ]
            )
            ->call(
                'withPermissionCopiers',
                [
                    tagged_iterator('tao.copier.permissions'),
                ]
            )
            ->call(
                'withEventManager',
                [
                    service(EventManager::class),
                ]
            );

        $services
            ->set(ClassCopier::class . '::ITEMS', ClassCopier::class)
            ->share(false)
            ->args(
                [
                    service(RootClassesListService::class),
                    service(ClassMetadataCopier::class),
                    service(InstanceCopier::class . '::ITEMS'),
                    service(ClassMetadataMapper::class),
                    service(Ontology::SERVICE_ID),
                ]
            )
            ->call(
                'withPermissionCopiers',
                [
                    tagged_iterator('tao.copier.permissions'),
                ]
            );

        $services
            ->set(ItemClassCopier::class, ItemClassCopier::class)
            ->share(false)
            ->args(
                [
                    service(ClassCopier::class . '::ITEMS'),
                    service(Ontology::SERVICE_ID),
                ]
            );

        $services
            ->get(ClassCopierProxy::class)
            ->call(
                'addClassCopier',
                [
                    TaoOntology::CLASS_URI_ITEM,
                    service(ItemClassCopier::class),
                ]
            );

        $services
            ->get(InstanceCopierProxy::class)
            ->call(
                'addInstanceCopier',
                [
                    TaoOntology::CLASS_URI_ITEM,
                    service(InstanceCopier::class . '::ITEMS'),
                ]
            );
    }
}

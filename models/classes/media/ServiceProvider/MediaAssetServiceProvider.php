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

namespace oat\taoItems\model\media\ServiceProvider;

use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use oat\oatbox\filesystem\FileSystemService;
use oat\tao\model\accessControl\ActionAccessControl;
use oat\tao\model\accessControl\PermissionChecker;
use oat\taoMediaManager\model\fileManagement\FileManagement;
use oat\taoMediaManager\model\fileManagement\FileSourceUnserializer;
use oat\taoItems\model\media\AssetFilesService;
use oat\taoItems\model\media\AssetIndexedSearchGatewayInterface;
use oat\taoItems\model\media\AssetListingReadAccessChecker;
use oat\taoItems\model\media\AssetSearchBuilder;
use oat\taoItems\model\media\AssetTreeBuilder;
use oat\taoItems\model\media\CurrentAssetResolver;
use oat\taoItems\model\media\NoOpAssetIndexedSearchGateway;
use oat\taoItems\model\media\AssetUpdatedAtResolverInterface;
use oat\taoItems\model\media\ResourceUpdatedAtResolver;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class MediaAssetServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services
            ->set(NoOpAssetIndexedSearchGateway::class, NoOpAssetIndexedSearchGateway::class);

        $services
            ->alias(AssetIndexedSearchGatewayInterface::class, NoOpAssetIndexedSearchGateway::class)
            ->public();

        $services
            ->set(AssetListingReadAccessChecker::class, AssetListingReadAccessChecker::class)
            ->args([
                service(PermissionChecker::class),
                service(ActionAccessControl::SERVICE_ID),
            ]);

        $services
            ->set(CurrentAssetResolver::class, CurrentAssetResolver::class)
            ->args([
                service(PermissionChecker::class),
            ]);

        $services
            ->set(ResourceUpdatedAtResolver::class, ResourceUpdatedAtResolver::class)
            ->args([
                service(FileManagement::SERVICE_ID),
                service(FileSourceUnserializer::class),
                service(FileSystemService::SERVICE_ID),
            ]);

        $services->alias(AssetUpdatedAtResolverInterface::class, ResourceUpdatedAtResolver::class);

        $services
            ->set(AssetSearchBuilder::class, AssetSearchBuilder::class)
            ->args([
                service(AssetIndexedSearchGatewayInterface::class),
                service(AssetListingReadAccessChecker::class),
                service(AssetUpdatedAtResolverInterface::class),
            ]);

        $services
            ->set(AssetFilesService::class, AssetFilesService::class)
            ->public()
            ->args([
                service(AssetSearchBuilder::class),
                service(AssetTreeBuilder::SERVICE_ID),
                service(CurrentAssetResolver::class),
            ]);
    }
}

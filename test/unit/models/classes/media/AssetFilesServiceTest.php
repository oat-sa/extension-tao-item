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

namespace oat\taoItems\test\unit\models\classes\media;

use oat\generis\test\TestCase;
use oat\tao\model\accessControl\PermissionCheckerInterface;
use oat\tao\model\media\MediaAsset;
use oat\taoItems\model\media\AssetFilesService;
use oat\taoItems\model\media\AssetSearchBuilder;
use oat\taoItems\model\media\AssetSearchQuery;
use oat\taoItems\model\media\AssetTreeBuilderInterface;
use oat\taoItems\model\media\CurrentAssetResolver;

class AssetFilesServiceTest extends TestCase
{
    public function testListFilesUsesSearchWhenQueryPresent(): void
    {
        $asset = $this->createMock(MediaAsset::class);
        $searchResult = [
            'items' => [['uri' => 'a']],
            'total' => 1,
            'page' => 1,
            'pageSize' => 10,
        ];

        $searchBuilder = $this->createMock(AssetSearchBuilder::class);
        $searchBuilder->expects($this->once())
            ->method('search')
            ->with($this->callback(static function (AssetSearchQuery $query): bool {
                return $query->getQuery() === 'color';
            }))
            ->willReturn($searchResult);

        $treeBuilder = $this->createMock(AssetTreeBuilderInterface::class);
        $treeBuilder->expects($this->never())->method('build');

        $service = new AssetFilesService(
            $searchBuilder,
            $treeBuilder,
            new CurrentAssetResolver($this->createMock(PermissionCheckerInterface::class))
        );
        $result = $service->listFiles($asset, 'item-uri', 'en-US', ['query' => 'color'], []);

        $this->assertSame($searchResult, $result);
    }

    public function testListFilesUsesBrowseWhenNoQueryOrMetadata(): void
    {
        $asset = $this->createMock(MediaAsset::class);
        $browseResult = [
            'path' => '/',
            'children' => [],
            'total' => 0,
        ];

        $searchBuilder = $this->createMock(AssetSearchBuilder::class);
        $searchBuilder->expects($this->never())->method('search');

        $treeBuilder = $this->createMock(AssetTreeBuilderInterface::class);
        $treeBuilder->expects($this->once())->method('build')->willReturn($browseResult);

        $service = new AssetFilesService(
            $searchBuilder,
            $treeBuilder,
            new CurrentAssetResolver($this->createMock(PermissionCheckerInterface::class))
        );
        $result = $service->listFiles($asset, 'item-uri', 'en-US', [], []);

        $this->assertSame($browseResult, $result);
    }

    public function testListFilesUsesSearchWhenMetadataPresent(): void
    {
        $asset = $this->createMock(MediaAsset::class);
        $searchResult = ['items' => [], 'total' => 0, 'page' => 1, 'pageSize' => 10];

        $searchBuilder = $this->createMock(AssetSearchBuilder::class);
        $searchBuilder->expects($this->once())
            ->method('search')
            ->with($this->callback(static function (AssetSearchQuery $query): bool {
                return $query->hasMetadataCriteria();
            }))
            ->willReturn($searchResult);

        $treeBuilder = $this->createMock(AssetTreeBuilderInterface::class);
        $treeBuilder->expects($this->never())->method('build');

        $service = new AssetFilesService(
            $searchBuilder,
            $treeBuilder,
            new CurrentAssetResolver($this->createMock(PermissionCheckerInterface::class))
        );
        $result = $service->listFiles(
            $asset,
            'item-uri',
            'en-US',
            ['metadata' => ['http://example/prop' => 'value']],
            []
        );

        $this->assertSame($searchResult, $result);
    }
}

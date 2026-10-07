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
use oat\taoItems\model\media\AssetBrowseListBuilderInterface;
use oat\taoItems\model\media\AssetTreeBrowseBuilderInterface;
use oat\taoItems\model\media\AssetFilesService;
use oat\taoItems\model\media\AssetSearchBuilder;
use oat\taoItems\model\media\AssetSearchQuery;
use oat\taoItems\model\media\AssetTreeBuilderInterface;
use oat\taoItems\model\media\CurrentAssetResolver;
use InvalidArgumentException;

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

    public function testListFilesUsesBuildTreeWhenPartIsTree(): void
    {
        $asset = $this->createMock(MediaAsset::class);
        $treeResult = ['path' => '/media', 'children' => []];

        $searchBuilder = $this->createMock(AssetSearchBuilder::class);
        $searchBuilder->expects($this->never())->method('search');

        $treeBuilder = new class($treeResult) implements AssetTreeBuilderInterface, AssetTreeBrowseBuilderInterface {
            /** @var array<string, mixed> */
            private $treeResult;

            /** @param array<string, mixed> $treeResult */
            public function __construct(array $treeResult)
            {
                $this->treeResult = $treeResult;
            }

            public function build(\oat\tao\model\media\mediaSource\DirectorySearchQuery $search): array
            {
                return [];
            }

            public function buildTree(\oat\tao\model\media\mediaSource\DirectorySearchQuery $search): array
            {
                return $this->treeResult;
            }
        };

        $service = new AssetFilesService(
            $searchBuilder,
            $treeBuilder,
            new CurrentAssetResolver($this->createMock(PermissionCheckerInterface::class))
        );

        $result = $service->listFiles($asset, 'item-uri', 'en-US', ['part' => 'tree', 'depth' => 1], []);

        $this->assertSame($treeResult, $result);
    }

    public function testListFilesUsesBuildAssetListWhenPartIsList(): void
    {
        $asset = $this->createMock(MediaAsset::class);
        $listResult = ['items' => [], 'total' => 0, 'page' => 2, 'pageSize' => 15];

        $searchBuilder = $this->createMock(AssetSearchBuilder::class);
        $searchBuilder->expects($this->never())->method('search');

        $treeBuilder = $this->createMock(AssetTreeBuilderInterface::class);
        $treeBuilder = new class ($listResult) implements
            AssetTreeBuilderInterface,
            AssetTreeBrowseBuilderInterface,
            AssetBrowseListBuilderInterface {
            /** @var array<string, mixed> */
            private $listResult;

            /** @param array<string, mixed> $listResult */
            public function __construct(array $listResult)
            {
                $this->listResult = $listResult;
            }

            public function build(\oat\tao\model\media\mediaSource\DirectorySearchQuery $search): array
            {
                return [];
            }

            public function buildTree(\oat\tao\model\media\mediaSource\DirectorySearchQuery $search): array
            {
                return [];
            }

            public function buildAssetList(\oat\tao\model\media\mediaSource\DirectorySearchQuery $search): array
            {
                return $this->listResult;
            }
        };

        $service = new AssetFilesService(
            $searchBuilder,
            $treeBuilder,
            new CurrentAssetResolver($this->createMock(PermissionCheckerInterface::class))
        );

        $result = $service->listFiles(
            $asset,
            'item-uri',
            'en-US',
            ['part' => 'list', 'page' => 2, 'pageSize' => 15],
            []
        );

        $this->assertSame($listResult, $result);
    }

    public function testListFilesRejectsInvalidPartEvenWhenQueryPresent(): void
    {
        $service = new AssetFilesService(
            $this->createMock(AssetSearchBuilder::class),
            $this->createMock(AssetTreeBuilderInterface::class),
            new CurrentAssetResolver($this->createMock(PermissionCheckerInterface::class))
        );

        $this->expectException(InvalidArgumentException::class);

        $service->listFiles(
            $this->createMock(MediaAsset::class),
            'item-uri',
            'en-US',
            ['part' => 'nope', 'query' => 'clip'],
            []
        );
    }

    public function testListFilesRejectsInvalidPart(): void
    {
        $service = new AssetFilesService(
            $this->createMock(AssetSearchBuilder::class),
            $this->createMock(AssetTreeBuilderInterface::class),
            new CurrentAssetResolver($this->createMock(PermissionCheckerInterface::class))
        );

        $this->expectException(InvalidArgumentException::class);

        $service->listFiles(
            $this->createMock(MediaAsset::class),
            'item-uri',
            'en-US',
            ['part' => 'nope'],
            []
        );
    }
}

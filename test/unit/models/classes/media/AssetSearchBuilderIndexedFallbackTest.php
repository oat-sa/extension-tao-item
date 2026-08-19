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
use oat\tao\model\AdvancedSearch\AdvancedSearchChecker;
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\MediaBrowser;
use oat\taoAdvancedSearch\model\SearchEngine\Exception\AssetSearchUnavailableException;
use oat\taoItems\model\media\AssetIndexedSearchGatewayInterface;
use oat\taoItems\model\media\AssetSearchBuilder;
use oat\taoItems\model\media\AssetSearchQuery;
use PHPUnit\Framework\MockObject\MockObject;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssetSearchBuilderIndexedFallbackTest extends TestCase
{
    /** @var AssetSearchBuilder */
    private $subject;

    /** @var ServiceLocatorInterface|MockObject */
    private $serviceLocator;

    /** @var MediaBrowser|MockObject */
    private $mediaSource;

    protected function setUp(): void
    {
        $this->subject = new AssetSearchBuilder();
        $this->serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $this->subject->setServiceLocator($this->serviceLocator);

        $this->mediaSource = $this->createMock(MediaBrowser::class);
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => 'taomedia://mediamanager/Assets',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'local.png',
                    'uri' => 'asset://local',
                    'mime' => 'image/png',
                ],
            ],
        ]);
    }

    public function testSearchFallsBackToFilesystemWhenGatewayIsUnavailable(): void
    {
        $this->serviceLocator->method('has')->willReturnMap([
            [AssetIndexedSearchGatewayInterface::SERVICE_ID, false],
        ]);

        $result = $this->subject->search($this->createQuery('local'));

        $this->assertSame(1, $result['total']);
        $this->assertSame('local.png', $result['items'][0]['name']);
    }

    public function testSearchFallsBackToFilesystemWhenGatewayThrows(): void
    {
        $checker = $this->createMock(AdvancedSearchChecker::class);
        $checker->method('isEnabled')->willReturn(true);
        $checker->method('ping')->willReturn(true);

        $gateway = $this->createMock(AssetIndexedSearchGatewayInterface::class);
        $gateway->method('isAvailable')->willReturn(true);
        $gateway->expects($this->once())->method('search')->willThrowException(new AssetSearchUnavailableException('ES down'));

        $this->serviceLocator->method('has')->willReturnMap([
            [AssetIndexedSearchGatewayInterface::SERVICE_ID, true],
            [AdvancedSearchChecker::class, true],
        ]);
        $this->serviceLocator->method('get')->willReturnMap([
            [AdvancedSearchChecker::class, $checker],
            [AssetIndexedSearchGatewayInterface::SERVICE_ID, $gateway],
        ]);

        $result = $this->subject->search($this->createQuery('local'));

        $this->assertSame(1, $result['total']);
        $this->assertSame('local.png', $result['items'][0]['name']);
    }

    public function testSearchReturnsEmptyResultWhenIndexedSearchFailsWithMetadataCriteria(): void
    {
        $checker = $this->createMock(AdvancedSearchChecker::class);
        $checker->method('isEnabled')->willReturn(true);
        $checker->method('ping')->willReturn(true);

        $gateway = $this->createMock(AssetIndexedSearchGatewayInterface::class);
        $gateway->method('isAvailable')->willReturn(true);
        $gateway->expects($this->once())->method('search')->willThrowException(new AssetSearchUnavailableException('ES down'));

        $this->serviceLocator->method('has')->willReturnMap([
            [AssetIndexedSearchGatewayInterface::SERVICE_ID, true],
            [AdvancedSearchChecker::class, true],
        ]);
        $this->serviceLocator->method('get')->willReturnMap([
            [AdvancedSearchChecker::class, $checker],
            [AssetIndexedSearchGatewayInterface::SERVICE_ID, $gateway],
        ]);

        $query = $this->createQuery('local');
        $query->setMetadataCriteria(['http://example.com/property' => 'science']);

        $result = $this->subject->search($query);

        $this->assertSame(0, $result['total']);
        $this->assertSame([], $result['items']);
    }

    private function createQuery(string $queryText): AssetSearchQuery
    {
        $mediaAsset = $this->createMock(MediaAsset::class);
        $mediaAsset->method('getMediaSource')->willReturn($this->mediaSource);
        $mediaAsset->method('getMediaIdentifier')->willReturn('/');

        return (new AssetSearchQuery($mediaAsset, 'item-uri', 'en-US'))
            ->setQuery($queryText)
            ->setPage(1)
            ->setPageSize(10);
    }
}

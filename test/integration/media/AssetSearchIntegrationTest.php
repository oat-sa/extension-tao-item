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

namespace oat\taoItems\test\integration\media;

use oat\generis\test\TestCase;
use oat\tao\model\accessControl\AccessControlEnablerInterface;
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\MediaBrowser;
use oat\taoItems\model\media\AssetSearchBuilder;
use oat\taoItems\model\media\AssetSearchQuery;

abstract class AccessControlMediaSource implements MediaBrowser, AccessControlEnablerInterface
{
}

/**
 * Scope and authorization contract tests for Resource Manager asset search.
 */
class AssetSearchIntegrationTest extends TestCase
{
    /** @var AssetSearchBuilder */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new AssetSearchBuilder();
    }

    public function testSearchFindsAssetInScopedSubfolder(): void
    {
        $mediaSource = $this->createMock(MediaBrowser::class);
        $mediaSource->method('getDirectories')->willReturn([
            'path' => 'taomedia://mediamanager/Allowed',
            'label' => 'Allowed',
            'children' => [
                [
                    'path' => 'taomedia://mediamanager/Allowed/Subfolder',
                    'label' => 'Subfolder',
                    'children' => [
                        [
                            'name' => 'nested.png',
                            'uri' => 'asset://nested',
                            'mime' => 'image/png',
                        ],
                    ],
                ],
            ],
        ]);

        $result = $this->subject->search($this->createQuery($mediaSource, 'nested'));

        $this->assertSame(1, $result['total']);
        $this->assertSame('Allowed/Subfolder', $result['items'][0]['location']);
    }

    public function testSearchDoesNotIncludeAssetsOutsideScopedTree(): void
    {
        $mediaSource = $this->createMock(MediaBrowser::class);
        $mediaSource->method('getDirectories')->willReturn([
            'path' => 'taomedia://mediamanager/SiblingFolder',
            'label' => 'SiblingFolder',
            'children' => [],
        ]);

        $result = $this->subject->search($this->createQuery($mediaSource, 'nested'));

        $this->assertSame(0, $result['total']);
        $this->assertSame([], $result['items']);
    }

    public function testSearchReturnsEmptyResultWhenUserHasNoReadableAssets(): void
    {
        $mediaSource = $this->createMock(AccessControlMediaSource::class);
        $mediaSource->expects($this->once())->method('enableAccessControl');
        $mediaSource->method('getDirectories')->willReturn([
            'path' => 'taomedia://mediamanager/Restricted',
            'label' => 'Restricted',
            'children' => [],
        ]);

        $result = $this->subject->search($this->createQuery($mediaSource, 'anything'));

        $this->assertSame(0, $result['total']);
        $this->assertSame([], $result['items']);
    }

    public function testSearchAppliesMimeFiltersFromQuery(): void
    {
        $mediaSource = $this->createMock(MediaBrowser::class);
        $mediaSource
            ->expects($this->once())
            ->method('getDirectories')
            ->with($this->callback(function (AssetSearchQuery $query): bool {
                return $query->getFilter() === ['video/mp4'];
            }))
            ->willReturn([
                'path' => '/',
                'label' => 'Assets',
                'children' => [
                    [
                        'name' => 'clip.mp4',
                        'uri' => 'asset://clip',
                        'mime' => 'video/mp4',
                    ],
                ],
            ]);

        $mediaAsset = $this->createMock(MediaAsset::class);
        $mediaAsset->method('getMediaSource')->willReturn($mediaSource);
        $mediaAsset->method('getMediaIdentifier')->willReturn('/');

        $query = (new AssetSearchQuery($mediaAsset, 'item-uri', 'en-US', ['video/mp4']))
            ->setQuery('clip')
            ->setPage(1)
            ->setPageSize(10);

        $result = $this->subject->search($query);

        $this->assertSame(1, $result['total']);
        $this->assertSame('video/mp4', $result['items'][0]['mime']);
    }

    private function createQuery(MediaBrowser $mediaSource, string $queryText): AssetSearchQuery
    {
        $mediaAsset = $this->createMock(MediaAsset::class);
        $mediaAsset->method('getMediaSource')->willReturn($mediaSource);
        $mediaAsset->method('getMediaIdentifier')->willReturn('/');

        return (new AssetSearchQuery($mediaAsset, 'item-uri', 'en-US'))
            ->setQuery($queryText)
            ->setPage(1)
            ->setPageSize(10);
    }
}

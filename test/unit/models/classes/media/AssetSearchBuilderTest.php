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
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\MediaBrowser;
use oat\taoItems\model\media\AssetSearchBuilder;

class AssetSearchBuilderTest extends TestCase
{
    /** @var AssetSearchBuilder */
    private $subject;

    /** @var MediaBrowser */
    private $mediaSource;

    protected function setUp(): void
    {
        $this->subject = new AssetSearchBuilder();
        $this->mediaSource = $this->createMock(MediaBrowser::class);
    }

    public function testSearchFiltersSortsAndPaginatesResults(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => 'taomedia://mediamanager/Media',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'video-example.mp4',
                    'uri' => 'taomedia://mediamanager/video-example',
                    'mime' => 'video/mp4',
                ],
                [
                    'name' => 'colorbars.mp4',
                    'uri' => 'taomedia://mediamanager/colorbars',
                    'mime' => 'video/mp4',
                ],
                [
                    'path' => 'taomedia://mediamanager/Folder',
                    'label' => 'Folder',
                    'children' => [
                        [
                            'name' => 'color-grade.png',
                            'uri' => 'taomedia://mediamanager/color-grade',
                            'mime' => 'image/png',
                        ],
                    ],
                ],
                [
                    'name' => 'beep.mp3',
                    'uri' => 'taomedia://mediamanager/beep',
                    'mime' => 'audio/mpeg',
                ],
            ],
        ]);

        $result = $this->subject->search($this->createSearchQuery('color', 1, 10));

        $this->assertSame(2, $result['total']);
        $this->assertSame(1, $result['page']);
        $this->assertSame(10, $result['pageSize']);
        $this->assertCount(2, $result['items']);
        $this->assertSame('color-grade.png', $result['items'][0]['name']);
        $this->assertSame('Assets/Folder', $result['items'][0]['location']);
        $this->assertSame('colorbars.mp4', $result['items'][1]['name']);
    }

    public function testSearchUsesTrailingTokenPrefixMatching(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'mycolor.mp4',
                    'uri' => 'asset://mycolor',
                    'mime' => 'video/mp4',
                ],
                [
                    'name' => 'colorbars.mp4',
                    'uri' => 'asset://colorbars',
                    'mime' => 'video/mp4',
                ],
            ],
        ]);

        $result = $this->subject->search($this->createSearchQuery('color', 1, 10));

        $this->assertSame(1, $result['total']);
        $this->assertSame('colorbars.mp4', $result['items'][0]['name']);
    }

    public function testSearchPaginatesStableSortedResults(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'color-b.mp4',
                    'uri' => 'asset://b',
                    'mime' => 'video/mp4',
                ],
                [
                    'name' => 'color-a.mp4',
                    'uri' => 'asset://a',
                    'mime' => 'video/mp4',
                ],
                [
                    'name' => 'color-c.mp4',
                    'uri' => 'asset://c',
                    'mime' => 'video/mp4',
                ],
            ],
        ]);

        $result = $this->subject->search($this->createSearchQuery('color', 2, 1));

        $this->assertSame(3, $result['total']);
        $this->assertSame(2, $result['page']);
        $this->assertSame(1, $result['pageSize']);
        $this->assertCount(1, $result['items']);
        $this->assertSame('color-b.mp4', $result['items'][0]['name']);
    }

    public function testSearchMatchesNonAsciiAssetNames(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'colorbars.mp4',
                    'uri' => 'asset://colorbars',
                    'mime' => 'video/mp4',
                ],
                [
                    'name' => 'цвет-bars.mp4',
                    'uri' => 'asset://cyrillic',
                    'mime' => 'video/mp4',
                ],
            ],
        ]);

        $result = $this->subject->search($this->createSearchQuery('цвет', 1, 10));

        $this->assertSame(1, $result['total']);
        $this->assertSame('цвет-bars.mp4', $result['items'][0]['name']);
    }

    private function createSearchQuery(string $query, int $page, int $pageSize): CompatDirectorySearchQuery
    {
        $mediaAsset = $this->createMock(MediaAsset::class);
        $mediaAsset->method('getMediaSource')->willReturn($this->mediaSource);
        $mediaAsset->method('getMediaIdentifier')->willReturn('/');

        return (new CompatDirectorySearchQuery($mediaAsset, 'item-uri', 'en-US'))
            ->setQuery($query)
            ->setPage($page)
            ->setPageSize($pageSize)
            ->setSortBy(CompatDirectorySearchQuery::SORT_LABEL)
            ->setSortDir('asc');
    }
}

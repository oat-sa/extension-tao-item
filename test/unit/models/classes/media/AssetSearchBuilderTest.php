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
use oat\tao\model\accessControl\AccessControlEnablerInterface;
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\MediaBrowser;
use oat\taoItems\model\media\AssetSearchBuilder;
use oat\taoItems\model\media\AssetSearchQuery;

abstract class AccessControlMediaSource implements MediaBrowser, AccessControlEnablerInterface
{
}

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

    public function testSearchTraversesAssetsNestedDeeperThanThirtyTwoLevels(): void
    {
        $this->mediaSource
            ->expects($this->once())
            ->method('getDirectories')
            ->willReturnCallback(function (AssetSearchQuery $query): array {
                $this->assertGreaterThan(32, $query->getDepth());

                return $this->createDeepTree(40);
            });

        $result = $this->subject->search($this->createSearchQuery('deep-asset', 1, 10));

        $this->assertSame(1, $result['total']);
        $this->assertSame('deep-asset.png', $result['items'][0]['name']);
        $this->assertStringContainsString('Level 40', $result['items'][0]['location']);
    }

    public function testSearchUsesStableTieBreakersForNonLabelSorts(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'beta.png',
                    'label' => 'Beta',
                    'uri' => 'asset://2',
                    'mime' => 'image/png',
                    'location' => 'Shared',
                    'updatedAt' => '2026-08-01T10:00:00Z',
                ],
                [
                    'name' => 'alpha.png',
                    'label' => 'Alpha',
                    'uri' => 'asset://1',
                    'mime' => 'image/png',
                    'location' => 'Shared',
                    'updatedAt' => '2026-08-01T10:00:00Z',
                ],
                [
                    'name' => 'alpha-copy.png',
                    'label' => 'Alpha',
                    'uri' => 'asset://3',
                    'mime' => 'image/png',
                    'location' => 'Shared',
                    'updatedAt' => '2026-08-01T10:00:00Z',
                ],
            ],
        ]);

        $result = $this->subject->search(
            $this->createSearchQuery('', 1, 10)
                ->setSortBy(AssetSearchQuery::SORT_LOCATION)
        );

        $this->assertSame(
            ['asset://1', 'asset://3', 'asset://2'],
            array_column($result['items'], 'uri')
        );
    }

    public function testSearchReturnsEmptyForDelimiterOnlyQuery(): void
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
            ],
        ]);

        $result = $this->subject->search($this->createSearchQuery('---', 1, 10));

        $this->assertSame(0, $result['total']);
        $this->assertSame([], $result['items']);
    }

    public function testSearchRequiresAllQueryTokensToMatch(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'color-grade.png',
                    'uri' => 'asset://color-grade',
                    'mime' => 'image/png',
                ],
                [
                    'name' => 'colorbars.mp4',
                    'uri' => 'asset://colorbars',
                    'mime' => 'video/mp4',
                ],
                [
                    'name' => 'grade-only.png',
                    'uri' => 'asset://grade-only',
                    'mime' => 'image/png',
                ],
            ],
        ]);

        $result = $this->subject->search($this->createSearchQuery('color grade', 1, 10));

        $this->assertSame(1, $result['total']);
        $this->assertSame('color-grade.png', $result['items'][0]['name']);
    }

    public function testSearchSortsUnicodeLabelsCaseInsensitively(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'eclair.png',
                    'label' => 'Éclair',
                    'uri' => 'asset://eclair-upper',
                    'mime' => 'image/png',
                ],
                [
                    'name' => 'eclair-lower.png',
                    'label' => 'éclair',
                    'uri' => 'asset://eclair-lower',
                    'mime' => 'image/png',
                ],
            ],
        ]);

        $result = $this->subject->search($this->createSearchQuery('eclair', 1, 10));

        $this->assertSame(2, $result['total']);
        // After Unicode case-folding labels tie; URI tie-breaker is ascending.
        $this->assertSame(
            ['éclair', 'Éclair'],
            array_column($result['items'], 'label')
        );
        $this->assertSame(
            ['asset://eclair-lower', 'asset://eclair-upper'],
            array_column($result['items'], 'uri')
        );
    }

    public function testSearchReturnsEmptyForNonMatchingQuery(): void
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
            ],
        ]);

        $result = $this->subject->search($this->createSearchQuery('zzzznonexistent', 1, 10));

        $this->assertSame(0, $result['total']);
        $this->assertSame([], $result['items']);
    }

    public function testSearchPaginatesWithoutDuplicatesOrSkips(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'asset-a.png',
                    'uri' => 'asset://a',
                    'mime' => 'image/png',
                ],
                [
                    'name' => 'asset-b.png',
                    'uri' => 'asset://b',
                    'mime' => 'image/png',
                ],
                [
                    'name' => 'asset-c.png',
                    'uri' => 'asset://c',
                    'mime' => 'image/png',
                ],
            ],
        ]);

        $pageOne = $this->subject->search($this->createSearchQuery('asset', 1, 2));
        $pageTwo = $this->subject->search($this->createSearchQuery('asset', 2, 2));

        $this->assertSame(3, $pageOne['total']);
        $this->assertSame(3, $pageTwo['total']);
        $this->assertCount(2, $pageOne['items']);
        $this->assertCount(1, $pageTwo['items']);

        $uris = array_merge(
            array_column($pageOne['items'], 'uri'),
            array_column($pageTwo['items'], 'uri')
        );
        $this->assertSame(['asset://a', 'asset://b', 'asset://c'], $uris);
    }

    public function testSearchClampsPageSizeToMax(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [],
        ]);

        $result = $this->subject->search($this->createSearchQuery('anything', 1, 99999));

        $this->assertSame(100, $result['pageSize']);
    }

    public function testSearchSortsNullLocationsLast(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'with-location.png',
                    'label' => 'With location',
                    'uri' => 'asset://with-location',
                    'mime' => 'image/png',
                    'location' => 'Folder/A',
                ],
                [
                    'name' => 'no-location.png',
                    'label' => 'No location',
                    'uri' => 'asset://no-location',
                    'mime' => 'image/png',
                    'location' => '',
                ],
            ],
        ]);

        $result = $this->subject->search(
            $this->createSearchQuery('', 1, 10)
                ->setSortBy(AssetSearchQuery::SORT_LOCATION)
        );

        $this->assertSame('asset://with-location', $result['items'][0]['uri']);
        $this->assertSame('asset://no-location', $result['items'][1]['uri']);
    }

    public function testSearchSortsNullUpdatedAtLast(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'dated.png',
                    'label' => 'Dated',
                    'uri' => 'asset://dated',
                    'mime' => 'image/png',
                    'updatedAt' => '2026-08-02T10:00:00Z',
                ],
                [
                    'name' => 'undated.png',
                    'label' => 'Undated',
                    'uri' => 'asset://undated',
                    'mime' => 'image/png',
                ],
            ],
        ]);

        $result = $this->subject->search(
            $this->createSearchQuery('', 1, 10)
                ->setSortBy(AssetSearchQuery::SORT_UPDATED_AT)
                ->setSortDir('asc')
        );

        $this->assertSame('asset://dated', $result['items'][0]['uri']);
        $this->assertSame('asset://undated', $result['items'][1]['uri']);
    }

    public function testSearchPassesMimeFiltersToMediaSource(): void
    {
        $this->mediaSource
            ->expects($this->once())
            ->method('getDirectories')
            ->with($this->callback(function (AssetSearchQuery $query): bool {
                return $query->getFilter() === ['image/png', 'image/jpeg'];
            }))
            ->willReturn([
                'path' => '/',
                'label' => 'Assets',
                'children' => [
                    [
                        'name' => 'test.png',
                        'uri' => 'asset://test-png',
                        'mime' => 'image/png',
                    ],
                ],
            ]);

        $mediaAsset = $this->createMock(MediaAsset::class);
        $mediaAsset->method('getMediaSource')->willReturn($this->mediaSource);
        $mediaAsset->method('getMediaIdentifier')->willReturn('/');

        $query = (new AssetSearchQuery($mediaAsset, 'item-uri', 'en-US', ['image/png', 'image/jpeg']))
            ->setQuery('test')
            ->setPage(1)
            ->setPageSize(10);

        $result = $this->subject->search($query);

        $this->assertSame(1, $result['total']);
        $this->assertSame('test.png', $result['items'][0]['name']);
    }

    public function testSearchOnlyIncludesAssetsReturnedByScopedTree(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => 'taomedia://mediamanager/Allowed',
            'label' => 'Allowed',
            'children' => [
                [
                    'path' => 'taomedia://mediamanager/Allowed/Subfolder',
                    'label' => 'Subfolder',
                    'children' => [
                        [
                            'name' => 'scoped-asset.png',
                            'uri' => 'asset://scoped',
                            'mime' => 'image/png',
                        ],
                    ],
                ],
            ],
        ]);

        $result = $this->subject->search($this->createSearchQuery('scoped', 1, 10));

        $this->assertSame(1, $result['total']);
        $this->assertSame('Allowed/Subfolder', $result['items'][0]['location']);
    }

    public function testSearchReturnsEmptyWhenAccessControlFiltersAllAssets(): void
    {
        $accessControlledSource = $this->createMock(AccessControlMediaSource::class);
        $accessControlledSource->expects($this->once())->method('enableAccessControl');
        $accessControlledSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [],
        ]);

        $mediaAsset = $this->createMock(MediaAsset::class);
        $mediaAsset->method('getMediaSource')->willReturn($accessControlledSource);
        $mediaAsset->method('getMediaIdentifier')->willReturn('/');

        $result = $this->subject->search(
            (new AssetSearchQuery($mediaAsset, 'item-uri', 'en-US'))
                ->setQuery('anything')
                ->setPage(1)
                ->setPageSize(10)
        );

        $this->assertSame(0, $result['total']);
        $this->assertSame([], $result['items']);
    }

    public function testSearchPreservesPermissionsFromMediaSourcePayload(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'readable.png',
                    'uri' => 'asset://readable',
                    'mime' => 'image/png',
                    'permissions' => ['READ' => true],
                ],
            ],
        ]);

        $result = $this->subject->search($this->createSearchQuery('readable', 1, 10));

        $this->assertSame(['READ' => true], $result['items'][0]['permissions']);
    }

    private function createSearchQuery(string $query, int $page, int $pageSize): AssetSearchQuery
    {
        $mediaAsset = $this->createMock(MediaAsset::class);
        $mediaAsset->method('getMediaSource')->willReturn($this->mediaSource);
        $mediaAsset->method('getMediaIdentifier')->willReturn('/');

        return (new AssetSearchQuery($mediaAsset, 'item-uri', 'en-US'))
            ->setQuery($query)
            ->setPage($page)
            ->setPageSize($pageSize)
            ->setSortBy(AssetSearchQuery::SORT_LABEL)
            ->setSortDir('asc');
    }

    private function createDeepTree(int $depth): array
    {
        $node = [
            'name' => 'deep-asset.png',
            'uri' => 'asset://deep',
            'mime' => 'image/png',
        ];

        for ($level = $depth; $level >= 1; $level--) {
            $node = [
                'path' => '/level-' . $level,
                'label' => 'Level ' . $level,
                'children' => [$node],
            ];
        }

        return [
            'path' => '/',
            'label' => 'Assets',
            'children' => [$node],
        ];
    }
}

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
use oat\taoItems\model\media\AssetIndexedSearchGatewayInterface;
use oat\taoItems\model\media\AssetSearchBuilder;
use oat\taoItems\model\media\AssetSearchQuery;
use oat\taoItems\model\media\AssetSearchUnavailableException;

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

        foreach (['---', '!!!', '...', '- - -'] as $delimiterOnlyQuery) {
            $result = $this->subject->search($this->createSearchQuery($delimiterOnlyQuery, 1, 10));

            $this->assertSame(0, $result['total'], 'Expected empty total for query: ' . $delimiterOnlyQuery);
            $this->assertSame([], $result['items'], 'Expected empty items for query: ' . $delimiterOnlyQuery);
            $this->assertSame(1, $result['page']);
            $this->assertSame(10, $result['pageSize']);
        }
    }

    public function testSearchWithoutTextQueryReturnsAllFlattenedAssets(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'root.png',
                    'uri' => 'asset://root',
                    'mime' => 'image/png',
                ],
                [
                    'name' => 'other.mp4',
                    'uri' => 'asset://other',
                    'mime' => 'video/mp4',
                ],
            ],
        ]);

        foreach (['', '   ', "\t\n"] as $emptyQuery) {
            $result = $this->subject->search($this->createSearchQuery($emptyQuery, 1, 10));

            $this->assertSame(2, $result['total'], 'Expected no text filter for query: ' . var_export($emptyQuery, true));
            $this->assertCount(2, $result['items']);
        }
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
                    'name' => 'zeta.png',
                    'label' => 'Zeta',
                    'uri' => 'asset://zeta',
                    'mime' => 'image/png',
                ],
                [
                    'name' => 'eclair-upper.png',
                    'label' => 'Éclair',
                    'uri' => 'asset://eclair-upper',
                    'mime' => 'image/png',
                ],
                [
                    'name' => 'alpha.png',
                    'label' => 'Alpha',
                    'uri' => 'asset://alpha',
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

        $result = $this->subject->search($this->createSearchQuery('', 1, 10));

        $this->assertSame(4, $result['total']);
        // mb_strtolower folds É/é to the same key; equal keys then tie-break on uri.
        // Byte-order string compare places ASCII labels before accented ones.
        $this->assertSame(
            ['asset://alpha', 'asset://zeta', 'asset://eclair-lower', 'asset://eclair-upper'],
            array_column($result['items'], 'uri')
        );
    }

    public function testSearchMatchesUnicodeQueryCaseInsensitively(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'eclair.png',
                    'label' => 'Éclair',
                    'uri' => 'asset://eclair',
                    'mime' => 'image/png',
                ],
                [
                    'name' => 'other.png',
                    'label' => 'Other',
                    'uri' => 'asset://other',
                    'mime' => 'image/png',
                ],
            ],
        ]);

        $result = $this->subject->search($this->createSearchQuery('ÉCLAIR', 1, 10));

        $this->assertSame(1, $result['total']);
        $this->assertSame('asset://eclair', $result['items'][0]['uri']);
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

    public function testSearchSortsNullLocationLastWhenDescending(): void
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
                    'location' => 'Folder/B',
                ],
                [
                    'name' => 'no-location.png',
                    'label' => 'No location',
                    'uri' => 'asset://no-location',
                    'mime' => 'image/png',
                    'location' => '',
                ],
                [
                    'name' => 'other-location.png',
                    'label' => 'Other location',
                    'uri' => 'asset://other-location',
                    'mime' => 'image/png',
                    'location' => 'Folder/A',
                ],
            ],
        ]);

        $result = $this->subject->search(
            $this->createSearchQuery('', 1, 10)
                ->setSortBy(AssetSearchQuery::SORT_LOCATION)
                ->setSortDir('desc')
        );

        $this->assertSame('asset://with-location', $result['items'][0]['uri']);
        $this->assertSame('asset://other-location', $result['items'][1]['uri']);
        $this->assertSame('asset://no-location', $result['items'][2]['uri']);
    }

    public function testSearchSortsNullUpdatedAtLastWhenDescending(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Assets',
            'children' => [
                [
                    'name' => 'latest.png',
                    'label' => 'Latest',
                    'uri' => 'asset://latest',
                    'mime' => 'image/png',
                    'updatedAt' => '2026-08-03T10:00:00Z',
                ],
                [
                    'name' => 'undated.png',
                    'label' => 'Undated',
                    'uri' => 'asset://undated',
                    'mime' => 'image/png',
                ],
                [
                    'name' => 'older.png',
                    'label' => 'Older',
                    'uri' => 'asset://older',
                    'mime' => 'image/png',
                    'updatedAt' => '2026-08-01T10:00:00Z',
                ],
            ],
        ]);

        $result = $this->subject->search(
            $this->createSearchQuery('', 1, 10)
                ->setSortBy(AssetSearchQuery::SORT_UPDATED_AT)
                ->setSortDir('desc')
        );

        $this->assertSame('asset://latest', $result['items'][0]['uri']);
        $this->assertSame('asset://older', $result['items'][1]['uri']);
        $this->assertSame('asset://undated', $result['items'][2]['uri']);
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

    public function testSearchParentScopeIncludesNestedFileAssets(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => 'taomedia://mediamanager/Parent',
            'label' => 'Parent',
            'children' => [
                [
                    'name' => 'root-level.png',
                    'uri' => 'asset://root-level',
                    'mime' => 'image/png',
                ],
                [
                    'path' => 'taomedia://mediamanager/Parent/Child',
                    'label' => 'Child',
                    'children' => [
                        [
                            'name' => 'nested.png',
                            'uri' => 'asset://nested',
                            'mime' => 'image/png',
                        ],
                        [
                            'path' => 'taomedia://mediamanager/Parent/Child/Grandchild',
                            'label' => 'Grandchild',
                            'children' => [
                                [
                                    'name' => 'deep-nested.png',
                                    'uri' => 'asset://deep-nested',
                                    'mime' => 'image/png',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $allInScope = $this->subject->search($this->createSearchQuery('', 1, 10));
        $this->assertSame(3, $allInScope['total']);
        $this->assertSame(
            ['asset://deep-nested', 'asset://nested', 'asset://root-level'],
            array_column($allInScope['items'], 'uri')
        );
        $this->assertSame('Parent/Child', $allInScope['items'][1]['location']);
        $this->assertSame('Parent/Child/Grandchild', $allInScope['items'][0]['location']);

        $nestedMatch = $this->subject->search($this->createSearchQuery('deep', 1, 10));
        $this->assertSame(1, $nestedMatch['total']);
        $this->assertSame('asset://deep-nested', $nestedMatch['items'][0]['uri']);
        $this->assertSame('Parent/Child/Grandchild', $nestedMatch['items'][0]['location']);
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

    public function testSearchReturnsEmptyWhenMetadataPresentWithoutIndexedGateway(): void
    {
        $this->mediaSource->expects($this->never())->method('getDirectories');

        $query = $this->createSearchQuery('', 1, 10)
            ->setMetadataCriteria([
                'http://www.tao.lu/Ontologies/TAOMedia.rdf#Language' =>
                    'http://www.tao.lu/Ontologies/TAO.rdf#Langja-JP',
            ]);

        $result = $this->subject->search($query);

        $this->assertSame(0, $result['total']);
        $this->assertSame([], $result['items']);
        $this->assertSame(1, $result['page']);
        $this->assertSame(10, $result['pageSize']);
    }

    public function testSearchDelegatesToIndexedGatewayWhenAvailable(): void
    {
        $expected = [
            'items' => [['uri' => 'asset://indexed', 'label' => 'indexed']],
            'total' => 1,
            'page' => 1,
            'pageSize' => 10,
        ];

        $gateway = $this->createMock(AssetIndexedSearchGatewayInterface::class);
        $gateway->expects($this->once())->method('isAvailable')->willReturn(true);
        $gateway->expects($this->once())->method('search')->willReturn($expected);

        $container = $this->createMock(\Psr\Container\ContainerInterface::class);
        $container->method('has')->with(AssetIndexedSearchGatewayInterface::SERVICE_ID)->willReturn(true);
        $container->method('get')->with(AssetIndexedSearchGatewayInterface::SERVICE_ID)->willReturn($gateway);

        $locator = new class ($container) implements \Zend\ServiceManager\ServiceLocatorInterface {
            /** @var \Psr\Container\ContainerInterface */
            private $container;

            public function __construct(\Psr\Container\ContainerInterface $container)
            {
                $this->container = $container;
            }

            public function get($id)
            {
                throw new \RuntimeException('Legacy ServiceLocator::get must not be used for gateway');
            }

            public function has($id)
            {
                return false;
            }

            public function getContainer(): \Psr\Container\ContainerInterface
            {
                return $this->container;
            }
        };
        $this->subject->setServiceLocator($locator);

        $this->mediaSource->expects($this->never())->method('getDirectories');

        $result = $this->subject->search($this->createSearchQuery('color', 1, 10));

        $this->assertSame($expected, $result);
    }

    public function testSearchPropagatesIndexedGatewayUnavailableException(): void
    {
        $gateway = $this->createMock(AssetIndexedSearchGatewayInterface::class);
        $gateway->expects($this->once())->method('isAvailable')->willReturn(true);
        $gateway->expects($this->once())->method('search')->willThrowException(
            new AssetSearchUnavailableException('es down')
        );

        $container = $this->createMock(\Psr\Container\ContainerInterface::class);
        $container->method('has')->with(AssetIndexedSearchGatewayInterface::SERVICE_ID)->willReturn(true);
        $container->method('get')->with(AssetIndexedSearchGatewayInterface::SERVICE_ID)->willReturn($gateway);

        $locator = new class ($container) implements \Zend\ServiceManager\ServiceLocatorInterface {
            /** @var \Psr\Container\ContainerInterface */
            private $container;

            public function __construct(\Psr\Container\ContainerInterface $container)
            {
                $this->container = $container;
            }

            public function get($id)
            {
                throw new \RuntimeException('Legacy ServiceLocator::get must not be used for gateway');
            }

            public function has($id)
            {
                return false;
            }

            public function getContainer(): \Psr\Container\ContainerInterface
            {
                return $this->container;
            }
        };
        $this->subject->setServiceLocator($locator);

        $this->expectException(AssetSearchUnavailableException::class);
        $this->expectExceptionMessage('es down');

        $this->subject->search($this->createSearchQuery('color', 1, 10));
    }

    public function testMetadataCriteriaNormalizedOnQuery(): void
    {
        $query = $this->createSearchQuery('x', 1, 10)->setMetadataCriteria([
            'http://example/prop' => 'value',
            'http://example/empty' => '',
            'http://example/list' => ['first', 'second'],
            12 => 'ignored',
        ]);

        $this->assertTrue($query->hasMetadataCriteria());
        $this->assertSame(
            [
                'http://example/prop' => 'value',
                'http://example/list' => 'first',
            ],
            $query->getMetadataCriteria()
        );
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

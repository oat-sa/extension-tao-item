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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA ;
 */

declare(strict_types=1);

namespace oat\taoItems\test\unit\models\classes\media;

use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\MediaBrowser;
use oat\tao\model\media\mediaSource\DirectorySearchQuery;
use oat\taoItems\model\media\AssetSearchQuery;
use oat\taoItems\model\media\AssetTreeBuilder;
use tao_helpers_Uri;
use oat\generis\test\TestCase;

class AssetTreeBuilderTest extends TestCase
{
    /** @var AssetTreeBuilder */
    private $subject;

    /** @var MediaBrowser&\PHPUnit\Framework\MockObject\MockObject */
    private $mediaSource;

    /** @var MediaAsset&\PHPUnit\Framework\MockObject\MockObject */
    private $mediaAsset;

    public function setUp(): void
    {
        $this->subject = new AssetTreeBuilder();
        $this->mediaSource = $this->createMock(MediaBrowser::class);
        $this->mediaAsset = $this->createMock(MediaAsset::class);
        $this->mediaAsset->method('getMediaIdentifier')->willReturn('/');
        $this->mediaAsset->method('getMediaSource')->willReturn($this->mediaSource);
    }

    public function testBuildWithAccessControlEnabled(): void
    {
        $data = [
            'children' => [
                [
                    'parent' => 'parent',
                ],
                [
                    'url' => 'something'
                ]
            ],
        ];

        $search = (new AssetSearchQuery($this->mediaAsset, '', ''))
            ->setSortBy(AssetSearchQuery::SORT_LABEL)
            ->setSortDir('asc');

        $this->mediaSource->method('getDirectories')
            ->willReturn($data);

        $expectedData = [
            'children' => [
                [
                    'url' => tao_helpers_Uri::getRootUrl() . 'taoItems/ItemContent/files?uri=&lang=&1=parent',
                    'path' => 'parent',
                ],
                [
                    'url' => 'something'
                ]
            ],
            'total' => 0,
            'childrenLimit' => 15,
        ];

        $this->assertEquals(
            $expectedData,
            $this->subject->build($search)
        );
    }

    public function testBuildDoesNotRequestUnboundedChildrenLimit(): void
    {
        $captured = null;
        $this->mediaSource->expects($this->once())
            ->method('getDirectories')
            ->with($this->callback(function (DirectorySearchQuery $query) use (&$captured): bool {
                $captured = $query;
                return true;
            }))
            ->willReturn(['path' => '/', 'label' => 'Root', 'children' => []]);

        $this->subject->build(new AssetSearchQuery($this->mediaAsset, 'item-uri', 'en-US'));

        $this->assertInstanceOf(AssetSearchQuery::class, $captured);
        $this->assertGreaterThan(0, $captured->getChildrenLimit());
        $this->assertSame(0, $captured->getChildrenOffset());
        $this->assertSame(PHP_INT_MAX, $captured->getDepth());
    }

    public function testBuildIncludesNestedFilesAndKeepsDirectoryStubs(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Root',
            'children' => [
                [
                    'path' => '/images',
                    'label' => 'images',
                    'children' => [
                        [
                            'path' => '/images/nested',
                            'label' => 'nested',
                            'children' => [
                                [
                                    'uri' => 'taomedia://local/nested.png',
                                    'name' => 'nested.png',
                                    'mime' => 'image/png',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'uri' => 'taomedia://local/root.png',
                    'name' => 'root.png',
                    'mime' => 'image/png',
                ],
            ],
        ]);

        $result = $this->subject->build(
            (new AssetSearchQuery($this->mediaAsset, 'item-uri', 'en-US'))
                ->setSortBy(AssetSearchQuery::SORT_LABEL)
                ->setSortDir('asc')
        );

        $this->assertSame(2, $result['total']);
        $this->assertSame(15, $result['childrenLimit']);

        $directories = array_values(array_filter(
            $result['children'],
            static function (array $child): bool {
                return isset($child['path']) && !isset($child['uri']);
            }
        ));
        $files = array_values(array_filter(
            $result['children'],
            static function (array $child): bool {
                return isset($child['uri']);
            }
        ));

        $this->assertCount(1, $directories);
        $this->assertSame('images', $directories[0]['label']);
        $this->assertArrayHasKey('url', $directories[0]);
        $this->assertArrayNotHasKey('children', $directories[0]);

        $this->assertCount(2, $files);
        $this->assertSame('nested.png', $files[0]['name']);
        $this->assertSame('Root/images/nested', $files[0]['location']);
        $this->assertSame('root.png', $files[1]['name']);
        $this->assertSame('Root', $files[1]['location']);
    }

    /**
     * @dataProvider sortProvider
     */
    public function testBuildSortsFiles(string $sortBy, string $sortDir, array $expectedNames): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Root',
            'children' => [
                [
                    'uri' => 'u-b',
                    'name' => 'Beta',
                    'label' => 'Beta',
                    'location' => 'Zulu',
                    'updatedAt' => '2024-01-02',
                    'mime' => 'image/png',
                ],
                [
                    'uri' => 'u-a',
                    'name' => 'alpha',
                    'label' => 'alpha',
                    'location' => 'able',
                    'updatedAt' => '2024-01-03',
                    'mime' => 'image/png',
                ],
                [
                    'uri' => 'u-c',
                    'name' => 'Charlie',
                    'label' => 'Charlie',
                    'location' => 'Baker',
                    'updatedAt' => '2024-01-01',
                    'mime' => 'image/png',
                ],
            ],
        ]);

        $result = $this->subject->build(
            (new AssetSearchQuery($this->mediaAsset, 'item-uri', 'en-US'))
                ->setSortBy($sortBy)
                ->setSortDir($sortDir)
        );

        $names = array_map(
            static function (array $child): string {
                return (string)($child['name'] ?? '');
            },
            array_values(array_filter(
                $result['children'],
                static function (array $child): bool {
                    return isset($child['uri']);
                }
            ))
        );

        $this->assertSame($expectedNames, $names);
        $this->assertSame(3, $result['total']);
    }

    public function sortProvider(): array
    {
        return [
            'label asc' => [AssetSearchQuery::SORT_LABEL, 'asc', ['alpha', 'Beta', 'Charlie']],
            'label desc' => [AssetSearchQuery::SORT_LABEL, 'desc', ['Charlie', 'Beta', 'alpha']],
            'location asc' => [AssetSearchQuery::SORT_LOCATION, 'asc', ['alpha', 'Charlie', 'Beta']],
            'location desc' => [AssetSearchQuery::SORT_LOCATION, 'desc', ['Beta', 'Charlie', 'alpha']],
            'updatedAt asc' => [AssetSearchQuery::SORT_UPDATED_AT, 'asc', ['Charlie', 'Beta', 'alpha']],
            'updatedAt desc' => [AssetSearchQuery::SORT_UPDATED_AT, 'desc', ['alpha', 'Beta', 'Charlie']],
            'invalid sort falls back to label' => ['unknown', 'asc', ['alpha', 'Beta', 'Charlie']],
        ];
    }

    public function testBuildPaginatesFilesOnlyAndPreservesTotal(): void
    {
        $this->subject->setOptions([AssetTreeBuilder::OPTION_PAGINATION_LIMIT => 2]);

        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Root',
            'children' => [
                [
                    'path' => '/folder',
                    'label' => 'folder',
                    'children' => [],
                ],
                [
                    'uri' => 'u-1',
                    'name' => 'a.png',
                    'mime' => 'image/png',
                ],
                [
                    'uri' => 'u-2',
                    'name' => 'b.png',
                    'mime' => 'image/png',
                ],
                [
                    'uri' => 'u-3',
                    'name' => 'c.png',
                    'mime' => 'image/png',
                ],
            ],
        ]);

        $result = $this->subject->build(
            new AssetSearchQuery($this->mediaAsset, 'item-uri', 'en-US', [], 1, 2)
        );

        $this->assertSame(3, $result['total']);
        $this->assertSame(2, $result['childrenLimit']);

        $files = array_values(array_filter(
            $result['children'],
            static function (array $child): bool {
                return isset($child['uri']);
            }
        ));
        $directories = array_values(array_filter(
            $result['children'],
            static function (array $child): bool {
                return isset($child['path']) && !isset($child['uri']);
            }
        ));

        $this->assertCount(1, $directories);
        $this->assertCount(1, $files);
        $this->assertSame('c.png', $files[0]['name']);
    }

    public function testBuildResetsOffsetForPlainDirectorySearchQuery(): void
    {
        $captured = null;
        $this->mediaSource->expects($this->once())
            ->method('getDirectories')
            ->with($this->callback(function (DirectorySearchQuery $query) use (&$captured): bool {
                $captured = $query;
                return true;
            }))
            ->willReturn([
                'path' => '/',
                'label' => 'Root',
                'children' => [
                    [
                        'uri' => 'u-1',
                        'name' => 'a.png',
                        'mime' => 'image/png',
                    ],
                    [
                        'uri' => 'u-2',
                        'name' => 'b.png',
                        'mime' => 'image/png',
                    ],
                    [
                        'uri' => 'u-3',
                        'name' => 'c.png',
                        'mime' => 'image/png',
                    ],
                ],
            ]);

        $this->subject->setOptions([AssetTreeBuilder::OPTION_PAGINATION_LIMIT => 1]);

        $plainQuery = new DirectorySearchQuery(
            $this->mediaAsset,
            'item-uri',
            'en-US',
            [],
            1,
            2,
            1
        );
        $plainQuery->setSortBy(AssetSearchQuery::SORT_LABEL)->setSortDir('asc');

        $result = $this->subject->build($plainQuery);

        $this->assertInstanceOf(AssetSearchQuery::class, $captured);
        $this->assertSame(0, $captured->getChildrenOffset());
        $this->assertGreaterThan(0, $captured->getChildrenLimit());
        $this->assertSame(3, $result['total']);
        $files = array_values(array_filter(
            $result['children'],
            static function (array $child): bool {
                return isset($child['uri']);
            }
        ));
        $this->assertCount(1, $files);
        $this->assertSame('c.png', $files[0]['name']);
    }

    public function testBuildUsesPathSegmentForPathOnlyNestedDirectories(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Root',
            'children' => [
                [
                    'path' => '/images/nested',
                    'children' => [
                        [
                            'uri' => 'taomedia://local/nested.png',
                            'name' => 'nested.png',
                            'mime' => 'image/png',
                        ],
                    ],
                ],
            ],
        ]);

        $result = $this->subject->build(new AssetSearchQuery($this->mediaAsset, 'item-uri', 'en-US'));

        $files = array_values(array_filter(
            $result['children'],
            static function (array $child): bool {
                return isset($child['uri']);
            }
        ));

        $this->assertCount(1, $files);
        $this->assertSame('Root/nested', $files[0]['location']);
    }

    public function testBuildSortsUnicodeLabelsCaseInsensitively(): void
    {
        $this->mediaSource->method('getDirectories')->willReturn([
            'path' => '/',
            'label' => 'Root',
            'children' => [
                [
                    'uri' => 'u-2',
                    'name' => 'Éclair',
                    'label' => 'Éclair',
                    'mime' => 'image/png',
                ],
                [
                    'uri' => 'u-1',
                    'name' => 'éclair',
                    'label' => 'éclair',
                    'mime' => 'image/png',
                ],
                [
                    'uri' => 'u-3',
                    'name' => 'Banana',
                    'label' => 'Banana',
                    'mime' => 'image/png',
                ],
            ],
        ]);

        $result = $this->subject->build(
            (new AssetSearchQuery($this->mediaAsset, 'item-uri', 'en-US'))
                ->setSortBy(AssetSearchQuery::SORT_LABEL)
                ->setSortDir('asc')
        );

        $labels = array_map(
            static function (array $child): string {
                return (string)($child['label'] ?? '');
            },
            array_values(array_filter(
                $result['children'],
                static function (array $child): bool {
                    return isset($child['uri']);
                }
            ))
        );

        // Equal case-folded keys fall back to uri for a stable order.
        $this->assertSame(['Banana', 'éclair', 'Éclair'], $labels);
    }
}

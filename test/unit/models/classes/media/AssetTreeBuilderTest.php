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
use oat\taoItems\model\media\AssetTreeBuilder;
use tao_helpers_Uri;
use oat\generis\test\TestCase;

class AssetTreeBuilderTest extends TestCase
{
    /** @var AssetTreeBuilder */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new AssetTreeBuilder();
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
        $search = $this->createMock(DirectorySearchQuery::class);
        $mediaAsset = $this->createMock(MediaAsset::class);
        $mediaSource = $this->createMock(MediaBrowser::class);

        $search->method('getAsset')
            ->willReturn($mediaAsset);

        $mediaAsset->method('getMediaSource')
            ->willReturn($mediaSource);

        $mediaSource->method('getDirectories')
            ->willReturn($data);

        $expectedData = [
            'children' => [
                [
                    'url' => tao_helpers_Uri::getRootUrl() . 'taoItems/ItemContent/files?uri=&lang=&1=parent',
                ],
                [
                    'url' => 'something'
                ]
            ],
        ];

        $this->assertEquals(
            $expectedData,
            $this->subject->build($search)
        );
    }
}

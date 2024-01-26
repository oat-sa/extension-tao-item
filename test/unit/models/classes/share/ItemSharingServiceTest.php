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
 * Copyright (c) 2024 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\test\unit\models\classes\share;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use oat\generis\test\ServiceManagerMockTrait;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\reporting\Report;
use oat\taoItems\model\share\ItemSharingService;
use oat\taoQtiItem\model\Export\QtiPackage22ExportHandler;
use PHPUnit\Framework\TestCase;

class ItemSharingServiceTest extends TestCase
{
    use ServiceManagerMockTrait;
    private $subject;
    public function setUp(): void
    {
        $this->exporterHandlerMock = $this->createMock(QtiPackage22ExportHandler::class);
        $this->fileSystemServiceMock = $this->createMock(FileSystemService::class);
        $this->fileSystemMock = $this->createMock(FilesystemInterface::class);

        $this->subject = new ItemSharingService(
            $this->exporterHandlerMock,
            $this->fileSystemServiceMock
        );
    }

    public function testShareItems()
    {
        $reportMock = $this->createMock(Report::class);

        $this->exporterHandlerMock
            ->expects($this->once())
            ->method('export')
            ->willReturn($reportMock);

        $reportMock
            ->expects($this->once())
            ->method('getData')
            ->willReturn('./');

        $this->fileSystemServiceMock
            ->expects($this->once())
            ->method('getFileSystem')
            ->willReturn($this->fileSystemMock);

        $this->fileSystemMock
            ->expects($this->once())
            ->method('putStream')
            ->willReturn(true);

        $result = $this->subject->shareItems(
            [
                ItemSharingService::PARAM_EXPORT_DATA => [
                    ItemSharingService::LABEL => 'Shared Resource Label',
                    ItemSharingService::INSTANCES => [
                        'http://tao.com/taoQtiItem.rdf#i1234',
                        'http://tao.com/taoQtiItem.rdf#i5678'
                    ],
                    ItemSharingService::DESTINATION => 'path/to/destination',
                    ItemSharingService::FILENAME => 'filename',
                    ItemSharingService::RESOURCE_URI => 'http://tao.com/taoQtiItem.rdf#i4321'
                ]
            ]
        );

        self::assertInstanceOf(Report::class, $result);
    }

    /**
     * @dataProvider invalidParamsProvider
     */
    public function testShareItemsWithException($params)
    {
        $this->exporterHandlerMock
            ->expects($this->never())
            ->method('export');

        $this->fileSystemServiceMock
            ->expects($this->never())
            ->method('getFileSystem');

        $this->fileSystemMock->expects($this->never())
            ->method('put');

        $this->expectException(InvalidArgumentException::class);
        $this->subject->shareItems($params);
    }

    public function invalidParamsProvider()
    {
        return [
            'Missing export data' => [
                [
                    ItemSharingService::LABEL => 'test',
                    ItemSharingService::INSTANCES => [
                        'test'
                    ]
                ]
            ],
            'Missing label' => [
                [
                    ItemSharingService::PARAM_EXPORT_DATA => [
                        ItemSharingService::INSTANCES => [
                            'test'
                        ]
                    ]
                ]
            ],
            'Missing instances' => [
                [
                    ItemSharingService::PARAM_EXPORT_DATA => [
                        ItemSharingService::LABEL => 'test',
                    ]
                ]
            ],
            'Missing destination' => [
                [
                    ItemSharingService::PARAM_EXPORT_DATA => [
                        ItemSharingService::LABEL => 'test',
                        ItemSharingService::INSTANCES => [
                            'test'
                        ]
                    ]
                ]
            ],
            'Missing filename' => [
                [
                    ItemSharingService::PARAM_EXPORT_DATA => [
                        ItemSharingService::LABEL => 'test',
                        ItemSharingService::INSTANCES => [
                            'test'
                        ],
                        ItemSharingService::DESTINATION => 'test'
                    ]
                ]
            ],
            'Missing resource uri' => [
                [
                    ItemSharingService::PARAM_EXPORT_DATA => [
                        ItemSharingService::LABEL => 'test',
                        ItemSharingService::INSTANCES => [
                            'test'
                        ],
                        ItemSharingService::DESTINATION => 'test',
                        ItemSharingService::FILENAME => 'test'
                    ]
                ]
            ]
        ];
    }
}

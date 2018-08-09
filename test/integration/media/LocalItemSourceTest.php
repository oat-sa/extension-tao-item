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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoItems\test\media;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoItems\model\media\LocalItemSource;
use oat\tao\model\media\MediaAsset;

/**
 * This class aims at testing LocalItemSource.
 *
 * @package taoItems
 */
class LocalItemSourceTest extends TaoPhpUnitTestRunner
{
    /** @var  \core_kernel_classes_Resource */
    protected $item;

    public function setUp()
    {
        parent::setUp();
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
    }

    public function tearDown()
    {
        if ($this->item) {
            $this->item->delete();
        }
        parent::tearDown();
    }

    public function testAdd()
    {
        $source = $this->getLocalItemSource();
        $info = $source->add($this->getSampleFile(), 'example.txt', '/');

	    $this->assertEquals('example.txt', $info['name']);
	    $this->assertEquals('example.txt', $info['uri']);
	    $this->assertEquals('example.txt', $info['filePath']);
    }

    public function testGetFileInfo()
    {
        $asset = $this->getMediaAsset();
        $source = $asset->getMediaSource();
        $info = $source->getFileInfo($asset->getMediaIdentifier());
        $this->assertEquals('example.txt', $info['uri']);
    }

	public function testDelete()
	{
	    $asset = $this->getMediaAsset();
	    $source = $asset->getMediaSource();
	    $success = $source->delete($asset->getMediaIdentifier());
	    $this->assertTrue($success);
	    try {
	        $source->getFileInfo($asset->getMediaIdentifier());
	        $this->fail('GetFileInfo on a deleted file should throw error');
	    } catch (\tao_models_classes_FileNotFoundException $e) {
	        // should not be found
	    }
	}

    protected function getMediaAsset()
    {
        $source = $this->getLocalItemSource();
        $info = $source->add($this->getSampleFile(), 'example.txt', '/');

        $link = $info['uri'];
        return new MediaAsset($source, $link);
    }

    protected function getSampleFile()
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR.'asset'.DIRECTORY_SEPARATOR.'sample.css';
    }

    protected function getLocalItemSource()
    {
        $itemService = \taoItems_models_classes_ItemsService::singleton();
        $this->item = $itemService->createInstance($itemService->getRootClass(), 'testItem');
        $source = new LocalItemSource(array(
            'item' => $this->item,
            'lang' => DEFAULT_LANG
        ));

        $this->setInaccessibleProperty($source, 'itemService', $this->getItemServiceMock());
        return $source;
    }

    protected function getItemServiceMock()
    {
        $itemServiceMock = $this->getMockBuilder(\taoItems_models_classes_ItemsService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $itemServiceMock->expects($this->any())
            ->method('getItemDirectory')
            ->will($this->returnValue($this->getTempDirectory()));
        return $itemServiceMock;
    }
}

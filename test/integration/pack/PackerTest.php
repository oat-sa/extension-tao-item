<?php

/*
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoItems\test\integration\pack;

use common_Exception;
use core_kernel_classes_Resource;
use oat\generis\test\TestCase;
use oat\oatbox\filesystem\Directory;
use oat\taoItems\model\pack\ItemPacker;
use oat\taoItems\model\pack\Packer;
use oat\taoItems\model\pack\ItemPack;

/**
 * Test the class {@link ItemPack}
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 */
class PackerTest extends TestCase
{
    public function setUp(): void
    {
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
    }

    /**
     * Test creating an ItemPack
     */
    public function testConstructor()
    {
        $item = new core_kernel_classes_Resource('toto');
        $packer = new Packer($item);
        $this->assertInstanceOf('oat\taoItems\model\pack\Packer', $packer);
    }

    /**
     * Test assigning assets to a pack
     */
    public function testPack()
    {
        $item = new core_kernel_classes_Resource('foo');
        $model = new core_kernel_classes_Resource('fooModel');

        $serviceMock = $this
                        ->getMockBuilder('\taoItems_models_classes_ItemsService')
                        ->disableOriginalConstructor()
                        ->getMock();

        $modelMock = $this
                        ->getMockBuilder('\taoItems_models_classes_itemModel')
                        ->getMock();

        $directoryMock = $this
            ->getMockBuilder(\tao_models_classes_service_StorageDirectory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $packerMock = new PackerMock();

        $modelMock
            ->method('getPackerClass')
            ->will($this->returnValue(get_class($packerMock)));

        $serviceMock
            ->method('getItemModel')
            ->will($this->returnValue(new core_kernel_classes_Resource('fooModel')));

        $serviceMock
            ->method('getItemModelImplementation')
            ->with($this->equalTo($model))
            ->will($this->returnValue($modelMock));

        $serviceMock
            ->method('singleton')
            ->will($this->returnValue($serviceMock));


        $packer = $this->getMockBuilder(Packer::class)
            ->setConstructorArgs([$item])
            ->setMethods(['getStorageDirectory'])
            ->getMock();

        $prop = new \ReflectionProperty(Packer::class, 'itemService');
        $prop->setAccessible(true);
        $prop->setValue($packer, $serviceMock);

        $packer
            ->method('getStorageDirectory')
            ->will($this->returnValue($directoryMock));

        $result = $packer->pack();
        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $result);
        $this->assertEquals('qti', $result->getType());
        $this->assertEquals(['uri' => $item->getUri()], $result->getData());
    }

    /**
     * Test the exception chain when the item has no model
     *
     */
    public function testNoItemModel()
    {
        $this->expectException(common_Exception::class);
        $item = new core_kernel_classes_Resource('foo');

        $serviceMock = $this
                        ->getMockBuilder('\taoItems_models_classes_ItemsService')
                        ->disableOriginalConstructor()
                        ->getMock();

        $serviceMock
            ->method('getItemModel')
            ->will($this->returnValue(null));

        $serviceMock
            ->method('singleton')
            ->will($this->returnValue($serviceMock));


        $packer = new Packer($item);

        $prop = new \ReflectionProperty('oat\taoItems\model\pack\Packer', 'itemService');
        $prop->setAccessible(true);
        $prop->setValue($packer, $serviceMock);

        $packer->pack();
    }

    /**
     * Test the exception chain when there is no implementations for a model
     */
    public function testNoModelImplementation()
    {
        $this->expectException(common_Exception::class);
        $item = new core_kernel_classes_Resource('foo');
        $model = new core_kernel_classes_Resource('fooModel');

        $serviceMock = $this
                        ->getMockBuilder('\taoItems_models_classes_ItemsService')
                        ->disableOriginalConstructor()
                        ->getMock();

        $serviceMock
            ->method('getItemModel')
            ->will($this->returnValue($model));

        $serviceMock
            ->method('getItemModelImplementation')
            ->with($this->equalTo($model))
            ->will($this->returnValue(null));

        $serviceMock
            ->method('singleton')
            ->will($this->returnValue($serviceMock));


        $packer = new Packer($item);

        $prop = new \ReflectionProperty('oat\taoItems\model\pack\Packer', 'itemService');
        $prop->setAccessible(true);
        $prop->setValue($packer, $serviceMock);

        $packer->pack();
    }

    /**
     * Test the exception chain when the model does not return a correct packer class
     *
     */
    public function testNoPackerClass()
    {
        $this->expectException(common_Exception::class);
        $item = new core_kernel_classes_Resource('foo');

        $serviceMock = $this
                        ->getMockBuilder('\taoItems_models_classes_ItemsService')
                        ->disableOriginalConstructor()
                        ->getMock();

        $modelMock = $this
                        ->getMockBuilder('\taoItems_models_classes_itemModel')
                        ->getMock();


        $modelMock
            ->method('getPackerClass')
            ->will($this->returnValue(null));

        $serviceMock
            ->method('getItemModel')
            ->will($this->returnValue(new core_kernel_classes_Resource('fooModel')));

        $serviceMock
            ->method('getItemModelImplementation')
            ->will($this->returnValue($modelMock));

        $serviceMock
            ->method('singleton')
            ->will($this->returnSelf());


        $packer = new Packer($item);

        $prop = new \ReflectionProperty('oat\taoItems\model\pack\Packer', 'itemService');
        $prop->setAccessible(true);
        $prop->setValue($packer, $serviceMock);

        $packer->pack();
    }

    /**
     * Test the exception chain when the model returns a wrong packer class
     *
     */
    public function testWrongPackerClass()
    {
        $this->expectException(common_Exception::class);
        $item = new core_kernel_classes_Resource('foo');

        $serviceMock = $this
                        ->getMockBuilder('\taoItems_models_classes_ItemsService')
                        ->disableOriginalConstructor()
                        ->getMock();

        $modelMock = $this
                        ->getMockBuilder('\taoItems_models_classes_itemModel')
                        ->getMock();

        $modelMock
            ->method('getPackerClass')
            ->will($this->returnValue("stdClass"));

        $serviceMock
            ->method('getItemModel')
            ->will($this->returnValue(new core_kernel_classes_Resource('fooModel')));

        $serviceMock
            ->method('getItemModelImplementation')
            ->will($this->returnValue($modelMock));

        $serviceMock
            ->method('singleton')
            ->will($this->returnSelf());


        $packer = new Packer($item);

        $prop = new \ReflectionProperty('oat\taoItems\model\pack\Packer', 'itemService');
        $prop->setAccessible(true);
        $prop->setValue($packer, $serviceMock);

        $packer->pack();
    }
}

//use an old school mock as the Packer create it's own instance from the class
class PackerMock extends ItemPacker
{
    public function packItem(core_kernel_classes_Resource $item, $lang, Directory $directory)
    {
        return new ItemPack('qti', ['uri' => $item->getUri()]);
    }
}

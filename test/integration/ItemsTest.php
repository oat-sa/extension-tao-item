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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2016 (update and modification) Open Assessment Technologies SA
 */

namespace oat\taoItems\test\integration;

use oat\generis\test\GenerisTestCase;
use oat\tao\model\TaoOntology;
use oat\generis\model\OntologyRdfs;
use oat\tao\test\TaoPhpUnitTestRunner;
use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use oat\taoItems\model\ItemModelStatus;
use oat\taoQtiItem\model\ItemModel;
use taoItems_models_classes_itemModel;
use taoItems_models_classes_ItemsService;

/**
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 */
class ItemsTest extends GenerisTestCase
{
    /**
     * @var \taoItems_models_classes_ItemsService
     */
    private $itemsService;

    /**
     * @var \core_kernel_persistence_smoothsql_SmoothModel
     */
    private $ontologyMock;

    /**
     * tests initialization
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');

        $this->ontologyMock = $this->getOntologyMock();

        $this->itemsService = \taoItems_models_classes_ItemsService::singleton();
        $this->itemsService->setModel($this->ontologyMock);
    }

    /**
     * Test the user service implementation
     * @see tao_models_classes_ServiceFactory::get
     * @see taoItems_models_classes_ItemsService::__construct
     */
    public function testService()
    {
        $this->assertInstanceOf(\tao_models_classes_Service::class, $this->itemsService);
        $this->assertInstanceOf(taoItems_models_classes_ItemsService::class, $this->itemsService);
    }

    /**
     * @return \core_kernel_classes_Class|null
     */
    public function testClassCreate()
    {
        $ItemClass = $this->itemsService->getRootClass();
        $this->assertInstanceOf(\core_kernel_classes_Class::class, $ItemClass);
        $this->assertEquals(TaoOntology::ITEM_CLASS_URI, $ItemClass->getUri());

        return $ItemClass;
    }

    /**
     * @depends testClassCreate
     * @param $ItemClass
     * @return \core_kernel_classes_Class
     */
    public function testSubClassCreate($ItemClass)
    {
        $subItemClassLabel = 'subItem class';
        $subItemClass = $this->itemsService->createSubClass($ItemClass, $subItemClassLabel);
        $this->assertInstanceOf(\core_kernel_classes_Class::class, $subItemClass);
        $this->assertEquals($subItemClassLabel, $subItemClass->getLabel());

        return $subItemClass;
    }

    /**
     * @depends testClassCreate
     * @param $class
     * @return \core_kernel_classes_Resource
     */
    public function testInstantiateClass($class)
    {
        $ItemInstanceLabel = 'Item instance';

        return $this->instantiateClass($class, $ItemInstanceLabel);
    }

    /**
     * @depends testSubClassCreate
     * @param $class
     * @return \core_kernel_classes_Resource
     */
    public function testInstantiateSubClass($class)
    {
        $subItemInstanceLabel = 'subItem instance';

        return $this->instantiateClass($class, $subItemInstanceLabel);
    }

    /**
     * @param $class
     * @param $label
     * @return \core_kernel_classes_Resource
     */
    protected function instantiateClass($class, $label)
    {
        $instance = $this->itemsService->createInstance($class, $label);
        $this->assertInstanceOf(core_kernel_classes_Resource::class, $instance);
        $this->assertEquals($label, $instance->getLabel());

        $instance->removePropertyValues($this->createTestProperty(OntologyRdfs::RDFS_LABEL));
        $instance->setLabel($label);


        $this->assertInstanceOf(core_kernel_classes_Resource::class, $instance);
        $this->assertEquals($label, $instance->getLabel());

        return $instance;
    }


    /**
     * @depends testInstantiateClass
     * @param \core_kernel_classes_Resource $instance
     */
    public function testItemContent($instance)
    {
        $this->assertFalse($this->itemsService->hasItemModel($instance, array(ItemModel::MODEL_URI)));
        $this->assertFalse($this->itemsService->hasItemContent($instance));

        $instance->setPropertyValue(
            $this->createTestProperty(taoItems_models_classes_ItemsService::PROPERTY_ITEM_MODEL),
            ItemModel::MODEL_URI
        );

        $directory = $this->itemsService->getItemDirectory($instance);
        $this->assertTrue($directory->getFile('qti.xml')->write('test'));

        $this->assertTrue($this->itemsService->hasItemContent($instance));

        $this->assertStringStartsWith(LOCAL_NAMESPACE, $instance->getUri());
        $this->assertTrue($this->itemsService->hasItemModel($instance, array(ItemModel::MODEL_URI)));

        $this->assertStringStartsWith(ROOT_URL, $this->itemsService->getPreviewUrl($instance));

        $this->assertEquals('oat\taoQtiItem\model\QtiItemCompiler', $this->itemsService->getCompilerClass($instance));

        $this->assertEquals(count($this->itemsService->getAllByModel($instance)), 0);
        $this->assertEquals(count($this->itemsService->getAllByModel(null)), 0);

        $this->assertFalse($this->itemsService->hasModelStatus($instance, array(ItemModelStatus::INSTANCE_DEPRECATED)));
    }

    public function testIsItemClass()
    {
        $clazz = $this->prophesize('core_kernel_classes_Class');
        $clazz->getUri()->willReturn(TaoOntology::ITEM_CLASS_URI);
        $this->assertTrue($this->itemsService->isItemClass($clazz->reveal()));


        $clazz = $this->prophesize('core_kernel_classes_Class');
        $clazz->getUri()->willReturn('uri');

        $parent = $this->prophesize('core_kernel_classes_Class');
        $parent->getUri()->willReturn(TaoOntology::ITEM_CLASS_URI);

        $clazz->getParentClasses(true)->willReturn(array($parent->reveal()));
        $this->assertTrue($this->itemsService->isItemClass($clazz->reveal()));
    }

    public function testGetModelRuntime()
    {

        $item = $this->ontologyMock->getResource('resource');
        $itemModel = $this->ontologyMock->getResource(taoItems_models_classes_ItemsService::PROPERTY_ITEM_MODEL);

        $itemModel->setPropertyValue(
            $this->createTestProperty(taoItems_models_classes_itemModel::CLASS_URI_RUNTIME),
            'returnValue'
        );
        $item->setPropertyValue(
            $this->createTestProperty(taoItems_models_classes_ItemsService::PROPERTY_ITEM_MODEL),
            $itemModel
        );

        $this->assertEquals('returnValue', $this->itemsService->getModelRuntime($item));
    }

    public function testGetItemModel()
    {
        $item = $this->ontologyMock->getResource('item');
        $this->assertNull($this->itemsService->getItemModel($item));

        $model = $this->ontologyMock->getResource(taoItems_models_classes_ItemsService::PROPERTY_ITEM_MODEL);
        $item->setPropertyValue(
            $this->createTestProperty(taoItems_models_classes_ItemsService::PROPERTY_ITEM_MODEL),
            $model
        );
        $this->assertEquals($model->getUri(), $this->itemsService->getItemModel($item)->getUri());
    }

    public function testGetPreviewUrl()
    {
        $item = $this->prophesize('core_kernel_classes_Resource');
        $itemModelProphecy = $this->prophesize('core_kernel_classes_Resource');

        $itemModelProphecy->getPropertyValues($this->createTestProperty(taoItems_models_classes_ItemsService::PROPERTY_ITEM_MODEL_SERVICE))
            ->willReturn(array());

        $this->assertNull($this->itemsService->getPreviewUrl($item->reveal()));
    }

    public function testGetItemModelImplementation()
    {
        $item = $this->ontologyMock->getResource('item');
        $property = $this->createTestProperty(taoItems_models_classes_ItemsService::PROPERTY_ITEM_MODEL_SERVICE);

        $item->setPropertyValue($property, 'fakeUri');

        try {
            $this->itemsService->getItemModelImplementation($item);
            $this->fail('an exception should have been raised');
        }
        catch (\common_Exception $e) {
            $this->assertInstanceOf('common_exception_Error', $e);
            $this->assertEquals('Item model service fakeUri not found', $e->getMessage());
        }
    }

    public function testIsItemModelDefined()
    {
        $item = $this->ontologyMock->getResource('item');

        $this->assertFalse($this->itemsService->isItemModelDefined($item));

        $property = $this->createTestProperty(taoItems_models_classes_ItemsService::PROPERTY_ITEM_MODEL);

        $item->setPropertyValue($property, 'notnull');
        $this->assertTrue($this->itemsService->isItemModelDefined($item));

        $item->setPropertyValue($property, new \core_kernel_classes_Literal('notnull'));
        $this->assertTrue($this->itemsService->isItemModelDefined($item));
    }

    /**
     * @depends testInstantiateClass
     * @param \core_kernel_classes_Resource $instance
     */
    public function testClone($instance)
    {
        $this->itemsService->setItemModel($instance, new core_kernel_classes_Resource(ItemModel::MODEL_URI));
        $clone = $this->itemsService->cloneInstance($instance);
        $this->assertNotSame($clone, $instance);
        $this->assertTrue($this->itemsService->deleteResource($clone));
    }


    /**
     * @depends testSubClassCreate
     * @param $class
     */
    public function testDeleteClass($class)
    {
        $this->assertTrue($this->itemsService->deleteClass($class));
    }

    /**
     * @depends testInstantiateClass
     * @param \core_kernel_classes_Resource $instance
     */
    public function testDeleteInstance($instance)
    {
        $this->assertTrue($this->itemsService->deleteResource($instance));
        $this->assertFalse($instance->exists());
    }

    /**
     * @param string $type
     * @return core_kernel_classes_Property
     */
    private function createTestProperty($type)
    {
        return $this->ontologyMock->getProperty($type);
    }
}

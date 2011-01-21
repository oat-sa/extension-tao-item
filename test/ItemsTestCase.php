<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/bootstrap.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class ItemsTestCase extends UnitTestCase {
	
	/**
	 * 
	 * @var taoItems_models_classes_ItemsService
	 */
	protected $itemsService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TestRunner::initTest();
	}
	
	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoItems_models_classes_ItemsService::__construct
	 */
	public function testService(){
		
		$itemsService = tao_models_classes_ServiceFactory::get('Items');
		$this->assertIsA($itemsService, 'tao_models_classes_Service');
		$this->assertIsA($itemsService, 'taoItems_models_classes_ItemsService');
		
		$this->itemsService = $itemsService;
	}
	
	/**
	 * Usual CRUD (Create Read Update Delete) on the item class  
	 */
	public function testCrud(){
		
		//check parent class
		$this->assertTrue(defined('TAO_ITEM_CLASS'));
		$itemClass = $this->itemsService->getItemClass();
		$this->assertIsA($itemClass, 'core_kernel_classes_Class');
		$this->assertEqual(TAO_ITEM_CLASS, $itemClass->uriResource);
		
		//create a subclass
		$subItemClassLabel = 'subItem class';
		$subItemClass = $this->itemsService->createSubClass($itemClass, $subItemClassLabel);
		$this->assertIsA($subItemClass, 'core_kernel_classes_Class');
		$this->assertEqual($subItemClassLabel, $subItemClass->getLabel());
		$this->assertTrue($this->itemsService->isItemClass($subItemClass));
		
		//create an instance of the Item class
		$itemInstanceLabel = 'item instance';
		$itemInstance = $this->itemsService->createInstance($itemClass, $itemInstanceLabel);
		$this->assertIsA($itemInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($itemInstanceLabel, $itemInstance->getLabel());
		
		//create instance of subItem
		$subItemInstanceLabel = 'subItem instance';
		$subItemInstance = $this->itemsService->createInstance($subItemClass, $subItemInstanceLabel);
		$this->assertIsA($subItemInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($subItemInstanceLabel, $subItemInstance->getLabel());
		
		$subItemInstanceLabel2 = 'my sub item instance';
		$subItemInstance->setLabel($subItemInstanceLabel2);
		$this->assertEqual($subItemInstanceLabel2, $subItemInstance->getLabel());
		
		//delete group instance
		$this->assertTrue($itemInstance->delete());
		
		//delete subclass and check if the instance is deleted
		$subItemInstanceUri = $subItemInstance->uriResource;
		$this->assertNotNull($this->itemsService->getItem($subItemInstanceUri));
		$this->assertTrue($subItemInstance->delete());
		$this->assertNull($this->itemsService->getItem($subItemInstanceUri));
		
		$this->assertTrue($subItemClass->delete());
	}
	
	public function testItemContent(){
		//create an instance of the Item class
		$itemClass = $this->itemsService->getItemClass();
		$item = $this->itemsService->createInstance($itemClass, 'test content');
		$this->assertIsA($item, 'core_kernel_classes_Resource');
		$this->assertEqual('test content', $item->getLabel());
		
		$item->setPropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_XHTML);
		
		$this->assertFalse($this->itemsService->hasItemContent($item));
		
		$itemContentProperty = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
		$this->assertIsA($itemContentProperty, 'core_kernel_classes_Property');
		
		$file = core_kernel_classes_File::create('test.txt');
		$this->assertTrue(core_kernel_classes_File::isFile($file));
		
		
		$this->assertTrue(file_put_contents($file->getAbsolutePath(), 'test') > 0);
		
		$item->setPropertyValue($itemContentProperty, $file->uriResource);
		
		$content = $item->getOnePropertyValue($itemContentProperty);
		
		$this->assertTrue(core_kernel_classes_File::isFile($content));
		
		$gotFile = new core_kernel_classes_File($content->uriResource);
		
		$this->assertEqual($gotFile->getAbsolutePath(), $file->getAbsolutePath());
			
		$this->assertTrue($this->itemsService->setItemContent($item, 'test 2'));
		$this->assertEqual('test 2', $this->itemsService->getItemContent($item));
		
		$this->assertTrue($this->itemsService->setItemContent($item, 'test FR', 'FR'));
		$this->assertEqual('test FR', $this->itemsService->getItemContent($item, false, 'FR'));
		
		$this->assertTrue($this->itemsService->deleteItem($item));
	}
}
?>
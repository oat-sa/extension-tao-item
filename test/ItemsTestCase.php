<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

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
		$subItemInstance = $this->itemsService->createInstance($subItemClass);
		$this->assertTrue(defined('RDFS_LABEL'));
		$subItemInstance->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
		$subItemInstance->setPropertyValue(new core_kernel_classes_Property(RDFS_LABEL), $subItemInstanceLabel);
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
}
?>
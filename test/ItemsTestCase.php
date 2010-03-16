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
	

}
?>
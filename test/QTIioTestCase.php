<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class QTIioTestCase extends UnitTestCase {
	
	protected $qtiService;
	protected $itemService;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TestRunner::initTest();
		$this->qtiService  = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
		$this->itemService = tao_models_classes_ServiceFactory::get("items");
	}
	
	
	/**
	 * test basically the import and deployment of QTI items
	 */
	public function testDeploy(){
		
		taoItems_models_classes_QTI_Data::setPersistance(false);

		foreach(glob(dirname(__FILE__).'/samples/*.xml') as $file){	
		
			$qtiItem = $this->qtiService->loadItemFromFile($file);
			$this->assertNotNull($qtiItem);
			$this->assertIsA($qtiItem, 'taoItems_models_classes_QTI_Item');
			
			$rdfItem = $this->itemService->createInstance($this->itemService->getItemClass());
			$this->assertNotNull($rdfItem);
			$this->assertIsA($rdfItem, 'core_kernel_classes_Resource');
			
			$rdfItem->setPropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_QTI);
			$this->assertTrue($this->itemService->hasItemModel($rdfItem, array(TAO_ITEM_MODEL_QTI)));
			
			$this->assertTrue($this->qtiService->saveDataItemToRdfItem($qtiItem, $rdfItem));
			
			//Deploy it
			$url = $this->itemService->deployItem($rdfItem);
			
			$this->assertTrue(!empty($url));

			$folder = dirname(str_replace(BASE_WWW, BASE_PATH . '/views/', $url));
			$this->assertTrue(is_dir($folder));
			
			//echo "<br /><iframe width='800px' height='300px' src='$url'></iframe><br />";
			
			$this->assertTrue($this->itemService->deleteItem($rdfItem));
			@tao_helpers_File::remove($folder, true);
		}
	}

}
?>
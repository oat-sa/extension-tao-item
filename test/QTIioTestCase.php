<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

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
		$this->itemService = tao_models_classes_ServiceFactory::get("Items");
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
			
			$deployParams = array(
				'delivery_server_mode'	=> false,
				'matching_server'		=> false,
				'qti_lib_www'			=> BASE_WWW .'js/QTI/',
				'qti_base_www'			=> BASE_WWW .'js/QTI/'
			);
			
			$folderName = substr($rdfItem->uriResource, strpos($rdfItem->uriResource, '#') + 1);
        	$itemFolder = ROOT_PATH . '/taoItems/views/runtime/' . $folderName;
        	$itemPath = "{$itemFolder}/index.html";
			if(!is_dir($itemFolder)){
        		mkdir($itemFolder);
        	}
        	$itemUrl = str_replace(BASE_PATH .'/views', BASE_WWW, $itemPath);
        	
        	//deploy the item
        	$this->assertTrue($this->itemService->deployItem($rdfItem, $itemPath, $itemUrl,  $deployParams));
			
			$this->assertTrue(!empty($itemUrl));
			$this->assertTrue(is_dir($itemFolder));
			
			//echo "<br /><iframe width='900px' height='400px' src='$itemUrl'></iframe><br />";
			
			$this->assertTrue($this->itemService->deleteItem($rdfItem));
			@tao_helpers_File::remove($itemPath, true);
		}
	}

}
?>
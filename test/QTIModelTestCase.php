<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class QTIModelTestCase extends UnitTestCase {
	
	protected $qtiService;
	
	/**
	 * tests initialization
	 * load qti service
	 */
	public function setUp(){		
		TestRunner::initTest();
		$this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
	}
	
	
	/**
	 * test the QTI objects persistance
	 */
	public function testPersitance(){
		
		taoItems_models_classes_QTI_Data::setPersistance(true);
		
		//load an item
		$qtiParser = new taoItems_models_classes_QTI_Parser(dirname(__FILE__).'/samples/choice_multiple.xml');
		$item = $qtiParser->load();
		
		$this->assertTrue($qtiParser->isValid());
		$this->assertNotNull($item);
		$this->assertIsA($item, 'taoItems_models_classes_QTI_Item');
		
		$serial = $item->getSerial();
		
		//item is saved by destruction 
		unset($item);

		$savedItem = $this->qtiService->getItemBySerial($serial);
		$this->assertNotNull($savedItem);
		$this->assertIsA($savedItem, 'taoItems_models_classes_QTI_Item');
		
		//real remove
		taoItems_models_classes_QTI_Data::setPersistance(false);
		unset($savedItem);
		
		$this->assertNull($this->qtiService->getItemBySerial($serial));
	}
	
	/**
	 * test the building of item from all the samples
	 */
	public function testSamples(){
		//check if samples are loaded 
		foreach(glob(dirname(__FILE__).'/samples/*.xml') as $file){	
//			$file = dirname(__FILE__).'/samples/associate.xml';
			$qtiParser = new taoItems_models_classes_QTI_Parser($file);
			$item = $qtiParser->load();
			
			$this->assertTrue($qtiParser->isValid());
			$this->assertNotNull($item);
			$this->assertIsA($item, 'taoItems_models_classes_QTI_Item');
			
			foreach($item->getInteractions() as $interaction){
				$this->assertIsA($interaction, 'taoItems_models_classes_QTI_Interaction');
				
				foreach($interaction->getChoices() as $choice){
					$this->assertIsA($choice, 'taoItems_models_classes_QTI_Choice');
				}
			}
//			print "<pre>";
//			print_r($item);
//			print "</pre>";
		}
	}
	
}
?>
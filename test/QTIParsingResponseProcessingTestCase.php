<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class QTIParsingResponseProcessingTestCase extends UnitTestCase {
	
	protected $qtiService;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TestRunner::initTest();
		$this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
	}
	
	
	/**
	 * test qti file parsing: validation and loading in a non-persistant context
	 */
	public function testFileParsing(){
		return;
		taoItems_models_classes_QTI_Data::setPersistance(false);
		
		//check if samples are loaded
		$file = dirname(__FILE__).'/samples/choice.xml'; 

		$qtiParser = new taoItems_models_classes_QTI_Parser($file);
		$qtiParser->validate();
		
		$this->assertTrue($qtiParser->isValid());
		$item = $qtiParser->load();
   //     echo '<pre>'; print_r( $item->getResponseProcessing ()); echo '</pre>';
//        echo '<pre>'; print_r ($item); echo '</pre>';
		
//		$qtiRes = $item->toQTI ();
		
		$this->assertIsA($item, 'taoItems_models_classes_QTI_Item');
	}
	
	/**
	 * test the building an QTI_Item object from it's XML definition
	 */
//	public function testBuilding(){
//		
//		taoItems_models_classes_QTI_Data::setPersistance(false);
//		
//		$file = dirname(__FILE__).'/samples/choice.xml'; 
//		$qtiParser = new taoItems_models_classes_QTI_Parser($file);
//		$item = $qtiParser->load();
//		
//		$this->assertTrue($qtiParser->isValid());
//		$this->assertNotNull($item);
//		$this->assertIsA($item, 'taoItems_models_classes_QTI_Item');
//		
////		echo '<pre>';
////		print_r ($item);
////		echo '</pre>';
//		
//		foreach($item->getInteractions() as $interaction){
//			$this->assertIsA($interaction, 'taoItems_models_classes_QTI_Interaction');
//			
//			foreach($interaction->getChoices() as $choice){
//				$this->assertIsA($choice, 'taoItems_models_classes_QTI_Choice');
//			}
//		}
//	}
	
}
?>
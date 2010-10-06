<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class QTIOutputTestCase extends UnitTestCase {
	
	protected $qtiService;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TestRunner::initTest();
		$this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
	}
	
	
	/**
	 * test the building and exporting out the items
	 */
	public function testToQTI(){
		return;
		taoItems_models_classes_QTI_Data::setPersistance(false);

		foreach(glob(dirname(__FILE__).'/samples/*.xml') as $file){	
		
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
			
			//test if content has been exported
			$qti = $item->toQTI();
			$this->assertFalse(empty($qti));
			

			//test if it's a valid QTI file
			$tmpFile = dirname(__FILE__).'/samples/'.uniqid('qti_', true).'.xml';
			file_put_contents($tmpFile, $qti);
			$this->assertTrue(file_exists($tmpFile));
			
			$parserValidator = new taoItems_models_classes_QTI_Parser($tmpFile);
			$parserValidator->validate();
			
			if(!$parserValidator->isValid()){
				$this->fail($parserValidator->displayErrors());
			}
			else{
				@unlink($tmpFile);
				$this->assertFalse(file_exists($tmpFile));
			}
			
		}
	}
	
	/**
	 * test the building and exporting out the items
	 */
	public function testToXHTML(){
		
		taoItems_models_classes_QTI_Data::setPersistance(false);

		$files = array(
			dirname(__FILE__).'/samples/associate.xml',
//			dirname(__FILE__).'/samples/choice_multiple.xml',
//			dirname(__FILE__).'/samples/choice.xml',
//			dirname(__FILE__).'/samples/order.xml',
//			dirname(__FILE__).'/samples/text_entry.xml',
//			dirname(__FILE__).'/samples/extended_text.xml',
//			dirname(__FILE__).'/samples/inline_choice.xml',
//			dirname(__FILE__).'/samples/hottext.xml',
			dirname(__FILE__).'/samples/gap_match.xml'
		);
		
		foreach($files as $file){	
			
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
			
			$xhtml =  $item->toXHTML();
			
			//test if content has been exported
			$this->assertFalse(empty($xhtml));
			
			//test if it's a valid QTI file
			$qtiType = str_replace('.xml', '', basename($file));
			$tmpFile = BASE_PATH.'/views/runtime/'.uniqid('qti_'.$qtiType, true).'.html';
			file_put_contents($tmpFile, $xhtml);
			
			echo "<strong>$qtiType</strong><br/>";
			echo "<iframe src='".str_replace(BASE_PATH, BASE_URL, $tmpFile)."' width='800px' height='500px'></iframe><br/><br/>";
			
		}
	}
	
}
?>
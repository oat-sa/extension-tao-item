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
		
		$parameters = array(
			'root_url' => ROOT_URL,
        	'base_www' => BASE_WWW,
        	'taobase_www' => TAOBASE_WWW,
        	'raw_preview'	=> false
		);
		taoItems_models_classes_QTI_TemplateRenderer::setContext($parameters, 'ctx_');
		
		$this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
	}
	
	
	/**
	 * test the building and exporting out the items
	 */
	public function testToQTI(){
		
		taoItems_models_classes_QTI_Data::setPersistance(false);

        //$file = dirname(__FILE__).'/samples/tags.xml';
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
			
			$xhtml =  $item->toXHTML();
			
			//test if content has been exported
			$this->assertFalse(empty($xhtml));
		}
	}

}
?>
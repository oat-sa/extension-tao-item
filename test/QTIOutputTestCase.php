<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

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
			'root_url' 		=> ROOT_URL,
        	'base_www' 		=> BASE_WWW,
        	'taobase_www' 	=> TAOBASE_WWW,
			'qti_lib_www'			=> BASE_WWW .'js/QTI/',
			'qti_base_www'			=> BASE_WWW .'js/QTI/',
        	'raw_preview'	=> false,
        	'debug'			=> false
		);
		taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');
		
		$this->qtiService = taoItems_models_classes_QTI_Service::singleton();
	}
	
	
	/**
	 * test the building and exporting out the items
	 */
	public function testToQTI(){
		
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
			$tmpFile = dirname(__FILE__).'/samples/tmp/'.uniqid('qti_', true).'.xml';
			file_put_contents($tmpFile, $qti);
			$this->assertTrue(file_exists($tmpFile));
			
			$parserValidator = new taoItems_models_classes_QTI_Parser($tmpFile);
			$parserValidator->validate();
			
			if(!$parserValidator->isValid()){
				$this->fail($parserValidator->displayErrors());
			}
                        
                        @unlink($tmpFile);
                        $this->assertFalse(file_exists($tmpFile));
                        
			
		}
	}
	
	/**
	 * test the building and exporting out the items
	 */
	public function testToXHTML(){
		
		taoItems_models_classes_QTI_Data::setPersistance(false);
		$doc = new DOMDocument();
		$doc->validateOnParse = true;
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
			$xhtml =  $item->toXHTML();
		
			$this->assertFalse(empty($xhtml));
			
			try{
				$doc->loadHTML($xhtml);
			}
			catch(DOMException $de){
				$this->fail($de);
			}
		}
	}

}
?>
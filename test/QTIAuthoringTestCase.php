<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class QTIAuthoringTestCase extends UnitTestCase {
	
	/**
	 * tests initialization
	 * load qti service
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
	}
	
	/**
	 * test the building of item from all the samples
	 */
	public function testSamples(){
		
		//check if samples are loaded 
		foreach(glob(dirname(__FILE__).'/samples/*.xml') as $file){	

			$qtiParser = new taoItems_models_classes_QTI_Parser($file);
			$item = $qtiParser->load();
			
			$this->assertTrue($qtiParser->isValid());
			$this->assertNotNull($item);
			$this->assertIsA($item, 'taoItems_models_classes_QTI_Item');
			
			foreach($item->getInteractions() as $interaction){
				$this->assertIsA($interaction, 'taoItems_models_classes_QTI_Interaction');
				
				// ensure the order of all choices supporting it can be restored
				$this->assertIsA(taoItems_models_classes_QtiAuthoringService::singleton()->getInteractionChoices($interaction), 'array');
				/*foreach ( as $choice) {
					$this->assertIsA($choice, 'taoItems_models_classes_QTI_Choice', 'Got non choice('.gettype($choice).') for item '.basename($file));
				}*/
			}
		}
	}
	
}
?>
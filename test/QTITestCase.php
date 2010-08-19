<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class QTITestCase extends UnitTestCase {
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TestRunner::initTest();
	}
	
	
	public function testFileParsing(){
		return;
		
		foreach(glob(dirname(__FILE__).'/samples/wrong/*.*') as $file){
			
			$qtiParser = new taoItems_models_classes_QTI_Parser($file);
			
			$qtiParser->validate();
			
			$this->assertFalse($qtiParser->isValid());
			$this->assertTrue(count($qtiParser->getErrors()) > 0);
		}
		
		foreach(glob(dirname(__FILE__).'/samples/*.xml') as $file){
			
			$qtiParser = new taoItems_models_classes_QTI_Parser($file);
			$qtiParser->validate();
			
			$this->assertTrue($qtiParser->isValid());
			
			$item = $qtiParser->load();
			
			$this->assertIsA($item, 'taoItems_models_classes_QTI_Item');
		}
	}
	
	public function testBuilding(){
		
		$qtiParser = new taoItems_models_classes_QTI_Parser(dirname(__FILE__).'/samples/choice.xml');
		
		$item = $qtiParser->load();
		
		$this->assertTrue($qtiParser->isValid());
		$this->assertNotNull($item);
		$this->assertIsA($item, 'taoItems_models_classes_QTI_Item');
		
		$serializedItem = serialize($item);
		
		$this->assertTrue( !empty($serializedItem) );
		
		
		$item = unserialize($serializedItem);
		
		$this->assertNotNull($item);
		$this->assertIsA($item, 'taoItems_models_classes_QTI_Item');
		
		
		
	}
}
?>
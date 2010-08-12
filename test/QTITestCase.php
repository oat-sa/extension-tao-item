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
	
	
	public function testParser(){
		foreach(glob(dirname(__FILE__).'/samples/*.xml') as $file){
			$this->assertTrue(file_exists($file) && is_readable($file));
			
			$qtiParser = new taoItems_models_classes_QTI_Parser();
			$qtiParser->load($file);
		}

		
	}
	
}
?>
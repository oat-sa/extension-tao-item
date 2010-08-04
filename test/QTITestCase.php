<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/constants.php';

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
		$qtiParser = new taoItems_models_classes_QTI_Parser();
		$qtiParser->load(dirname(__FILE__).'/samples/choice.xml');
	}
	
}
?>
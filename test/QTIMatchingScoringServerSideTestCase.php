<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';
require_once dirname(__FILE__) . '/qti_labs/server/qti_api.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class QTIOMatchingScoringServerSideTestCase extends UnitTestCase {
	
	protected $qtiService;
	protected $qtiMSApi;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TestRunner::initTest();
		$this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
	
//		$this->qtiMSApi = new QtiMSApi ();
//		$this->rules = array ();
//		$this->rules['match0_1'] = "match(getVariable('RESPONSE0_1'), getCorrect('RESPONSE0_1'))";
	}
	
	public function test0 () {
		global $rules;

		eval ("\$res = ".$rules['match0_1'].";");
		$this->assertTrue($res);
		
		eval ("\$res = ".$rules['match0_2'].";");
		$this->assertFalse($res);
		
		eval ("\$res = ".$rules['match0_3'].";");
		$this->assertFalse($res);
	}
	
	public function test1 () {
		global $rules;

		eval ("\$res = ".$rules['match1_1'].";");
		$this->assertTrue($res);
		
		eval ("\$res = ".$rules['match1_2'].";");
		$this->assertFalse($res);
		
		eval ("\$res = ".$rules['match1_3'].";");
		$this->assertFalse($res);
		
		eval ("\$res = ".$rules['match1_4'].";");
		$this->assertFalse($res);
	}
	
	public function test2 () {
		global $rules;

		eval ("\$res = ".$rules['match2_1'].";");
		$this->assertTrue($res);
		
		eval ("\$res = ".$rules['match2_2'].";");
		$this->assertFalse($res);
		
		eval ("\$res = ".$rules['match2_3'].";");
		$this->assertFalse($res);
		
		echo $rules['match2_4'];
		eval ("\$res = ".$rules['match2_4'].";");
		$this->assertTrue($res);
	}
	
	public function test3 () {
		global $rules;

		eval ("\$res = ".$rules['match3_1'].";");
		$this->assertTrue($res);
		
		eval ("\$res = ".$rules['match3_2'].";");
		$this->assertFalse($res);
		
		eval ("\$res = ".$rules['match3_3'].";");
		$this->assertFalse($res);
		
		echo $rules['match3_4'];
		eval ("\$res = ".$rules['match3_4'].";");
		$this->assertTrue($res);
	}
	
	public function test6 () {
		global $rules;

		eval ("\$res = ".$rules['match6_1'].";");
		$this->assertFalse($res);
		
		eval ("\$res = ".$rules['match6_2'].";");
		$this->assertTrue($res);
	}
	
//	public function test5 () {
//		global $rules;
//
//		eval ("\$res = ".$rules['setoutcome5_1'].";");
//		$this->assertFalse($res);
//		
//		eval ("\$res = ".$rules['setoutcome5_2'].";");
//		$this->assertFalse($res);
//	}

}
?>
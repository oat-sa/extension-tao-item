<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/bootstrap.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class QTIPackageParsingTestCase extends UnitTestCase {
	
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
		
		//check if wrong packages are not validated correctly
		foreach(glob(dirname(__FILE__).'/samples/package/wrong/*.zip') as $file){
			
			$qtiParser = new taoItems_models_classes_QTI_PackageParser($file);
			
			$qtiParser->validate();
			
			$this->assertFalse($qtiParser->isValid());
			$this->assertTrue(count($qtiParser->getErrors()) > 0);
			$this->assertTrue(strlen($qtiParser->displayErrors()) > 0);
		}
		
		//check if package samples are valid
		foreach(glob(dirname(__FILE__).'/samples/package/*.zip') as $file){
			
			$qtiParser = new taoItems_models_classes_QTI_PackageParser($file);
			$qtiParser->validate();
			
			if(!$qtiParser->isValid())
				echo $qtiParser->displayErrors();
			
			$this->assertTrue($qtiParser->isValid());
		}
		
		
		//check if wrong manifest files are not validated correctly
		foreach(glob(dirname(__FILE__).'/samples/package/wrong/*.xml') as $file){
			
			$qtiParser = new taoItems_models_classes_QTI_ManifestParser($file);
			
			$qtiParser->validate();
			
			$this->assertFalse($qtiParser->isValid());
			$this->assertTrue(count($qtiParser->getErrors()) > 0);
			$this->assertTrue(strlen($qtiParser->displayErrors()) > 0);
		}
		
		//check if manifest samples are valid
		foreach(glob(dirname(__FILE__).'/samples/package/*.xml') as $file){
			
			$qtiParser = new taoItems_models_classes_QTI_ManifestParser($file);
			$qtiParser->validate();
			
			if(!$qtiParser->isValid())
				echo $qtiParser->displayErrors();
			
			$this->assertTrue($qtiParser->isValid());
		}
		
	}
}
?>
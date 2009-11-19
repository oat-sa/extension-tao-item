<?php

require_once dirname(__FILE__).'/../includes/common.php';
require_once $GLOBALS['inc_path'].'/simpletest/autorun.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package tao
 * @subpackage test
 */
class FormTestCase extends UnitTestCase {
	
	protected $service;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		//connection to the API 
		$this->service = tao_models_classes_ServiceFactory::get('Items');
	}
	
	/**
	 * @return 
	 */
	public function testAuthoringService(){
		
		$authoringFileData = $this->service->getAuthoringFile();
		$this->assertEqual(count($authoringFileData), 2);
		
		$id = $this->service->getAuthoringFileIdByUri($authoringFileData['uri']);
		$this->assertNotEqual('', $id);
		
		$uri = $this->service->getAuthoringFileUriById($authoringFileData['id']);
		$this->assertNotEqual('', $uri);
		
		if(file_exists($uri)){
			unlink($uri);
		}
	}

}
?>
<?php 
require_once('tao/actions/Api.class.php');

/**
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class PreviewApi extends Api {
	
	const AUTH_TOKEN = 'PREVIEW_ME';
	
	protected $itemService;
	
	/**
	 * constructor: initialize the services
	 */
	public function __construct(){
		parent::__construct();
		$this->itemService = tao_models_classes_ServiceFactory::get('Items');
	}
	
	/**
	 * Create an execution environment for the preview
	 * @param core_kernel_classes_Resource $item
	 * @param core_kernel_classes_Resource $user
	 * @return array
	 */
	private function getFakeExecutionEnvironment(core_kernel_classes_Resource $item, core_kernel_classes_Resource $user){
		$executionEnvironment = array();
		if(!is_null($item) && !is_null($user)){	
		//create a fake  executionEnvironment
        	$executionEnvironment = array(
				'token'			 => self::AUTH_TOKEN,
				'localNamespace' => core_kernel_classes_Session::singleton()->getNameSpace(),
			
				CLASS_PROCESS_EXECUTIONS => array(
					'uri'		=> 'fake_process#i'.time(),
					RDFS_LABEL	=> __('Fake process')
				),
				
				TAO_ITEM_CLASS	=> array(
					'uri'		=> $item->uriResource,
					RDFS_LABEL	=> $item->getLabel()
				),
				TAO_TEST_CLASS	=> array(
					'uri'		=> 'fake_test#i'.time(),
					RDFS_LABEL	=> __('Fake test')
				),
				TAO_DELIVERY_CLASS	=> array(
					'uri'		=> 'fake_delivery#i'.time(),
					RDFS_LABEL	=> __('Fake delivery')
				),
				TAO_SUBJECT_CLASS => array(
					'uri'					=> $user->uriResource,
					RDFS_LABEL				=> $user->getLabel(),
					PROPERTY_USER_LOGIN		=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN)),
					PROPERTY_USER_FIRTNAME	=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_FIRTNAME)),
					PROPERTY_USER_LASTNAME	=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LASTNAME))
				)
			);
		}
		return $executionEnvironment;
	}
	
	
	/**
	 * Initialize, deploy and display the preview of an item
	 */
	public function runner(){
		if($this->hasRequestParameter('uri')){
			
			$item 	= new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
			
			//use the TaoManager user as the subject
			$user = $this->userService->getCurrentUser();
			if(is_null($user)){
				throw new Exception(__('No user is logged in'));
			}
			
			//default deployment params
			$deployParams = array(
				'delivery_server_mode'	=> false,
				'preview_mode'			=> true
			);
				
			$itemFolder = $this->itemService->getRuntimeFolder($item);
        	$itemPath = "{$itemFolder}/index.html";
			if(!is_dir($itemFolder)){
        		mkdir($itemFolder);
        	}
        	$itemUrl = str_replace(BASE_PATH .'/views', BASE_WWW, $itemPath);
        		
        	//deploy the item
        	if(!$this->itemService->deployItem($item, $itemPath, $itemUrl,  $deployParams)){
        		throw new Exception('unable to deploy item');
        	}
        	
        	$executionEnvironment = $this->getFakeExecutionEnvironment($item, $user);
        		
			// We inject the data directly in the item file
			try{
				$doc = new DOMDocument();
				$doc->loadHTMLFile($itemPath);
				
				
				//initialization of the TAO API
				$varCode = 'var '.self::ENV_VAR_NAME.' = '.json_encode($executionEnvironment).';';
				$initAPICode = 'initManualDataSource('.self::ENV_VAR_NAME.');';
				
				$saveResult = json_encode(array(
					'url' 		=> _url('save', 'PreviewApi', 'taoItems'), 
					'params'	=> array('token' => self::AUTH_TOKEN)
				));
				$initAPICode .= "initPush($saveResult, null);";
				
				
				//initialize the events logging
				$initEventCode = '';
				if(file_exists($itemFolder .'/events.xml')){
					$eventService = tao_models_classes_ServiceFactory::get("tao_models_classes_EventsService");
					$eventData =  json_encode($eventService->getEventList($itemFolder .'/events.xml'));
					$saveEvent = json_encode(array(
						'url' 		=> _url('traceEvents', 'PreviewApi', 'taoItems'), 
						'params'	=> array('token' => self::AUTH_TOKEN)
					));
					
					$initEventCode = "initEventServices({ type: 'manual', data: $eventData}, $saveEvent);";
				}
				
				$clientCode  = '$(document).ready(function(){ '; 
				$clientCode .= "$varCode \n";
				$clientCode .= "$initAPICode \n";
				$clientCode .= "$initEventCode \n";
				$clientCode .= '});';
				$scriptElt   = $doc->createElement('script', $clientCode);
				$scriptElt->setAttribute('type', 'text/javascript');
				
				$headNodes = $doc->getElementsByTagName('head');
				
				foreach($headNodes as $headNode){
					$inserted = false;
					$scriptNodes = $headNode->getElementsByTagName('script');
					if($scriptNodes->length > 0){
						foreach($scriptNodes as $index => $scriptNode){
							if($scriptNode->hasAttribute('src')){
								if(preg_match("/taoApi\.min\.js$/", $scriptNode->getAttribute('src'))){
									$headNode->insertBefore($scriptElt, $scriptNodes->item($index +1));
									$inserted = true;
									break;
								}
							}
						}
					}
					if(!$inserted){
						$taoScriptElt = $doc->createElement('script');
						$taoScriptElt->setAttribute('type', 'text/javascript');
						$taoScriptElt->setAttribute('src', TAO_BASE_WWW.'js/taoApi/taoApi.min.js');
						$headNode->appendChild($taoScriptElt);
						
						$headNode->appendChild($scriptElt);
					}
					
					$previewScriptElt = $doc->createElement('script');
					$previewScriptElt->setAttribute('type', 'text/javascript');
					$previewScriptElt->setAttribute('src', BASE_WWW.'js/preview-console.js');
					$headNode->appendChild($previewScriptElt);
					
					break;
				}
				
				//render the item
				echo $doc->saveHTML();
				
			}
			catch(DOMException $de){
				if(DEBUG_MODE){
					throw new Exception(__("An error occured while loading the item: ") . $de);
				}
				else{
					error_log($de->getMessage);		//log the error in the log file and display a common message
					throw new Exception(__("An error occured while loading the item"));
				}
			}
        		
		}
	}
	
	public function evaluate () {
		
	}
	
	public function save(){
		echo json_encode(array('saved' => true));
	}
	
	
	public function traceEvents(){
		echo json_encode(array('saved' => true));
	}
	
}
?>
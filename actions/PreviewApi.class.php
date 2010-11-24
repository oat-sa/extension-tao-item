<?php 
require_once('tao/actions/Api.class.php');

/**
 * the PreviewApi provides methods to preview and execute items 
 * in a sandbox environment.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class PreviewApi extends Api {
	
	/**
	 * @var string the same token is exchanged for all the communications
	 */
	const AUTH_TOKEN = 'PREVIEW_ME';
	
	/**
	 * @var taoItems_models_classes_ItemService
	 */
	protected $itemService;
	
	/**
	 * constructor: initialize the services
	 */
	public function __construct(){
		parent::__construct();
		$this->itemService = tao_models_classes_ServiceFactory::get('Items');
	}
	
	/**
	 * Create a fake  execution environment for the preview
	 * @param core_kernel_classes_Resource $item
	 * @param core_kernel_classes_Resource $user
	 * @return array
	 */
	private function createFakeExecutionEnvironment(core_kernel_classes_Resource $item, core_kernel_classes_Resource $user){
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
			Session::setAttribute(self::ENV_VAR_NAME.'_'.tao_helpers_Uri::encode($user->uriResource), $executionEnvironment);
		}
		
		return $executionEnvironment;
	}
	
	/**
	 * Retrieve the fake execution environment
	 * @param core_kernel_classes_Resource $user
	 * @return array
	 */
	private function getFakeExecutionEnvironment(core_kernel_classes_Resource $user){
		$executionEnvironment = array();
		if(!is_null($user)){
			$sessionKey =  self::ENV_VAR_NAME . '_' . tao_helpers_Uri::encode($user->uriResource);
			if(Session::hasAttribute($sessionKey)){
				$executionEnvironment = Session::getAttribute($sessionKey);
				if(isset($executionEnvironment['token'])){
					return $executionEnvironment;
				}
			}
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
				'preview_mode'			=> true,
				'matching_server'		=> true
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
        	
        	$executionEnvironment = $this->createFakeExecutionEnvironment($item, $user);
        		
			// We inject the data directly in the item file
			try{
				$doc = new DOMDocument();
				$doc->loadHTMLFile($itemPath);
				
				$headNodes = $doc->getElementsByTagName('head');
				
				foreach($headNodes as $headNode){
					
					$initScriptElt = $doc->createElement('script');
					$initScriptElt->setAttribute('type', 'text/javascript');
					
					$initScriptParams = array(
						'matching' => ($deployParams['matching_server']) ? 'server' : 'client',
						'uri' => tao_helpers_Uri::encode($item->uriResource)
					);
					
					$initScriptElt->setAttribute('src', _url('initApis', 'PreviewApi', 'taoItems', $initScriptParams));
					$headNode->appendChild($initScriptElt);
					
					$inserted = false;
					$scriptNodes = $headNode->getElementsByTagName('script');
					$poisition = 0;
					if($scriptNodes->length > 0){
						foreach($scriptNodes as $index => $scriptNode){
							if($scriptNode->hasAttribute('src')){
								if(preg_match("/taoApi\.min\.js$/", $scriptNode->getAttribute('src')) ||
									preg_match("/taoMatching\.min\.js$/", $scriptNode->getAttribute('src'))){
									if($index > $position){
										$position = $index;
									}
								}
							}
						}
						if($scriptNodes->item($position + 1)){
							$headNode->insertBefore($initScriptElt, $scriptNodes->item($position + 1));
							$inserted = true;
						}
					}
					if(!$inserted){
						$taoScriptElt = $doc->createElement('script');
						$taoScriptElt->setAttribute('type', 'text/javascript');
						$taoScriptElt->setAttribute('src', TAO_BASE_WWW.'js/taoApi/taoApi.min.js');
						$headNode->appendChild($taoScriptElt);
						
						$matchingScriptElt = $doc->createElement('script');
						$matchingScriptElt->setAttribute('type', 'text/javascript');
						$matchingScriptElt->setAttribute('src', TAO_BASE_WWW.'js/taoMatching/taoMatching.min.js');
						$headNode->appendChild($matchingScriptElt);
						
						$headNode->appendChild($initScriptElt);
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
	
	/**
	 * Action to render a dynamic javascript page
	 * containing the APIs initialization for the current preview execution context
	 */
	public function initApis(){
		if($this->hasRequestParameter('uri')){
			
			$item 	= new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
			$user = $this->userService->getCurrentUser();
			
			$executionEnvironment = $this->getExecutionEnvironment($user);
			if(isset($executionEnvironment['token'])){
				
				header('Context-Type', 'application/javascript');
				
				//taoApi data source
				$this->setData('envVarName', self::ENV_VAR_NAME);
				$this->setData('executionEnvironment', json_encode($executionEnvironment));
				
				//taoApi push parameters
				$this->setData('pushParams', json_encode(array(
						'url' 		=> _url('save', 'PreviewApi', 'taoItems'), 
						'params'	=> array('token' => self::AUTH_TOKEN)
				)));
				
				//taoApi events tracing parameters
				$itemFolder = $this->itemService->getRuntimeFolder($item);
				if(file_exists($itemFolder .'/events.xml')){
					$eventService = tao_models_classes_ServiceFactory::get("tao_models_classes_EventsService");
					$eventData =  $eventService->getEventList($itemFolder .'/events.xml');
					
					$this->setData('eventData', json_encode($eventData));
					$this->setData('eventParams', json_encode(array(
							'url' 		=> _url('traceEvents', 'PreviewApi', 'taoItems'), 
							'params'	=> array('token' => self::AUTH_TOKEN)
					)));
				}

				//taoMatching
				$matching_server = false;
				if($this->hasRequestParameter('matching')){
					if($this->getRequestParameter('matching') == 'server'){
						$matching_server = true;
					}
				}
				$this->setData('matchingServer', $matching_server);
				$matchingParams = array();
				if($matching_server == true){
					$matchingParams = array(
						'url'		=> _url('evaluate', 'PreviewApi', 'taoItems'), 
						'params'	=> array('token' => self::AUTH_TOKEN)
					);
				}
				else{
					$this->setData('matchingData', json_encode($this->itemService->getMatchingData($item)));
				}
				$this->setData('matchingParams', json_encode($matchingParams));
				
				
				$this->setView('init_api.js.tpl');
			}
		}
		return;
	}
	
	/**
	 * Server matchting evaluation
	 * Used to test a server-side matching implemenatation in the previewed item.
	 */
	public function evaluate () {
		$returnValue = array();
		
		if($this->hasRequestParameter('token')){
			if($this->getRequestParameter('token') == self::AUTH_TOKEN){
				
				$user = $this->userService->getCurrentUser();
				$executionEnvironment = $this->getFakeExecutionEnvironment($user);
				if(isset($executionEnvironment['token'])){
				
					$item = new core_kernel_classes_Resource($executionEnvironment[TAO_ITEM_CLASS]['uri']);
					$itemMatchingData = $this->itemService->getMatchingData($item);
					
					matching_init ();
			        matching_setRule ($itemMatchingData["rule"]);
			        matching_setMaps ($itemMatchingData["maps"]);
			        matching_setCorrects ($itemMatchingData["corrects"]);
			        matching_setResponses (json_decode($_POST['data']));
			        matching_setOutcomes ($itemMatchingData["outcomes"]);
			        matching_evaluate ();
			
			        $outcomes = matching_getOutcomes ();
			        // Check if outcomes are scalar
			        try {
			            foreach ($outcomes as $outcome) {
			                if (! is_scalar($outcome['value'])){
			                    throw new Exception ('taoItems_models_classes_ItemsService::evaluate outcomes are not scalar');
			                }
			            }
			            $returnValue = $outcomes;
			        } catch (Exception $e) { }
		        
				}
			}
		}
        echo json_encode ($returnValue);
	}
	
	/**
	 * Check the communication from the item
	 * The token parameter is checked
	 */
	protected function checkCommunication(){
		if($this->hasRequestParameter('token')){
			if($this->getRequestParameter('token') == self::AUTH_TOKEN){
				$executionEnvironment = $this->getFakeExecutionEnvironment($this->userService->getCurrentUser());
				if(isset($executionEnvironment['token'])){
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * taoApi save variables action:
	 * Used to test the taoApi push implemenatation in the previewed item.
	 */
	public function save(){
		echo json_encode(array('saved' => $this->checkCommunication()));
	}
	
	/**
	 * taoApi trace event action:
	 * Used to test the taoApi event tracing implemenatation in the previewed item.
	 */
	public function traceEvents(){
		echo json_encode(array('saved' => $this->checkCommunication()));
	}
	
}
?>
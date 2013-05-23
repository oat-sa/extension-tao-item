<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * the PreviewApi provides methods to preview and execute items
 * in a sandbox environment.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoItems_actions_PreviewApi extends tao_actions_Api {

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
		$this->itemService = taoItems_models_classes_ItemsService::singleton();
	}

	/**
	 * Create a fake  execution environment for the preview,
	 *
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
					'uri'		=> $item->getUri(),
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
					'uri'					=> $user->getUri(),
					RDFS_LABEL				=> $user->getLabel(),
					PROPERTY_USER_LOGIN		=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN)),
					PROPERTY_USER_FIRSTNAME	=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_FIRSTNAME)),
					PROPERTY_USER_LASTNAME	=> (string)$user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LASTNAME))
				)
			);
        	$this->setSessionAttribute(self::ENV_VAR_NAME.'_'.tao_helpers_Uri::encode($user->getUri()), $executionEnvironment);
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
			$sessionKey =  self::ENV_VAR_NAME . '_' . tao_helpers_Uri::encode($user->getUri());
			if($this->hasSessionAttribute($sessionKey)){
				$executionEnvironment = $this->getSessionAttribute($sessionKey);
				if(isset($executionEnvironment['token'])){
					return $executionEnvironment;
				}
			}
		}
		return $executionEnvironment;
	}

	/**
	 * The runner is the containter called to :
	 * 	- Initialize the environment and the parameters, inject the Apis into the item
	 *  - Deploy the item: generate the item files
	 *  - Display the previewed item
	 */
	public function runner(){
		if ($this->hasRequestParameter('uri')) {
			$item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));

			//use the TaoManager user as the subject
			$user = $this->userService->getCurrentUser();
			if (is_null($user)) {
				throw new Exception(__('No user is logged in'));
			}

			//default deployment params
			$deployParams = array(
				'delivery_server_mode' => false,
				'matching_server' => true,
				'base_www' => BASE_WWW
			);

			//Initialize the deployment parameters
			if ($this->hasRequestParameter('match')) {
				if ($this->getRequestParameter('match') == 'client') {
					$deployParams['matching_server'] = false;
				}
			}
			$debugMode = false;
			if ($this->hasRequestParameter('debug')) {
				if ($this->getRequestParameter('debug')) {
					$deployParams['debug'] = true;
				}
			}
			$useContext  = false;
			if ($this->hasRequestParameter('context')) {
				if ($this->getRequestParameter('context')) {
					$useContext = true;
				}
			}

			//Prepare folders for the deployment
			$itemFolder = $this->itemService->getRuntimeFolder($item);
			$itemPath = "{$itemFolder}/index.html";
			if (!is_dir($itemFolder)) {
				mkdir($itemFolder);
			}

			$itemUrl = tao_helpers_Uri::getUrlForPath($itemPath);
			//Deploy the item, will create the html file in itemPath available from itemUrl

			if (!$this->itemService->deployItem($item, $itemPath, $itemUrl,  $deployParams)) {
				throw new Exception('unable to deploy item');
			}

			//Create the sandbox
			$executionEnvironment = $this->createFakeExecutionEnvironment($item, $user);
			
			try {
				$source = file_get_contents($itemPath);
				$htmlCode = $this->insertPreviewJs($item, $source);
				echo $htmlCode;
			}
			catch (DOMException $de) {
				throw new Exception(__("An error occured while loading the item: ") . $de);
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

			$executionEnvironment = $this->getFakeExecutionEnvironment($user);
			if(isset($executionEnvironment['token'])){

				$this->setContentHeader('application/javascript');

				//taoApi data source
				$this->setData('envVarName', self::ENV_VAR_NAME);
				$this->setData('executionEnvironment', json_encode($executionEnvironment));

				//taoApi push parameters
				$this->setData('pushParams', json_encode(array(
						'url' 		=> _url('save', 'PreviewApi', 'taoItems'),
						'params'	=> array('token' => self::AUTH_TOKEN)
				)));

				//taoApi events tracing parameters
				$eventFile = $this->itemService->getItemFolder($item).'events.xml';
				if (!file_exists($eventFile)) {
					$eventFile = ROOT_PATH.'taoItems/data/events_ref.xml';
				}
				
				$eventService = tao_models_classes_EventsService::singleton();
				$eventData =  $eventService->getEventList($eventFile);

				$this->setData('eventData', json_encode($eventData));
				$this->setData('eventParams', json_encode(array(
						'url' 		=> _url('traceEvents', 'PreviewApi', 'taoItems'),
						'params'	=> array('token' => self::AUTH_TOKEN)
				)));
			
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

				//wfApi recovery context parameters
				$disable_context = true;
				if($this->hasRequestParameter('context')){
					if($this->getRequestParameter('context')){
						$disable_context = false;
					}
				}
				$this->setData('disableContext', $disable_context);
				$this->setData('contextSourceParams', json_encode(array(
						'url' 		=> _url('retrieveContext', 'PreviewApi', 'taoItems'),
						'params'	=> array('token' => self::AUTH_TOKEN)
				)));
				$this->setData('contextDestinationParams', json_encode(array(
						'url' 		=> _url('saveContext', 'PreviewApi', 'taoItems'),
						'params'	=> array('token' => self::AUTH_TOKEN)
				)));


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
					$outcomes = $this->itemService->evaluate($item, json_decode($_POST['data']));

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

	/**
	 * wfApi retrieve context action:
	 * used to test retrieveing the context recovery during the item preview
	 */
	public function retrieveContext(){
		$context = array();
		if($this->checkCommunication()){
			if(isset($_COOKIE['previewContext'])){

				$executionEnvironment = $this->getFakeExecutionEnvironment($this->userService->getCurrentUser());
				$itemUri = $executionEnvironment[TAO_ITEM_CLASS]['uri'];

				$currentContext = $_COOKIE['previewContext'];
				if(is_string($currentContext) && !empty($currentContext)){
					$currentContext = unserialize($currentContext);
					if(isset($currentContext[$itemUri])){
						$context = $currentContext[$itemUri];
					}
				}
			}
		}
		echo json_encode($context);
	}

	/**
	 * wfApi save context action:
	 * used to test saving the context recovery during the item preview
	 */
	public function saveContext(){
		$saved = false;
		if($this->checkCommunication() && $this->hasRequestParameter('context')){

			$executionEnvironment = $this->getFakeExecutionEnvironment($this->userService->getCurrentUser());
			$itemUri = $executionEnvironment[TAO_ITEM_CLASS]['uri'];

			$context = $this->getRequestParameter('context');
			if(is_array($context)){
				$currentContext = array();
				if(isset($_COOKIE['previewContext'])){
					$currentContext = $_COOKIE['previewContext'];
					if(is_string($currentContext) && !empty($currentContext)){
						$currentContext = unserialize($currentContext);
					}
				}
				foreach($context as $key => $value){
					$currentContext[$itemUri][$key] = $value;
				}
				//a 1 hour cookie
				$saved = setcookie('previewContext', serialize($currentContext), time() + 3600 );
			}
		}
		echo json_encode(array('saved' => $saved));
	}
	
	private function insertPreviewJs($item, $html) {
		
		//Initialize the deployment parameters
		$matching_server = true;
		if ($this->hasRequestParameter('match')) {
			if ($this->getRequestParameter('match') == 'client') {
				$matching_server = false;
			}
		}
		$debugMode = false;
		if ($this->hasRequestParameter('debug')) {
			if ($this->getRequestParameter('debug')) {
				$deployParams['debug'] = true;
			}
		}
		$useContext  = false;
		if ($this->hasRequestParameter('context')) {
			if ($this->getRequestParameter('context')) {
				$useContext = true;
			}
		}
		//we parse the DOM of the item (it must be well formed and valid)
		$doc = new DOMDocument();
		(DEBUG_MODE)?@$doc->loadHTML($html):$doc->loadHTML($html);

		//inject the apis
		$headNodes = $doc->getElementsByTagName('head');

		foreach ($headNodes as $headNode) {
			//Inject the initialisation script
			//@see taoItems_actions_PreviewApi::iniApis
			$initScriptElt = $doc->createElement('script');
			$initScriptElt->setAttribute('type', 'text/javascript');

			$initScriptParams = array(
				'context'	=> $useContext,
				'matching' 	=> $matching_server ? 'server' : 'client',
				'debug'		=> $debugMode,
				'uri' 		=> tao_helpers_Uri::encode($item->getUri()),
				'time'		=> time()	//to prevent caching
			);
			common_Logger::d(var_export($initScriptParams, true), 'QTIdebug');
			
			//the url of the init script
			$initScriptElt->setAttribute('src', _url('initApis', 'PreviewApi', 'taoItems', $initScriptParams));

			$headNode->appendChild($initScriptElt);

			//we inject too the preview-console
			$previewScriptElt = $doc->createElement('script');
			$previewScriptElt->setAttribute('type', 'text/javascript');
			$previewScriptElt->setAttribute('src', BASE_WWW.'js/preview-console.js');
			$headNode->appendChild($previewScriptElt);
			break;
		}

		/*
		 * Render of the item by printing the HTML,
		 * so be carefull with the URLs inside the item
		 */
		return $doc->saveHTML();
	}
	
	public static function getPreviewUrl($item, $options) {
		$code = base64_encode($item->getUri());
		unset($options['uri'], $options['classUri']);
		return _url('render/'.$code.'/index.php', 'PreviewApi', 'taoItems', $options);
	}
	
	public function render() {
		$parts = explode('?', $_SERVER['REQUEST_URI'], 2);
		$parts = explode('/', $parts[0], 6);
		list($empty, $extension, $module, $action, $codedUri, $path) = $parts;
		$uri = base64_decode($codedUri);
		$item = new core_kernel_classes_Resource($uri);
		if ($path == 'index.php') {
			$this->renderItem($item);
		} else {
			$this->renderResource($item, $path);
		}
	}
	
	private function renderItem($item) {
		
		$user = $this->userService->getCurrentUser();
		$this->createFakeExecutionEnvironment($item, $user);
		
		$itemModel = $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
		$impl = $this->itemService->getItemModelImplementation($itemModel);
		if (!is_null($impl)) {
			$html = $impl->render($item);
			echo $this->insertPreviewJs($item, $html);
		} else {
			throw new common_Exception('preview not supported for this item type '.$itemModel->getUri());
		}
	}
	
	private function renderResource($item, $path) {
		$folder = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item);
		$filename = $folder.$path;
		if (file_exists($filename)) {
			$mimeType = tao_helpers_File::getMimeType($filename);
			header('Content-Type: '.$mimeType); 
			echo file_get_contents($filename);
		} else {
			throw new tao_models_classes_FileNotFoundException($filename);
		}
	}
}
?>
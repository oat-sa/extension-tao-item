<?php

require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Items Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class Items extends TaoModule{
	
	/**
	 * constructor: initialize the service and the default data
	 * @return  Items
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		

		$this->service = tao_models_classes_ServiceFactory::get('Items');
		$this->defaultData();
		$this->setData('modelDefined', false);
		
	}

	/**
	 * Override auth method
	 * @see TaoModule::_isAllowed
	 * @return boolean
	 */	
	protected function _isAllowed(){
		$context = Context::getInstance();
		if($context->getActionName() != 'getItemContent'){
			return parent::_isAllowed();
		}
		return true;
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the instancee of the current item regarding the 'uri' and 'classUri' request parameters
	 * @return core_kernel_classes_Resource the item instance
	 */
	protected function getCurrentInstance(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$item = $this->service->getItem($uri, $this->getCurrentClass());
		if(is_null($item)){
			throw new Exception("No item found for the uri {$uri}");
		}
		
		return $item;
	}
	
	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getItemClass();
	}
	
/*
 * controller actions
 */

	
	/**
	 * edit an item instance
	 */
	public function editItem(){
	
		$itemClass = $this->getCurrentClass();
		$item = $this->getCurrentInstance();
		
		$formContainer = new taoItems_actions_form_Item($itemClass, $item);
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$item = $this->service->bindProperties($item, $myForm->getValues());
				$item = $this->service->setDefaultItemContent($item);
				
				$this->setData('message', __('Item saved'));
				$this->setData('reload', true);
			}
		}
		
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->uriResource));
		
		$modelDefined = $this->isModelDefined($item);
		if(!$modelDefined){
			$myForm->removeElement(tao_helpers_Uri::encode(TAO_ITEM_CONTENT_PROPERTY));
		}
		$this->setData('modelDefined', $modelDefined);
		
		$this->setData('uri', tao_helpers_Uri::encode($item->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($itemClass->uriResource));
		
		$this->setData('formTitle', __('Edit Item'));
		$this->setData('myForm', $myForm->render());
		
		$this->setView('form.tpl');
	}
	
	/**
	 * Edit the row item content: download and upload the item content from the XML format 
	 */
	public function itemContentIO(){
		
		$item = $this->getCurrentInstance();
		$itemClass = $this->getCurrentClass();
		
		$formContainer = new taoItems_actions_form_ItemContentIO($itemClass, $item);
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$data = $myForm->getValues();
				
				if(isset($data['file_import']['uploaded_file'])){
					
					//parse and validate the sent file
					$parser = new taoItems_models_classes_Parser($data['file_import']['uploaded_file']);
					
					//check if the valdiation should be skipped
					$validate = true;
					if(isset($data['disable_validation'])){
						if(in_array('on', $data['disable_validation'])){
							$validate = false;	
						}
					}
					if(!$validate){
						$parser->forceValidation();
					}
					$schema = '';
					 
					//get the Xml Schema regarding the item model
					$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
					switch($itemModel->uriResource){
						case TAO_ITEM_MODEL_QCM: break;
					 	case TAO_ITEM_MODEL_WATERPHENIX: break;
					 	case TAO_ITEM_MODEL_QTI:
							$schema = BASE_PATH . '/models/classes/QTI/data/imsqti_v2p0.xsd';
							break;
					 	default:
					 		$modelName = strtolower(trim($itemModel->getLabel()));
					 		$schema = BASE_PATH . "/models/classes/data/{$modelName}/{$modelName}.xsd";
							break;
						
					}
					 
					if(!empty($schema)){
						//run the validation
						$parser->validate($schema);	
					}
					
					if($parser->isValid()){
						//if the file is valid, we set it as the property of the item
						$item->editPropertyValues(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY), file_get_contents($data['file_import']['uploaded_file']));
						
						$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->uriResource));
						$this->setData('message', __('Item content saved'));
					}
					
					//get the errors (is empty if the file is valid)  
					$this->setData('importErrors', $parser->getErrors());
				}
			}
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($item->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($itemClass->uriResource));
		
		$this->setData('formTitle', __('Manage item content'));
		$this->setData('myForm', $myForm->render());
		
		$this->setView('form_content.tpl');
	}
	
	/**
	 * Preview an item
	 * @return void
	 */
	public function preview(){
		$itemClass = $this->getCurrentClass();
		$item = $this->getCurrentInstance();
		
		$previewData = $this->initPreview($item, $itemClass);
		if(count($previewData) == 0){
			$this->setData('preview', false);
			$this->setData('previewMsg', __("Not yet available"));
		}
		else{
			$this->setData('preview', true);
			$this->setData('instanceUri', tao_helpers_Uri::encode($item->uriResource, false));
			foreach($previewData as $key => $value){
				$this->setData($key, $value);
			}
		}
		
		$previewTitle = __('Preview');
		if($this->hasRequestParameter('previewTitle')){
			$previewTitle = $this->getRequestParameter('previewTitle');
		}
		$this->setData('previewTitle', $previewTitle);
		$this->setData('uri', tao_helpers_Uri::encode($item->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($itemClass->uriResource));
		$this->setView('preview.tpl');
	}
	
	/**
	 * get the data from the item used to run the preview 
	 * @param core_kernel_classes_Resource $item
	 * @param core_kernel_classes_Class    $clazz
	 * @return array 
	 */
	protected function initPreview(core_kernel_classes_Resource $item, core_kernel_classes_Class $clazz){
		$previewData = array();
		try{
			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			$itemContent = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
			
			if($itemContent instanceof core_kernel_classes_Literal && $itemModel instanceof core_kernel_classes_Resource){
				
				//the item content url 
				$contentUrl = urlencode(_url('getItemContent', 'Items', 'taoItems', array('uri' => urlencode($item->uriResource), 'classUri' => urlencode($clazz->uriResource), 'preview' => true)));
				
				//the runtime
				$runtime = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_RUNTIME_PROPERTY));
			
				if($itemModel->uriResource == TAO_ITEM_MODEL_WATERPHENIX){
					$content = trim(file_get_contents($this->service->getTempAuthoringFile($item->uriResource)));
					//@todo need to fix it in the runtime instead of urlencode 2x
					$contentUrl = urlencode($contentUrl);
				}
				else{
					$content = trim((string)$itemContent);
				}
				
				if(preg_match("/\.swf$/", (string)$runtime) && !empty($content)){
					$previewData['swf'] 		= BASE_URL.'/models/ext/itemRuntime/'.(string)$runtime;
					$previewData['contentUrl'] 	= $contentUrl;
				}
			}
		}
		catch(Exception $e){}
		
		return $previewData;
	}
	
	/**
	 * Chekc
	 * @param core_kernel_classes_Resource $item
	 * @return 
	 */
	protected function isModelDefined(core_kernel_classes_Resource $item){
		$isDefined = false;
		if(!is_null($item)){
			try{
				$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
				if($itemModel instanceof core_kernel_classes_Resource){
					$isDefined = true;
				}
			}
			catch(Exception $e){
			}
		}
		return $isDefined;
	}
	
	/**
	 * Edit a class
	 */
	public function editItemClass(){
		$clazz = $this->getCurrentClass();

		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}
		
		$myForm = $this->editClass($clazz, $this->service->getItemClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit a class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * Sub Class
	 * @return void
	 */
	public function addItemClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$subClass = $this->service->createSubClass($this->getCurrentClass());
		if($subClass instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $subClass->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($subClass->uriResource)
			));
		}
	}
	
	/**
	 * delete an item or an item class
	 * called via ajax
	 * @see TaoModule::delete
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteItem($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteItemClass($this->getCurrentClass());
		}
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * @see TaoModule::translateInstance
	 * @return void
	 */
	public function translateInstance(){
		parent::translateInstance();
		$this->setView('form.tpl', false);
	}
	
	/**
	 * Display the Item.ItemContent property value. 
	 * It's used by the authoring runtime/tools to retrieve the content
	 * @return void 
	 */
	public function getItemContent(){
		
		header("Content-Type: text/xml; charset utf-8");
		
		try{
			$item = $this->getCurrentInstance();
			$itemContent = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			if($itemContent instanceof core_kernel_classes_Literal && $itemModel instanceof core_kernel_classes_Resource){
				
				$xml = (string)$itemContent;
				
				if($itemModel->uriResource == TAO_ITEM_MODEL_WATERPHENIX){
					if($this->hasRequestParameter('preview')){
						$xml = file_get_contents($this->service->getTempAuthoringFile($item->uriResource));
					}
					else{
						$fileId = '';
						if(!empty($xml)){
							$xmlElt = new SimpleXMLElement($xml);
							if($xmlElt->root){
								if(isset($xmlElt->root['reference'])){
									$fileId = (string)$xmlElt->root['reference'];
								}
							}
						}
						if(empty($fileId)){
							$fileId = $item->uriResource;
						}
						$xml = file_get_contents($this->service->getAuthoringFile($fileId));
					}
				}
				echo $xml;
			}
		}
		catch(Exception $e){
			//print an empty response
			echo '<?xml version="1.0" encoding="utf-8" ?>';
			print $e;
		}
	}
	
	/**
	 * Item Authoring tool loader action
	 * @return void
	 */
	public function authoring(){
		
		$this->setData('error', false);
		
		try{
			$item = $this->getCurrentInstance();
			$itemClass = $this->getCurrentClass();
			
			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			if($itemModel instanceof core_kernel_classes_Resource){
				$authoring = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_AUTHORING_PROPERTY));
				if($authoring instanceof core_kernel_classes_Literal){
					
					//urlencode instead of tao_helpers_Uri::encode to be compatible with the swf authoring tools
					$itemContentUrlParam = array(
						'uri' => urlencode($item->uriResource), 
						'classUri' => urlencode($itemClass->uriResource)
					);
					$itemContentUrl = urlencode(_url('getItemContent', 'Items', 'taoItems', $itemContentUrlParam));
					
					if(preg_match("/\.swf$/", (string)$authoring)){
						$this->setData('type', 'swf');
					}
					if(preg_match("/\.php$/", (string)$authoring)){
						$this->setData('type', 'php');
					}
					if(preg_match("/taoItems\//", (string)$authoring)){
						$this->redirect((string)$authoring.'?xml='.$itemContentUrl.'&instance='.tao_helpers_Uri::encode($item->uriResource, false));
					}
					$this->setData('authoringFile', BASE_URL.'/models/ext/itemAuthoring/'.(string)$authoring);
					$this->setData('itemContentUrl', $itemContentUrl);
					
				}
			}
			$this->setData('instanceUri', tao_helpers_Uri::encode($item->uriResource, false));
		
			$this->setData('uri', tao_helpers_Uri::encode($item->uriResource));
			$this->setData('classUri', tao_helpers_Uri::encode($itemClass->uriResource));
		}
		catch(Exception $e){
			
			$this->setData('error', true);
		}
		$this->setView('authoring.tpl');
	}
	
	/**
	 * Authoring File mappgin service:
	 * Send into the request the parameters id and/or uri or nothing.
	 * Must be called via Ajax. 
	 * Render json response {id: id, uri: uri}
	 * @return void
	 */
	public function getAuthoringFile(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$idParam 	= $this->getRequestParameter('id');
		$uriParam 	= $this->getRequestParameter('uri');
		
		$authoringFileData = array();
		
		if(!$uriParam){
			$authoringFileData = $this->service->getAuthoringFile($idParam);
		}
		else{
			$authoringFileData['uri'] 	= $uriParam;
			$authoringFileData['id'] 	= $this->service->getAuthoringFileIdByUri($uriParam);
		}
		
		echo json_encode($authoringFileData);
	}
	
	/**
	 * use the xml content in session and set it to the item
	 * forwarded to the index action 
	 * @return void
	 */
	public function saveItemContent(){
		
		$message = __('An error occured while saving the item');
		
		if(isset($_SESSION['instance']) && isset($_SESSION['xml'])){
		
			$item = $this->service->getItem($_SESSION['instance']);
			if(!is_null($item)){
				
				$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
				if($itemModel instanceof core_kernel_classes_Resource){
					
					switch($itemModel->uriResource){
							
						case TAO_ITEM_MODEL_WATERPHENIX:
							$fileUri = $this->service->getAuthoringFile($item->uriResource);
							file_put_contents($fileUri, $_SESSION['xml']);
							break;
							

						case TAO_ITEM_MODEL_CTEST :
							isset($_SESSION["datalg"]) ? $lang = $_SESSION["datalg"] : $lang = $GLOBALS['lang'];
							$data = "<?xml version='1.0' encoding='UTF-8'?>
										<tao:ITEM xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#' rdf:ID='{$item->uriResource}' xmlns:tao='http://www.tao.lu/tao.rdfs' xmlns:rdfs='http://www.w3.org/2000/01/rdf-schema#'>
											<rdfs:LABEL lang='{$lang}'>{$item->getLabel()}</rdfs:LABEL>
											<rdfs:COMMENT lang='{$lang}'>{$item->comment}</rdfs:COMMENT>
											{$_SESSION['xml']}
										</tao:ITEM>";
							$item = $this->service->bindProperties($item, array(TAO_ITEM_CONTENT_PROPERTY => $data));
							break;
						default:
							$item = $this->service->bindProperties($item, array(TAO_ITEM_CONTENT_PROPERTY => $_SESSION['xml']));
							break;
					}
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->uriResource));
					$message = __('Item saved successfully');
				}
			}
		}
	
		if(tao_helpers_Context::check('STANDALONE_MODE')){
			$itemClass = $this->service->getClass($item);
			$this->redirect(_url('authoring', 'SaSItems', 'taoItems', array('uri' => tao_helpers_Uri::encode($item->uriResource).'&classUri='.tao_helpers_Uri::encode($itemClass->uriResource), 'classUri' => tao_helpers_Uri::encode($itemClass->uriResource), 'message' => urlencode($message))));
		}
		else{
			$this->redirect( _url('index', 'Main', 'tao', array('message' => urlencode($message))));
		}
	}
	
	/**
	 * get the temporary authoring file
	 * @return void
	 */
	public function loadTempAuthoringFile(){
		try{
			echo file_get_contents($this->service->getTempAuthoringFile( $this->getRequestParameter('instance')));
		}
		catch(Exception $e){
			//print an empty response
			echo '<?xml version="1.0" encoding="utf-8" ?>';
		}
	}
	
	/**
	 * save the temporary authoring file
	 * @return void
	 */
	public function saveTempAuthoringFile(){
		$instance = $this->getRequestParameter('instance');
		$xml = $this->getRequestParameter('xml');
		file_put_contents($this->service->getTempAuthoringFile($instance), html_entity_decode($xml));
	}
}
?>
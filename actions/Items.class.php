<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Items Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage actions
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
	protected function getCurrentItem(){
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
	
/*
 * controller actions
 */

	/**
	 * the default action. Do nothing
	 * @return void
	 */
	public function index(){
		
		if($this->getData('reload') == true){
			unset($_SESSION[SESSION_NAMESPACE]['uri']);
			unset($_SESSION[SESSION_NAMESPACE]['classUri']);
		}
		
		$context = Context::getInstance();
		$this->setData('content', "module: ". get_class($this) ." , action: " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	/**
	 * Render json data to populate the subject tree 
	 * 'modelType' must be in request parameter
	 */
	public function getItems(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		//render directly the json
		$highlightUri = '';
		if($this->hasSessionAttribute("showNodeUri")){
			$highlightUri = $this->getSessionAttribute("showNodeUri");
			unset($_SESSION[SESSION_NAMESPACE]["showNodeUri"]);
		} 
		echo json_encode( $this->service->toTree( $this->service->getItemClass() , true, true, $highlightUri) );
	}
	
	
	/**
	 * edit an item instance
	 */
	public function editItem(){
		$itemClass = $this->getCurrentClass();
		$item = $this->getCurrentItem();
		
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($itemClass, $item);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$item = $this->service->bindProperties($item, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->uriResource));
				$this->setData('message', 'item saved');
				$this->setData('reload', true);
				$this->forward('Items', 'index');
			}
		}
		
		$this->setData('preview', false);
		$runtimeFound = false;
		try{
			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			if($itemModel instanceof core_kernel_classes_Resource){
				$runtime = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_RUNTIME_PROPERTY));
				if($runtime instanceof core_kernel_classes_Literal ){
					if(preg_match("/\.swf$/", (string)$runtime)){
						$this->setData('swf', BASE_URL.'/models/ext/itemRuntime/'.(string)$runtime);
						$this->setData('dataPreview', urlencode(_url('getItemContent', 'Items', array('uri' => $item->uriResource, 'classUri' => $itemClass->uriResource))));
						$runtimeFound = true;
					}
				}
			}
		}
		catch(Exception $e){}
		
		if($runtimeFound){
			$this->setData('preview', true);
			$this->setData('instanceUri', tao_helpers_Uri::encode($item->uriResource, false));
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($item->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($itemClass->uriResource));
		$this->setData('formTitle', 'Edit Item');
		$this->setData('myForm', $myForm->render());
		$this->setView('form_preview.tpl');
	}
	
	/**
	 * Preview an item
	 * @return void
	 */
	public function preview(){
		$itemClass = $this->getCurrentClass();
		$item = $this->getCurrentItem();
		
		$this->setData('preview', false);
		$runtimeFound = false;
		try{
			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			if($itemModel instanceof core_kernel_classes_Resource){
				$runtime = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_RUNTIME_PROPERTY));
				if($runtime instanceof core_kernel_classes_Literal ){
					if(preg_match("/\.swf$/", (string)$runtime)){
						$this->setData('swf', BASE_URL.'/models/ext/itemRuntime/'.(string)$runtime);
						$this->setData('dataPreview', urlencode(_url('getItemContent', 'Items', array('uri' => $item->uriResource, 'classUri' => $itemClass->uriResource))));
						$runtimeFound = true;
					}
				}
			}
		}
		catch(Exception $e){ }
		
		if($runtimeFound){
			$this->setData('preview', true);
			$this->setData('instanceUri', tao_helpers_Uri::encode($item->uriResource, false));
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($item->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($itemClass->uriResource));
		$this->setView('preview.tpl');
	}
	
	/**
	 * Edit a class
	 */
	public function editItemClass(){
		$myForm = $this->editClass($this->getCurrentClass(), $this->service->getItemClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', 'class saved');
				$this->setData('reload', true);
				$this->forward('Items', 'index');
			}
		}
		$this->setData('formTitle', 'Edit a class');
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * Add an item instance
	 */
	public function addItem(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$itemClass = $this->getCurrentClass();
		$instance = $this->service->createInstance($itemClass);
		if($instance instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $instance->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($instance->uriResource)
			));
		}
	}
	
	/**
	 * Sub Class
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
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteItem($this->getCurrentItem());
		}
		else{
			$deleted = $this->service->deleteItemClass($this->getCurrentClass());
		}
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * duplicate an item instance by property copy
	 */
	public function cloneItem(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$item = $this->getCurrentItem();
		$clazz = $this->getCurrentClass();
		
		$clone = $this->service->createInstance($clazz);
		if(!is_null($clone)){
			
			foreach($clazz->getProperties() as $property){
				foreach($item->getPropertyValues($property) as $propertyValue){
					$clone->setPropertyValue($property, $propertyValue);
				}
			}
			$clone->setLabel($item->getLabel()."'");
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	/**
	 * @see TaoModule::getMetaData
	 * @return void
	 */
	public function getMetaData(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$this->setData('metadata', false); 
		if($this->getRequestParameter('uri') && $this->getRequestParameter('classUri')){
			
			$item = $this->getCurrentItem();
			
			$date = $item->getLastModificationDate();
			$this->setData('date', $date->format('d/m/Y H:i:s'));
			$this->setData('user', $item->getLastModificationUser());
			$this->setData('comment', $item->comment);
			
			$this->setData('uri', $this->getRequestParameter('uri'));
			$this->setData('classUri', $this->getRequestParameter('classUri'));
			$this->setData('metadata', true); 
		}
		
		
		$this->setView('metadata.tpl');
	}
	
	/**
	 * @see TaoModule::saveComment
	 * @return void
	 */
	public function saveComment(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$response = array(
			'saved' 	=> false,
			'comment' 	=> ''
		);
		if($this->getRequestParameter('uri') && $this->getRequestParameter('classUri') && $this->getRequestParameter('comment')){
			
			$item = $this->getCurrentItem();
			$item->setComment($this->getRequestParameter('comment'));
			if($item->comment == $this->getRequestParameter('comment')){
				$response['saved'] = true;
				$response['comment'] = $item->comment;
			}
		}
		echo json_encode($response);
	} 
	
	/**
	 * Display the Item.ItemContent property value. 
	 * It's used by the authoring runtime/tools to retrieve the content
	 * @return void 
	 */
	public function getItemContent(){
		header("Content-Type: text/xml; charset utf-8");
		$item = $this->getCurrentItem();
		$itemContent = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
		if($itemContent instanceof core_kernel_classes_Literal ){
			echo (string)$itemContent;
		}
	}
	
	/**
	 * Item Authoring tool loader action
	 * @return void
	 */
	public function authoring(){
		$this->setData('error', false);
		try{
			$item = $this->getCurrentItem();
			$itemClass = $this->getCurrentClass();
			
			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			if($itemModel instanceof core_kernel_classes_Resource){
				$authoring = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_AUTHORING_PROPERTY));
				if($authoring instanceof core_kernel_classes_Literal ){
					if(preg_match("/\.swf$/", (string)$authoring)){
						$this->setData('type', 'swf');
					}
					if(preg_match("/\.php$/", (string)$authoring)){
						$this->setData('type', 'php');
					}
					$this->setData('authoringFile', BASE_URL.'/models/ext/itemAuthoring/'.(string)$authoring);
					$this->setData('dataPreview', urlencode(_url('getItemContent', 'Items', array('uri' => $item->uriResource, 'classUri' => $itemClass->uriResource))));
				}
			}
			$this->setData('instanceUri', tao_helpers_Uri::encode($item->uriResource, false));
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
	
	
	/*
	 * @TODO implement the following actions
	 */
	
	public function import(){
		throw new Exception("Not yet implemented");
	}
	
	public function export(){
		throw new Exception("Not yet implemented");
	}
}
?>
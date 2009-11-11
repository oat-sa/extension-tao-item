<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Items controller
 */
class Items extends TaoModule{

	public function __construct(){
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Items');
		$this->defaultData();
	}

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
				
				$this->service->bindProperties($item, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->uriResource));
				$this->setData('message', 'item saved');
				$this->setData('reload', true);
				$this->forward('Items', 'index');
			}
		}
		$item = $this->getCurrentItem();
		
		$this->setData('preview', false);
		
		unset($_SESSION['ITEMpreview']);
		$itemContent = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
		if($itemContent instanceof core_kernel_classes_Literal ){
			$_SESSION['ITEMpreview'] = (string)$itemContent;
		}
		
		$swfFound = false;
		try{
			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			if($itemModel instanceof core_kernel_classes_Resource){
				$swf = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_SWFFILE_PROPERTY));
				if($swf instanceof core_kernel_classes_Literal ){
					$swffile = BASE_URL.'/models/ext/itemRuntime/'.(string)$swf;
					$this->setData('swf', $swffile);
					$this->setData('dataPreview', urlencode(BASE_URL.'/models/ext/itemRuntime/TAOgetItemPreview.php'));
					$swfFound = true;
				}
			}
		}
		catch(Exception $e){}
		
		if(isset($_SESSION['ITEMpreview']) && $swfFound){
			$this->setData('preview', true);
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($item->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($itemClass->uriResource));
		$this->setData('formTitle', 'Edit Item');
		$this->setData('myForm', $myForm->render());
		$this->setView('form_preview.tpl');
	}
	
	/**
	 * Edit a class
	 */
	public function editItemClass(){
		$myForm = tao_helpers_form_GenerisFormFactory::classEditor($this->getCurrentClass(), $this->service->getItemClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$classValues = array();
				$propertyValues = array();
				foreach($myForm->getValues() as $key => $value){
					if(preg_match("/^class_/", $key)){
						$classKey =  tao_helpers_Uri::decode(str_replace('class_', '', $key));
						$classValues[$classKey] =  tao_helpers_Uri::decode($value);
					}
					if(preg_match("/^property_/", $key)){
						if(isset($_POST[$key])){
							$key = str_replace('property_', '', $key);
							$propNum = substr($key, 0, 1 );
							$propKey = tao_helpers_Uri::decode(str_replace($propNum.'_', '', $key));
							$propertyValues[$propNum][$propKey] = tao_helpers_Uri::decode($value);
						}
						else{
							$key = str_replace('property_', '', $key);
							$propNum = substr($key, 0, 1 );
							if(!isset($propertyValues[$propNum])){
								$propertyValues[$propNum] = array();
							}
						}
					}
				}
				
				$clazz = $this->service->bindProperties($this->getCurrentClass(), $classValues);
				foreach($propertyValues as $propNum => $properties){
					if(isset($_POST['propertyUri'.$propNum]) && count($properties) == 0){
						
						//delete property mode
						foreach($clazz->getProperties() as $classProperty){
							if($classProperty->uriResource == tao_helpers_Uri::decode($_POST['propertyUri'.$propNum])){
								
								$classProperty->delete();
								break;
							}
						}
					}
					else{
						$this->service->bindProperties(new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST['propertyUri'.$propNum])), $properties);
					}
				}
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
	
	/*
	 * TODO
	 */
	
	public function import(){
		$context = Context::getInstance();
		$this->setData('content', "module: ". get_class($this) ." , action: " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	public function export(){
		$context = Context::getInstance();
		$this->setData('content', "module: ". get_class($this) ." , action: " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	public function authoring(){
		$this->setData('content', "ITEM AUTHORING TOOL");
		$this->setView('index.tpl');
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
	
}
?>
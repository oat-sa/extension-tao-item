<?php

class Items extends Module{

	protected $service;

	public function __construct(){
		$this->service = tao_models_classes_ServiceFactory::get('Items');
	}

	public function index(){
		$context = Context::getInstance();
		$this->setData('content', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	/**
	 * Render json data to populate the subject tree 
	 * 'modelType' must be in request parameter
	 * @return void
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
		echo json_encode( $this->service->toTree(new core_kernel_classes_Class( TAO_ITEM_CLASS ), true, true, $highlightUri));
	}
	
	public function addInstance(){
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
	
	public function editInstance(){
		$itemClass = $this->getCurrentClass();
		$item = $this->getCurrentItem();
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($itemClass, $item);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$this->service->bindProperties($item, $myForm->getValues());
				$this->setData('message', 'item saved');
			}
		}
		
		$this->setData('formTitle', 'Create a new Item');
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');;
	}
	
	public function addSubClass(){
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
	
	public function editClass(){
		$myForm = tao_helpers_form_GenerisFormFactory::classEditor($this->getCurrentClass(), new core_kernel_classes_Class( TAO_ITEM_CLASS ));
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$classValues = array();
				$propertyValues = array();
				foreach($myForm->getValues() as $key => $value){
					if(preg_match("/^class_/", $key)){
						$classValues[str_replace('class_', '', $key)] = $value;
					}
					if(preg_match("/^property_/", $key)){
						
						$key = str_replace('property_', '', $key);
						$propNum = substr($key, 0, 1 );
						$key = str_replace($propNum.'_', '', $key);
						$propertyValues[$propNum][$key] = $value;
					}
				}
				/*print "<pre>";
				print_r($_POST);
				print "</pre>";
				print "<pre>";
				print_r($myForm->getValues());
				print "</pre>";
				print "<pre>";
				print_r($classValues);
				print "</pre>";*/
				
				$clazz = $this->service->bindProperties($this->getCurrentClass(), $classValues);
				foreach($propertyValues as $propertyValue){
			//		$this->service->bindProperties(new core_kern, $classValues);
				}
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($class->uriResource));
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
	
	public function import(){
		$context = Context::getInstance();
		$this->setData('content', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	public function export(){
		$context = Context::getInstance();
		$this->setData('content', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	
	/*
	 * conveniance methods
	 */
	private function getCurrentItem(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		$classUri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
		if(is_null($uri) || empty($uri) || is_null($classUri) || empty($classUri)){
			throw new Exception("No valid uri found");
		}
		
		$model = new core_kernel_classes_Class($classUri);
		$item = $this->service->getItem($uri);
		if(is_null($item)){
			throw new Exception("No item found for the uri {$uri}");
		}
		
		return $item;
	}
	
	private function getCurrentClass(){
		$classUri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
		if(is_null($classUri) || empty($classUri)){
			throw new Exception("No valid uri found");
		}
		
		return  new core_kernel_classes_Class($classUri);
	}
	
}
?>
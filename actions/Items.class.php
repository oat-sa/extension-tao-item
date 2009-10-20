<?php

class Items extends Module{

	protected $service;

	public function __construct(){
		$this->service = tao_models_classes_ServiceFactory::get('Items');
	}

	public function index(){
		$context = Context::getInstance();
		$this->setData('testVar', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('view.tpl');
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
		
		$data = array();
		
		
		$instances = array();
		foreach($this->service->getItems() as $instance){
			$instances[] = array(
					'data' 	=> $instance->getLabel(),
					'attributes' => array(
						'id' => tao_helpers_Uri::encode($instance->uriResource),
						'class' => 'node-instance'
					)
				);
		}
		$itemClass			= new core_kernel_classes_Class( TAO_ITEM_CLASS );
		//format classes for json tree datastore 
		$data = array(
				'data' 	=> $itemClass->getLabel(),
				'attributes' => array(
						'id' => tao_helpers_Uri::encode($itemClass->uriResource),
						'class' => 'node-class'
					),
				'children'	=> $instances
			);
		
		
		
		//render directly the json
		echo json_encode($data);
	}
	
	public function add(){
		$itemClass = new core_kernel_classes_Class( TAO_ITEM_CLASS );
		$myForm = tao_helpers_form_GenerisFormFactory::createFromClass($itemClass);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$instance = $this->service->createInstance($itemClass);
				$instance = $this->service->bindProperties($instance, $myForm->getValues());
				print "<pre>";
				print_r($instance);
				print "<pre><br>";
				$this->setData('message', 'item created');
			}
		}
		
		$this->setData('formTitle', 'Create a new Item');
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');;
	}
	
	public function edit(){
		$itemClass = new core_kernel_classes_Class( TAO_ITEM_CLASS );
		$item = $this->getCurrentItem();
		$myForm = tao_helpers_form_GenerisFormFactory::createFromClass($itemClass, $item);
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
	
	public function import(){
		$context = Context::getInstance();
		$this->setData('testVar', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('view.tpl');
	}
	
	public function export(){
		$context = Context::getInstance();
		$this->setData('testVar', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('view.tpl');
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
	
	private function getCurrentModel(){
		$classUri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
		if(is_null($classUri) || empty($classUri)){
			throw new Exception("No valid uri found");
		}
		
		return  new core_kernel_classes_Class($classUri);
	}
}
?>
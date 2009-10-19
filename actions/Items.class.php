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
	
	public function create(){
		
		$myForm = tao_helpers_form_GenerisFormFactory::createFromClass( new core_kernel_classes_Class( TAO_ITEM_CLASS ));
		
		$this->setData('formTitle', 'Create a new Item');
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');;
	}
	
	public function import(){
		$context = Context::getInstance();
		$this->setData('testVar', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('view.tpl');
	}
	
	public function select(){
		$context = Context::getInstance();
		$this->setData('testVar', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('view.tpl');
	}
	
	public function search(){
		$context = Context::getInstance();
		$this->setData('testVar', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('view.tpl');
	}
}
?>
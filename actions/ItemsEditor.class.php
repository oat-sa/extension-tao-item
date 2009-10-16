<?php

class ItemsEditor extends Module{

	public function index(){
		$context = Context::getInstance();
		$this->setData('testVar', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('view.tpl');
	}
	
	public function property(){
		$context = Context::getInstance();
		$this->setData('testVar', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('view.tpl');;
	}
	
	public function fields(){
		$context = Context::getInstance();
		$this->setData('testVar', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('view.tpl');
	}
	
	public function stimulus(){
		$context = Context::getInstance();
		$this->setData('testVar', "this is the ". get_class($this) ." module, " . $context->getActionName());
		$this->setView('view.tpl');
	}
}
?>
<?php
/**
 * Items controller
 */
class Items extends AbstractItems{

	public function index(){
		$context = Context::getInstance();
		$this->setData('content', "module: ". get_class($this) ." , action: " . $context->getActionName());
		$this->setView('preview_index.tpl');
	}
	
}
?>
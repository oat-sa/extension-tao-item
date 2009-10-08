<?php

class Items extends Module {

	public function index(){
		$this->setData('testVar', "this is the ". get_class($this) ." module");
		
		$this->setView('view.tpl');
	}
}
?>
<?php

/**
 * Hawai Authoring Controller provide actions to edit a QTI item
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoItems_actions_HawaiAuthoring extends tao_actions_CommonModule {
	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){
		
		parent::__construct();
		$this->itemService = tao_models_classes_ServiceFactory::get("Items");
	}
	
	/**
	 * Save hawai item content
	 */
	public function saveItemContent() {
		$_SESSION['xml'] = $_POST['xml'];
		$_SESSION['instance'] = $_POST['instance'];
		header("Location: ".tao_helpers_Uri::url('saveItemContent', 'Items', 'taoItems'));
	}
}

?>

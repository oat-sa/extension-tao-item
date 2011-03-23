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
		if($this->hasRequestParameter("xml") && $this->hasRequestParameter("isntance")){
			$item = $this->itemService->getItem($this->getRequestParameter("isntance"));
			if(!is_null($item) && $this->itemService->isItemModelDefined($item)){
				$item = $this->itemService->setItemContent($item, $data);
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->uriResource));
				$message = __('Item saved successfully');
				if(tao_helpers_Context::check('STANDALONE_MODE')){
					$itemClass = $this->service->getClass($item);
					$this->redirect(_url('authoring', 'SaSItems', 'taoItems', array('uri' => tao_helpers_Uri::encode($item->uriResource).'&classUri='.tao_helpers_Uri::encode($itemClass->uriResource), 'classUri' => tao_helpers_Uri::encode($itemClass->uriResource), 'message' => urlencode($message))));
				}
				else{
					$this->redirect( _url('index', 'Main', 'tao', array('message' => urlencode($message))));
				}
			}
		}
	}
}

?>

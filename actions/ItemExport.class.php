<?php
require_once('tao/actions/Export.class.php');

/**
 * This controller provide the actions to import items 
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @subpackage action
 *
 */
class ItemExport extends Export {

	public function __construct(){
		parent::__construct();
		
		$data = array();
		if($this->hasRequestParameter('classUri')){
			if(trim($this->getRequestParameter('classUri')) != ''){
				$data['class'] = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));
			}
		}
		if($this->hasRequestParameter('uri') && $this->hasRequestParameter('classUri')){
			if(trim($this->getRequestParameter('uri')) != ''){
				$data['item'] = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
			}
		}
		$this->formContainer = new taoItems_actions_form_Export($data);
	}
	
	public function exportXMLData($formValues){
		if($this->hasRequestParameter('name') && $this->hasRequestParameter('instances_0')){
			var_dump($formValues);
			exit;
		}
	}
}
?>
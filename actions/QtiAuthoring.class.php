<?php
/**
 * QTiAuthoring Controller provide actions to edit a QTI item
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class QTiAuthoring extends CommonModule {
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = new taoItems_models_classes_QtiAuthoringService();
		$this->defaultData();
	}
	
	public function index(){
		$itemUri = $this->getRequestParameter('instance'); 
		$itemContent = $this->getRequestParameter('xml');
		
		var_dump($itemUri, $itemContent);
		// $this->setData('htmlbox_wysiwyg_path', BASE_WWW.'js/HtmlBox_4.0/');
		$this->setData('jwysiwyg_path', BASE_WWW.'js/jwysiwyg/');
		$this->setData('simplemodal_path', BASE_WWW.'js/simplemodal/');
		$this->setView("QTIAuthoring/authoring.tpl");
	}
	
	
}
?>
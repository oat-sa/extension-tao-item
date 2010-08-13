<?php
/**
 * SaSItems Controller provide process services for in the Items
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class SaSItems extends Items {

	
    /**
     * @see Items::__construct()
     */
    public function __construct() {
    	tao_helpers_Context::load('STANDALONE_MODE');
        $this->setSessionAttribute('currentExtension', 'taoItems');
		parent::__construct();
    }

	/**
	 * @see TaoModule::setView()
	 * @param string $identifier the view name
	 * @param boolean $useMetaExtensionView use a view from the parent extention
	 * @return mixed 
	 */
    public function setView($identifier, $useMetaExtensionView = false) {
		if(tao_helpers_Request::isAjax()){
			return parent::setView($identifier, $useMetaExtensionView);
		}
    	if($useMetaExtensionView){
			$this->setData('includedView', $identifier);
		}
		else{
			$this->setData('includedView', BASE_PATH . '/' . DIR_VIEWS . $GLOBALS['dir_theme'] . $identifier);
		}
		return parent::setView('sas.tpl', true);
    }
	
	/**
     * overrided to prevent exception: 
     * if no class is selected, the root class is returned 
     * @see TaoModule::getCurrentClass()
     * @return core_kernel_class_Class
     */
    protected function getCurrentClass() {
        if($this->hasRequestParameter('classUri')){
        	return parent::getCurrentClass();
        }
		return $this->getRootClass();
    }
    
	/**
	 * Edit an instances 
	 * @return void
	 */
	public function sasEditInstance(){
		$clazz = $this->getCurrentClass();
		$instance = $this->getCurrentInstance();
		
		
		$formContainer = new tao_actions_form_Instance($clazz, $instance);
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$instance = $this->service->bindProperties($instance, $myForm->getValues());
				$instance = $this->service->setDefaultItemContent($instance);
				$this->setData('message', __('Item saved'));
			}
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($instance->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setData('formTitle', __('Edit item'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', true);
	}
	
	/**
	 * view and item
	 * @return void
	 */
	public function viewItem(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$itemClass = $this->getCurrentClass();
		$item = $this->getCurrentInstance();

		$lang = null;
		if($this->hasRequestParameter('target_lang')){
			$lang = $this->getRequestParameter('target_lang');
		}
		
		$hiddenProperties = array(
			TAO_ITEM_CONTENT_PROPERTY
		);
		
		$properties = array();
		foreach($this->service->getClazzProperties($itemClass) as $property){
			if(in_array($property->uriResource, $hiddenProperties)){
				continue;
			}
			$range = $property->getRange();
			
			if(is_null($lang)){
				$propValues = $item->getPropertyValues($property);
			}
			else{
				$propContainer = $item->getPropertyValuesByLg($property, $lang);
				$propValues = $propContainer->getIterator();
			}
			foreach($propValues as $propValue){	
				$value = '';
				if($range->uriResource == RDFS_LITERAL){
					$value = (string)$propValue;
				}
				else {
					$resource = new core_kernel_classes_Resource($propValue);
					$value = $resource->getLabel();
				}
				$properties[] = array(
					'name'	=> $property->getLabel(),
					'value'	=> $value
				);
			}
		}
		
		$previewData = $this->initPreview($item, $itemClass);
		if(count($previewData) == 0){
			$this->setData('preview', false);
			$this->setData('previewMsg', __("Not yet available"));
		}
		else{
			$this->setData('preview', true);
			$this->setData('instanceUri', tao_helpers_Uri::encode($item->uriResource, false));
			foreach($previewData as $key => $value){
				$this->setData($key, $value);
			}
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($item->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($itemClass->uriResource));
		
		$this->setData('label', $item->getLabel());
		$this->setData('itemProperties', $properties);
		$this->setView('view.tpl');
	}
}
?>
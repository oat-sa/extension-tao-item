<?php
/**
 * SaSItems Controller provide process services
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
        $this->setSessionAttribute('currentExtension', 'taoItems');
		tao_helpers_form_GenerisFormFactory::setMode(tao_helpers_form_GenerisFormFactory::MODE_STANDALONE);
		parent::__construct();
    }

	/**
     * @see TaoModule::setView()
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
		
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $instance);
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
	
	public function viewInstance(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$instance = $this->getCurrentInstance();
		
		$properties = array();
		foreach($this->service->getClazzProperties($clazz) as $property){
			$value = '';
			try{
			$value = $instance->getUniquePropertyValue($property);
			}
			catch(common_Exception $ce){}
			$properties[] = array(
				'name'	=> $property->getLabel(),
				'value'	=> $value
			);
		}
		$this->setData('itemProperties', $properties);
		$this->setView('view.tpl');
	}
}
?>
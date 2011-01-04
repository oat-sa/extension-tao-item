<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/actions/form/class.Export.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 01.12.2010, 16:45:04 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This container initialize the export form.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/actions/form/class.Export.php');

/* user defined includes */
// section 127-0-1-1-70b2308e:12ca2398ae8:-8000:000000000000293A-includes begin
// section 127-0-1-1-70b2308e:12ca2398ae8:-8000:000000000000293A-includes end

/* user defined constants */
// section 127-0-1-1-70b2308e:12ca2398ae8:-8000:000000000000293A-constants begin
// section 127-0-1-1-70b2308e:12ca2398ae8:-8000:000000000000293A-constants end

/**
 * Short description of class taoItems_actions_form_Export
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */
class taoItems_actions_form_Export
    extends tao_actions_form_Export
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute formats
     *
     * @access protected
     * @var array
     */
    protected $formats = array('rdf' => 'RDF', 'xml' => 'XML');

    // --- OPERATIONS ---

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1-2c41a4d:12ca27f7d37:-8000:0000000000002921 begin
        
    	parent::initElements();
    	
    	if(isset($this->data['item'])){
    		$item = $this->data['item'];
    		if($item instanceof core_kernel_classes_Resource){
				//add an hidden elt for the instance Uri
				$uriElt = tao_helpers_form_FormFactory::getElement('uri', 'Hidden');
				$uriElt->setValue($item->uriResource);
				$this->form->addElement($uriElt);
    		}	
    	}
    	if(isset($this->data['class'])){
    		$class = $this->data['class'];
    		if($class instanceof core_kernel_classes_Class){
    			//add an hidden elt for the class uri
				$classUriElt = tao_helpers_form_FormFactory::getElement('classUri', 'Hidden');
				$classUriElt->setValue($class->uriResource);
				$this->form->addElement($classUriElt);
    		}	
    	}
    	
        // section 127-0-1-1-2c41a4d:12ca27f7d37:-8000:0000000000002921 end
    }

    /**
     * Short description of method initXMLElements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initXMLElements()
    {
        // section 127-0-1-1-70b2308e:12ca2398ae8:-8000:000000000000293C begin
        
    	$itemService = tao_models_classes_ServiceFactory::get('Items');
		
		$fileName = '';
    	$options = array();
    	if(isset($this->data['item'])){
    		$item = $this->data['item'];
    		if($item instanceof core_kernel_classes_Resource){
    			$fileName = strtolower(tao_helpers_Display::textCleaner($item->getLabel()));
    			$options[$item->uriResource] = $item->getLabel();
    		}
    	}
    	else {
    		if(isset($this->data['class'])){
	    		$class = $this->data['class'];
	    	}
	    	else{
	    		$class = $itemService->getItemClass();
	    	}
    		if($class instanceof core_kernel_classes_Class){
				$fileName =  strtolower(tao_helpers_Display::textCleaner($class->getLabel()));
				foreach($class->getInstances() as $instance){
					$options[$instance->uriResource] = $instance->getLabel();
				}
    		}
    	}
    	
    	$descElt = tao_helpers_form_FormFactory::getElement('xml_desc', 'Label');
		$descElt->setValue(__("Enables you to export a ZIP archive containing one folder for each exported item. Each  folder is composed by a main XML file (the item's data) and  by externals resources (media, manifests, etc.)"));
		$this->form->addElement($descElt);
    	
    	$instanceElt = tao_helpers_form_FormFactory::getElement('instances', 'Checkbox');
    	$instanceElt->setDescription(__('Items'));
		$instanceElt->setOptions(tao_helpers_Uri::encodeArray($options, tao_helpers_Uri::ENCODE_ARRAY_KEYS));
    	foreach(array_keys($options) as $value){
			$instanceElt->setValue($value);
		}
		$this->form->addElement($instanceElt);
		
    	$nameElt = tao_helpers_form_FormFactory::getElement('filename', 'Textbox');
		$nameElt->setDescription(__('File name'));
		$nameElt->setValue($fileName);
		$nameElt->setUnit(".zip");
		$nameElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
    	$this->form->addElement($nameElt);
    	
    	$this->form->createGroup('options', __('Export Options'), array('xml_desc','instances', 'name'));
    	
        // section 127-0-1-1-70b2308e:12ca2398ae8:-8000:000000000000293C end
    }

} /* end of class taoItems_actions_form_Export */

?>
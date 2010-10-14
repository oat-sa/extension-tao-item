<?php

error_reporting(E_ALL);

/**
 * This container initialize the qti item form:
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/**
 * This container initialize the login form.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */
class taoItems_actions_QTIform_Mapping
    extends tao_helpers_form_FormContainer
{
	
	/**
     * the class resource to create the form from
     *
     * @access protected
     * @var response
     */
	protected $response = null;
	
	public function __construct(taoItems_models_classes_QTI_Response $response){
		if(is_null($response)){
			throw new Exception('the response cannot be null');
		}
		$this->response = $response;
		$returnValue = parent::__construct(array(), array('option1' => ''));
		
	}
	
	/**
     * The method initForm for all type of interaction form
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
		$this->form = tao_helpers_form_FormFactory::getForm('MappingForm');
		
		$actions = tao_helpers_form_FormFactory::getCommonActions('bottom', true, false);
		// $addChoiceElt = tao_helpers_form_FormFactory::getElement('addCustomRule', 'Free');
		// $addChoiceElt->setValue("<a href='#' class='form-choice-adder' ><img src='".TAOBASE_WWW."/img/add.png'  /> ".__('Add a custom rule')."</a>");
		// $actions[] = $addChoiceElt;
		
		$this->form->setActions(array(), 'top');
		$this->form->setActions($actions, 'bottom');
		
    }
	
	public function initElements(){
		
		//add hidden id element, to know what the old id is:
		$responseSerialElt = tao_helpers_form_FormFactory::getElement('responseSerial', 'Hidden');
		$responseSerialElt->setValue($this->response->getSerial());
		$this->form->addElement($responseSerialElt);
		
		//default box:
		$defaultValueElt = tao_helpers_form_FormFactory::getElement('defaultValue', 'Textbox');
		$defaultValueElt->setDescription(__('Default value'));
		$defaultValue = 0;
		$mappingDefaultValue = $this->response->getMappingDefaultValue();
		if(empty($mappingDefaultValue)){
			$this->response->setMappingDefaultValue($defaultValue);
		}else{
			$defaultValue = $mappingDefaultValue;
		}
		$defaultValueElt->setValue($defaultValue);
		$this->form->addElement($defaultValueElt);
		
		//upperbound+lowerbound:
		$upperBoundElt = tao_helpers_form_FormFactory::getElement('upperBound', 'Textbox');
		$upperBoundElt->setDescription(__('Upper bound'));
		
		$lowerBoundElt = tao_helpers_form_FormFactory::getElement('lowerBound', 'Textbox');
		$lowerBoundElt->setDescription(__('Lower bound'));
		
		$mappingOptions = $this->response->getOption('mapping');
		if(is_array($mappingOptions)){
			if(isset($mappingOptions['upperBound'])) $upperBoundElt->setValue($mappingOptions['upperBound']);
			if(isset($mappingOptions['lowerBound'])) $lowerBoundElt->setValue($mappingOptions['lowerBound']);
		}
		$this->form->addElement($upperBoundElt);
		$this->form->addElement($lowerBoundElt);
	
	}
}

?>
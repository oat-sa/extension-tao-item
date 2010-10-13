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
class taoItems_actions_QTIform_ResponseProcessing
    extends tao_helpers_form_FormContainer
{
	
	/**
     * the class resource to create the form from
     *
     * @access protected
     * @var responseProcessing
     */
    protected $responseProcessing = null;
	protected $item = null;
	protected $processingType = '';
	
	public function __construct(taoItems_models_classes_QTI_Item $item){
		$this->item = $item;
		
		$this->responseProcessing = $item->getResponseProcessing();
		// var_dump($item, $this->responseProcessing);
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
		$this->form = tao_helpers_form_FormFactory::getForm('ResponseProcessingForm');
		
		$actions = tao_helpers_form_FormFactory::getCommonActions('bottom', true, false);
		// $addChoiceElt = tao_helpers_form_FormFactory::getElement('addCustomRule', 'Free');
		// $addChoiceElt->setValue("<a href='#' class='form-choice-adder' ><img src='".TAOBASE_WWW."/img/add.png'  /> ".__('Add a custom rule')."</a>");
		// $actions[] = $addChoiceElt;
		
		$this->form->setActions(array(), 'top');
		$this->form->setActions($actions, 'bottom');
		
    }
	
	public function getResponseProcessing(){
		return $this->responseProcessing;
	}
	
	public function getProcessingType(){
		return $this->processingType;
	}
	
	public function initElements(){
		
		//add hidden id element, to know what the old id is:
		$itemSerialElt = tao_helpers_form_FormFactory::getElement('itemSerial', 'Hidden');
		$itemSerialElt->setValue($this->item->getSerial());
		$this->form->addElement($itemSerialElt);
		
		//select box:
		$typeElt = tao_helpers_form_FormFactory::getElement('responseProcessingType', 'Combobox');
		$typeElt->setDescription(__('Processing type'));
		$typeElt->setOptions(array(
			tao_helpers_Uri::encode(QTI_RESPONSE_TEMPLATE_MATCH_CORRECT) => __('match'),
			tao_helpers_Uri::encode(QTI_RESPONSE_TEMPLATE_MAP_RESPONSE) => __('map'),
			tao_helpers_Uri::encode(QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT) => __('map point'),
			'custom' => __('custom')
		));
		$qtiAuthoringService = tao_models_classes_ServiceFactory::get('taoItems_models_classes_QtiAuthoringService');
		try{
			$type = $qtiAuthoringService->getResponseProcessingType($this->responseProcessing);
		}catch(Exception $e){}
		
		if(!empty($type)){
			$this->processingType = $type;
			$typeElt->setValue($type);
		}
		$this->form->addElement($typeElt);
		
		//if the type is a custom one, display the rule editor:
		if(false){
			//the rule id element:
			$ruleElt = tao_helpers_form_FormFactory::getElement('customRule', 'Textarea');
			$ruleElt->setDescription(__('Processing rule:'));
			$ruleElt->setValue($this->responseProcessing->getIdentifier());
			$this->form->addElement($ruleElt);
		}
	
	}
}

?>
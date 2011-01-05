<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\class.ResponseProcessing.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.01.2011, 11:32:48 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @subpackage actions_QTIform
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FC8-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FC8-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FC8-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FC8-constants end

/**
 * Short description of class taoItems_actions_QTIform_ResponseProcessing
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @subpackage actions_QTIform
 */
class taoItems_actions_QTIform_ResponseProcessing
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute responseProcessing
     *
     * @access protected
     * @var ResponseProcessing
     */
    protected $responseProcessing = null;

    /**
     * Short description of attribute item
     *
     * @access protected
     * @var Item
     */
    protected $item = null;

    /**
     * Short description of attribute processingType
     *
     * @access protected
     * @var string
     */
    protected $processingType = '';

    /**
     * Short description of attribute newAttr
     *
     * @access public
     * @var Integer
     */
    public $newAttr = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Item item
     */
    public function __construct( taoItems_models_classes_QTI_Item $item)
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FD3 begin
		
		$this->item = $item;
		
		$this->responseProcessing = $item->getResponseProcessing();
		$returnValue = parent::__construct(array(), array('option1' => ''));
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FD3 end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initForm()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FDA begin
		
		$this->form = tao_helpers_form_FormFactory::getForm('ResponseProcessingForm');
		
		$actions = array();
		$saveElt = tao_helpers_form_FormFactory::getElement('save', 'Free');
		$saveElt->setValue("<a href='#' class='form-submiter' ><img src='".TAOBASE_WWW."/img/save.png'  /> ".__('Apply')."</a>");
		$actions[] = $saveElt;
		
		$this->form->setActions(array(), 'top');
		$this->form->setActions($actions, 'bottom');
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FDA end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FDC begin
		
		//add hidden id element, to know what the old id is:
		$itemSerialElt = tao_helpers_form_FormFactory::getElement('itemSerial', 'Hidden');
		$itemSerialElt->setValue($this->item->getSerial());
		$this->form->addElement($itemSerialElt);
		
		//select box:
		$typeElt = tao_helpers_form_FormFactory::getElement('responseProcessingType', 'Combobox');
		$typeElt->setDescription(__('Processing type'));
		
		$qtiAuthoringService = tao_models_classes_ServiceFactory::get('taoItems_models_classes_QtiAuthoringService');
		try{
			$type = $qtiAuthoringService->getResponseProcessingType($this->responseProcessing);
		}catch(Exception $e){}
		
		if(!empty($type)){
			$this->processingType = $type;//in array('template', 'custom', 'customTemplate')
			$availableOptions = array(
				'template' => __('template')
			);
			if($type == 'custom'||$type == 'customTemplate'){
				$availableOptions[$type] = __($type);
			}
			$typeElt->setOptions($availableOptions);
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
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FDC end
    }

    /**
     * Short description of method getResponseProcessing
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return taoItems_models_classes_QTI_response_ResponseProcessing
     */
    public function getResponseProcessing()
    {
        $returnValue = null;

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FDF begin
		$returnValue = $this->responseProcessing;
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FDF end

        return $returnValue;
    }

    /**
     * Short description of method getProcessingType
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return string
     */
    public function getProcessingType()
    {
        $returnValue = (string) '';

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FE2 begin
		$returnValue = $this->processingType;
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FE2 end

        return (string) $returnValue;
    }

} /* end of class taoItems_actions_QTIform_ResponseProcessing */

?>
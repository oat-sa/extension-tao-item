<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\class.Mapping.php
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
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10066
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
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB7-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB7-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB7-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB7-constants end

/**
 * Short description of class taoItems_actions_QTIform_Mapping
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10066
 * @subpackage actions_QTIform
 */
class taoItems_actions_QTIform_Mapping
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute response
     *
     * @access protected
     * @var Response
     */
    protected $response = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Response response
     */
    public function __construct( taoItems_models_classes_QTI_Response $response)
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FBF begin
		
		if(is_null($response)){
			throw new Exception('the response cannot be null');
		}
		$this->response = $response;
		$returnValue = parent::__construct(array(), array('option1' => ''));
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FBF end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initForm()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FC3 begin
		
		$this->form = tao_helpers_form_FormFactory::getForm('MappingForm');
		
		$actions = array();
		$saveElt = tao_helpers_form_FormFactory::getElement('save', 'Free');
		$saveElt->setValue("<a href='#' class='form-submiter' ><img src='".TAOBASE_WWW."/img/save.png'  /> ".__('Apply')."</a>");
		$actions[] = $saveElt;
		
		$this->form->setActions(array(), 'bottom');
		$this->form->setActions($actions, 'top');
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FC3 end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FC5 begin
		
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
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FC5 end
    }

    /**
     * Short description of method newOperation
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return mixed
     */
    public function newOperation()
    {
        // section 10-13-1-39--340dbb51:12d5574289f:-8000:0000000000002F5F begin
        // section 10-13-1-39--340dbb51:12d5574289f:-8000:0000000000002F5F end
    }

} /* end of class taoItems_actions_QTIform_Mapping */

?>
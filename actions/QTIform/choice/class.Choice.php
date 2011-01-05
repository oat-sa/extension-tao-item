<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\choice\class.Choice.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.01.2011, 11:32:50 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10254
 * @subpackage actions_QTIform_choice
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
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FE7-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FE7-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FE7-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FE7-constants end

/**
 * Short description of class taoItems_actions_QTIform_choice_Choice
 *
 * @abstract
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10254
 * @subpackage actions_QTIform_choice
 */
abstract class taoItems_actions_QTIform_choice_Choice
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute choice
     *
     * @access protected
     * @var Data
     */
    protected $choice = null;

    /**
     * Short description of attribute formName
     *
     * @access protected
     * @var string
     */
    protected $formName = 'ChoiceForm_';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Data choice
     */
    public function __construct( taoItems_models_classes_QTI_Data $choice = null)
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FF4 begin
		
		if(!is_null($choice)){
			$this->choice = $choice;
			if($choice instanceof taoItems_models_classes_QTI_Group){
				$this->formName = 'GroupForm_'.$this->choice->getSerial();
			}else{
				$this->formName = 'ChoiceForm_'.$this->choice->getSerial();
			}
			
		}
		$returnValue = parent::__construct(array(), array());
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FF4 end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initForm()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FF6 begin
		
		$this->form = tao_helpers_form_FormFactory::getForm($this->formName);
		$this->form->setActions(array(), 'bottom');
		//no save elt required, all shall be done with ajax request
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FF6 end
    }

    /**
     * Short description of method setCommonElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function setCommonElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FFE begin
		
		//add hidden id element, to know what the old id is:
		if($this->choice instanceof taoItems_models_classes_QTI_Group){
			$oldIdElt = tao_helpers_form_FormFactory::getElement('groupSerial', 'Hidden');
		}else{
			$oldIdElt = tao_helpers_form_FormFactory::getElement('choiceSerial', 'Hidden');
		}
		$oldIdElt->setValue($this->choice->getSerial());
		$this->form->addElement($oldIdElt);
		
		//id element: need for checking unicity
		$labelElt = tao_helpers_form_FormFactory::getElement('choiceIdentifier', 'Textbox');
		$labelElt->setDescription(__('Identifier'));
		$labelElt->setValue($this->choice->getIdentifier());
		$this->form->addElement($labelElt);
		
		//the fixed attribute element
		$fixedElt = tao_helpers_form_FormFactory::getElement('fixed', 'Checkbox');
		$fixedElt->setDescription(__('Fixed'));
		$fixedElt->setOptions(array('true' => ''));//empty label because the description of the element is enough
		$fixed = $this->choice->getOption('fixed');
		if(!empty($fixed)){
			if($fixed === 'true' || $fixed === true){
				$fixedElt->setValue('true');
			}
		}
		$this->form->addElement($fixedElt);
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FFE end
    }

    /**
     * Short description of method getChoice
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return taoItems_models_classes_QTI_Data
     */
    public function getChoice()
    {
        $returnValue = null;

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005001 begin
		$returnValue = $this->choice;
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005001 end

        return $returnValue;
    }

} /* end of abstract class taoItems_actions_QTIform_choice_Choice */

?>
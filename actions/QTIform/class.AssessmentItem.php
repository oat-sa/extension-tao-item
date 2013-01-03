<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\class.AssessmentItem.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.01.2011, 11:32:47 with ArgoUML PHP module
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10010
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
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E23-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E23-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E23-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E23-constants end

/**
 * Short description of class taoItems_actions_QTIform_AssessmentItem
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10010
 * @subpackage actions_QTIform
 */
class taoItems_actions_QTIform_AssessmentItem
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute item
     *
     * @access protected
     * @var Item
     */
    protected $item = null;

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
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E2A begin
		$this->item = $item;
		$returnValue = parent::__construct(array(), array());
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E2A end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initForm()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E2D begin

		$this->form = tao_helpers_form_FormFactory::getForm('AssessmentItem_Form');

		$actions = array();

		$this->form->setActions($actions, 'top');
		$this->form->setActions(array(), 'bottom');

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E2D end
    }

    /**
     * Short description of method getItem
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return taoItems_models_classes_QTI_Item
     */
    public function getItem()
    {
        $returnValue = null;

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E2F begin
		$returnValue = $this->item;
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E2F end

        return $returnValue;
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E31 begin

		//serial
		$serialElt = tao_helpers_form_FormFactory::getElement('itemSerial', 'Hidden');
		$serialElt->setValue($this->item->getSerial());
		$this->form->addElement($serialElt);

		//title:
		$titleElt = tao_helpers_form_FormFactory::getElement('title', 'Textbox');
		$titleElt->setDescription(__('Title'));
		$titleElt->setValue($this->item->getOption('title'));
		$this->form->addElement($titleElt);

		//label: not used, instead rather confusing for users
//		$labelElt = tao_helpers_form_FormFactory::getElement('label', 'Textbox');
//		$labelElt->setDescription(__('Label'));
//		$labelElt->setValue($this->item->getOption('label'));
//		$this->form->addElement($labelElt);
		
		//@TODO : funcitons not available yet, to be implemented
//		$this->form->addElement(self::createBooleanElement($this->item, 'timeDependent', 'Time dependent'));
//		$this->form->addElement(self::createBooleanElement($this->item, 'adaptive', ''));

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E31 end
    }

    /**
     * Short description of method createBooleanElement
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Data qtiObject
     * @param  string optionName
     * @param  string elementLabel
     * @param  array boolean
     * @return tao_helpers_form_FormElement
     */
    public static function createBooleanElement( taoItems_models_classes_QTI_Data $qtiObject, $optionName, $elementLabel = '', $boolean = array('no', 'yes'))
    {
        $returnValue = null;

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E33 begin

		if(count($boolean) != 2){
			throw new Exception('invalid number of elements in boolean array definition');
		}
		$returnValue = tao_helpers_form_FormFactory::getElement($optionName, 'Radiobox');

		if(empty($elementLabel)) $elementLabel = __(ucfirst(strtolower($optionName)));
		$returnValue->setDescription($elementLabel);
		$returnValue->setOptions(array('true'=>$boolean[1], 'false' => $boolean[0]));

		$optionValue = $qtiObject->getOption($optionName);

		$returnValue->setValue('false');
		if(!empty($optionValue)){
			if($optionValue === 'true' || $optionValue === true){
				$returnValue->setValue('true');
			}
		}

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E33 end

        return $returnValue;
    }

    /**
     * Short description of method createTextboxElement
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Data qtiObject
     * @param  string optionName
     * @param  string elementLabel
     * @return tao_helpers_form_FormElement
     */
    public static function createTextboxElement( taoItems_models_classes_QTI_Data $qtiObject, $optionName, $elementLabel = '')
    {
        $returnValue = null;

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E3D begin

		$returnValue = tao_helpers_form_FormFactory::getElement($optionName, 'Textbox');
		if(empty($elementLabel)) $elementLabel = __(ucfirst(strtolower($optionName)));
		$returnValue->setDescription($elementLabel);

		//validator: is int??
		$value = (string) $qtiObject->getOption($optionName);
		if(!is_null($value)){
			$returnValue->setValue($value);
		}

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000002E3D end

        return $returnValue;
    }

} /* end of class taoItems_actions_QTIform_AssessmentItem */

?>
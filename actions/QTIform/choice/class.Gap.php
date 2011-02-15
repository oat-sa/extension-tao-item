<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\choice\class.Gap.php
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
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10286
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
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000502C-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000502C-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000502C-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000502C-constants end

/**
 * Short description of class taoItems_actions_QTIform_choice_Gap
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10286
 * @subpackage actions_QTIform_choice
 */
class taoItems_actions_QTIform_choice_Gap
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute group
     *
     * @access protected
     * @var Group
     */
    protected $group = null;

    /**
     * Short description of attribute interaction
     *
     * @access protected
     * @var Interaction
     */
    protected $interaction = null;

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
     * @param  Group group
     */
    public function __construct( taoItems_models_classes_QTI_Group $group)
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000503A begin
		
		$this->group = $group;
		$this->formName = 'GroupForm_'.$this->group->getSerial();//GroupForm...however it is considered as a choice
		
		$qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
		$interaction = $qtiService->getComposingData($group);
		if($interaction instanceof taoItems_models_classes_QTI_Interaction){
			$this->interaction = $interaction;
		}else{
			var_dump($group, $interaction);
			throw new Exception('cannot find the parent interaction');
		}
		
		$returnValue = parent::__construct(array(), array());
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000503A end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initForm()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000503C begin
		
		$this->form = tao_helpers_form_FormFactory::getForm($this->formName);
		$this->form->setActions(array(), 'bottom');
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000503C end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000503E begin
		
		//reimplement the matchGroup element with the group:
		//add hidden id element, to know what the old id is:
		$oldIdElt = tao_helpers_form_FormFactory::getElement('groupSerial', 'Hidden');
		$oldIdElt->setValue($this->group->getSerial());
		$this->form->addElement($oldIdElt);
		
		//id element: need for checking unicity
		$labelElt = tao_helpers_form_FormFactory::getElement('groupIdentifier', 'Textbox');
		$labelElt->setDescription(__('Identifier'));
		$labelElt->setValue($this->group->getIdentifier());
		$this->form->addElement($labelElt);
		
		//the fixed attribute element
		$fixedElt = tao_helpers_form_FormFactory::getElement('fixed', 'Checkbox');
		$fixedElt->setDescription(__('Fixed'));
		$fixedElt->setOptions(array('true' => ''));//empty label because the description of the element is enough
		$fixed = $this->group->getOption('fixed');
		if(!empty($fixed)){
			if($fixed === 'true' || $fixed === true){
				$fixedElt->setValue('true');
			}
		}
		$this->form->addElement($fixedElt);
		
		//associable choice special property:
		$matchGroupElt = tao_helpers_form_FormFactory::getElement('matchGroup', 'Checkbox');
		$matchGroupElt->setDescription(__('Match group'));
		$options = array();
		foreach($this->interaction->getChoices() as $choice){
			$options[$choice->getIdentifier()] = $choice->getIdentifier();
		}
		$matchGroupElt->setOptions($options);
		//the default empty value indicates to the authoring controller that there is no restriction to the associated choices
		$qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
		foreach($this->group->getChoices() as $choiceSerial){
			$choice = $qtiService->getDataBySerial($choiceSerial, 'taoItems_models_classes_QTI_Choice');
			$matchGroupElt->setValue($choice->getIdentifier());
		}
		
		$this->form->addElement($matchGroupElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->group->getSerial(), __('Advanced properties'), array('fixed', 'matchGroup'));
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000503E end
    }

} /* end of class taoItems_actions_QTIform_choice_Gap */

?>
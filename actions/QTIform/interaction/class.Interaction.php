<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\interaction\class.Interaction.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.01.2011, 11:32:49 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
 * @subpackage actions_QTIform_interaction
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
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005058-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005058-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005058-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005058-constants end

/**
 * Short description of class taoItems_actions_QTIform_interaction_Interaction
 *
 * @abstract
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
 * @subpackage actions_QTIform_interaction
 */
abstract class taoItems_actions_QTIform_interaction_Interaction
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute interaction
     *
     * @access protected
     * @var Interaction
     */
    protected $interaction = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Interaction interaction
     * @param  array choices
     */
    public function __construct( taoItems_models_classes_QTI_Interaction $interaction, $choices)
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000505C begin
		
		$this->interaction = $interaction;
		$returnValue = parent::__construct(array(), array('choices'=>$choices));
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000505C end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initForm()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005060 begin
		
		$interactionType = $this->interaction->getType();
		$this->form = tao_helpers_form_FormFactory::getForm('InteractionForm');
		
		//custom actions only:
		$actions = array();
		
		$saveElt = tao_helpers_form_FormFactory::getElement('save', 'Free');
		$saveElt->setValue("<a href='#' class='interaction-form-submitter' ><img src='".BASE_WWW."img/qtiAuthoring/update.png'  /> ".__('Update interaction & choices modifications')."</a>");
		$actions[] = $saveElt;
		
		$this->form->setActions($actions, 'top');
		$this->form->setActions(array(), 'bottom');
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005060 end
    }

    /**
     * Short description of method getInteraction
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return taoItems_models_classes_QTI_Interaction
     */
    public function getInteraction()
    {
        $returnValue = null;

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005062 begin
		$returnValue = $this->interaction;
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005062 end

        return $returnValue;
    }

    /**
     * Short description of method setCommonElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function setCommonElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005064 begin
		
		//add hidden serial element:
		$oldIdElt = tao_helpers_form_FormFactory::getElement('interactionSerial', 'Hidden');
		$oldIdElt->setValue($this->interaction->getSerial());
		$this->form->addElement($oldIdElt);
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005064 end
    }

} /* end of abstract class taoItems_actions_QTIform_interaction_Interaction */

?>
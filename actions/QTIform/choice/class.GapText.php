<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\choice\class.GapText.php
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
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10289
 * @subpackage actions_QTIform_choice
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_actions_QTIform_choice_AssociableChoice
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10257
 */
require_once('taoItems/actions/QTIform/choice/class.AssociableChoice.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005041-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005041-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005041-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005041-constants end

/**
 * Short description of class taoItems_actions_QTIform_choice_GapText
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10289
 * @subpackage actions_QTIform_choice
 */
class taoItems_actions_QTIform_choice_GapText
    extends taoItems_actions_QTIform_choice_AssociableChoice
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005042 begin
		
		parent::setCommonElements();
		
		//add Textbox:
		$dataElt = tao_helpers_form_FormFactory::getElement('data', 'Textbox');
		$dataElt->setDescription(__('Value'));
		$choiceData = $this->choice->getData();
		if(!empty($choiceData)){
			$dataElt->setValue($choiceData);
		}
		$this->form->addElement($dataElt);
		
		$matchMaxElt = tao_helpers_form_FormFactory::getElement('matchMax', 'Textbox');
		$matchMaxElt->setDescription(__('Maximal number of matching'));
		$matchMax = (string) $this->choice->getOption('matchMax');
		$matchMaxElt->setValue($matchMax);
		$this->form->addElement($matchMaxElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('fixed', 'matchMax', 'matchGroup'));
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005042 end
    }

    /**
     * Short description of method getMatchGroupOptions
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return array
     */
    public function getMatchGroupOptions()
    {
        $returnValue = array();

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005049 begin
		
		foreach($this->interaction->getGroups() as $group){
			$returnValue[$group->getIdentifier()] = $group->getIdentifier();
		}
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005049 end

        return (array) $returnValue;
    }

} /* end of class taoItems_actions_QTIform_choice_GapText */

?>
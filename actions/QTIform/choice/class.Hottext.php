<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\choice\class.Hottext.php
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
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10310
 * @subpackage actions_QTIform_choice
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_actions_QTIform_choice_Choice
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10254
 */
require_once('taoItems/actions/QTIform/choice/class.Choice.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000500F-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000500F-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000500F-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000500F-constants end

/**
 * Short description of class taoItems_actions_QTIform_choice_Hottext
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10310
 * @subpackage actions_QTIform_choice
 */
class taoItems_actions_QTIform_choice_Hottext
    extends taoItems_actions_QTIform_choice_Choice
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
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005010 begin
		
		parent::setCommonElements();
		
		//add Textbox:
		$dataElt = tao_helpers_form_FormFactory::getElement('data', 'Textbox');//the widget for an inline choice data is a text box!!
		$dataElt->setDescription(__('Value'));
		$choiceData = $this->choice->getData();
		if(!empty($choiceData)){
			$dataElt->setValue($choiceData);
		}
		$this->form->addElement($dataElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('fixed'));
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005010 end
    }

} /* end of class taoItems_actions_QTIform_choice_Hottext */

?>
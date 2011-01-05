<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\interaction\class.AssociateInteraction.php
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
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10269
 * @subpackage actions_QTIform_interaction
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_actions_QTIform_interaction_BlockInteraction
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10250
 */
require_once('taoItems/actions/QTIform/interaction/class.BlockInteraction.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000506F-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000506F-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000506F-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000506F-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10269
 * @subpackage actions_QTIform_interaction
 */
class taoItems_actions_QTIform_interaction_AssociateInteraction
    extends taoItems_actions_QTIform_interaction_BlockInteraction
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
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005070 begin
		
		$interaction = $this->getInteraction();
		
		parent::setCommonElements();
				
		$this->form->addElement(taoItems_actions_QTIform_AssessmentItem::createBooleanElement($interaction, 'shuffle', __('Shuffle choices')));
		
		$this->form->addElement(taoItems_actions_QTIform_AssessmentItem::createTextboxElement($interaction, 'maxAssociations', __('Maximum number of associations')));
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005070 end
    }

} /* end of class taoItems_actions_QTIform_interaction_AssociateInteraction */

?>
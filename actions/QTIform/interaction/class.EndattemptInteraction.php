<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\interaction\class.EndattemptInteraction.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 31.01.2011, 17:28:13 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @subpackage actions_QTIform_interaction
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_actions_QTIform_interaction_Interaction
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
 */
require_once('taoItems/actions/QTIform/interaction/class.Interaction.php');

/* user defined includes */
// section 10-13-1-39--1553ee98:12ddcd3839e:-8000:0000000000003009-includes begin
// section 10-13-1-39--1553ee98:12ddcd3839e:-8000:0000000000003009-includes end

/* user defined constants */
// section 10-13-1-39--1553ee98:12ddcd3839e:-8000:0000000000003009-constants begin
// section 10-13-1-39--1553ee98:12ddcd3839e:-8000:0000000000003009-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @subpackage actions_QTIform_interaction
 */
class taoItems_actions_QTIform_interaction_EndattemptInteraction
    extends taoItems_actions_QTIform_interaction_Interaction
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return mixed
     */
    public function initElements()
    {
        // section 10-13-1-39--1553ee98:12ddcd3839e:-8000:000000000000300B begin
		parent::setCommonElements();
		$this->form->addElement(taoItems_actions_QTIform_AssessmentItem::createTextboxElement($this->getInteraction(), 'title', __('Title')));//mendatory
        // section 10-13-1-39--1553ee98:12ddcd3839e:-8000:000000000000300B end
    }

} /* end of class taoItems_actions_QTIform_interaction_EndattemptInteraction */

?>
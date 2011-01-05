<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\response\class.ExtendedtextInteraction.php
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
 * @subpackage actions_QTIform_response
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_actions_QTIform_response_StringInteraction
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('taoItems/actions/QTIform/response/class.StringInteraction.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050DC-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050DC-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050DC-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050DC-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @subpackage actions_QTIform_response
 */
class taoItems_actions_QTIform_response_ExtendedtextInteraction
    extends taoItems_actions_QTIform_response_StringInteraction
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
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050DD begin
		
		parent::setCommonElements();
		
		//the fixed attribute element
		$orderedCardinalityElt = tao_helpers_form_FormFactory::getElement('ordered', 'Radiobox');
		$orderedCardinalityElt->setDescription(__('Ordered response'));
		$orderedCardinalityElt->setOptions(array(0 => __('no'), 1 => __('yes')));
		$orderedCardinalityElt->setValue(0);
		$cardinality = $this->response->getOption('cardinality');
		if(!empty($cardinality)){
			if($cardinality == 'ordered'){
				$orderedCardinalityElt->setValue(1);
			}
		}
		$this->form->addElement($orderedCardinalityElt);
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050DD end
    }

} /* end of class taoItems_actions_QTIform_response_ExtendedtextInteraction */

?>
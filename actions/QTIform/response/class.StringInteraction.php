<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\response\class.StringInteraction.php
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
 * include taoItems_actions_QTIform_response_Response
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10074
 */
require_once('taoItems/actions/QTIform/response/class.Response.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050D4-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050D4-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050D4-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050D4-constants end

/**
 * Short description of class
 *
 * @abstract
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @subpackage actions_QTIform_response
 */
abstract class taoItems_actions_QTIform_response_StringInteraction
    extends taoItems_actions_QTIform_response_Response
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method setCommonElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return mixed
     */
    public function setCommonElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050D5 begin
		parent::setCommonElements();
		
		$baseTypeElt = tao_helpers_form_FormFactory::getElement('baseType', 'Radiobox');
		$baseTypeElt->setDescription(__('Response variable type'));
		$options = array(
			'string' => __('String'),
			'integer' => __('Integer'),
			'float' => __('Float')
		);
		$baseTypeElt->setOptions($options);
		$baseType = $this->response->getOption('baseType');
		if(!empty($baseType)){
			if(in_array($baseType, array_keys($options))){
				$baseTypeElt->setValue($baseType);
			}else{
				$baseTypeElt->setValue('string');
			}
		}
		$this->form->addElement($baseTypeElt);
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050D5 end
    }

} /* end of abstract class taoItems_actions_QTIform_response_StringInteraction */

?>
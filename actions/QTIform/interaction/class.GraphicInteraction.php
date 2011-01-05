<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\interaction\class.GraphicInteraction.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.01.2011, 11:32:51 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10319
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
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000509C-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000509C-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000509C-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000509C-constants end

/**
 * Short description of class
 *
 * @abstract
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10319
 * @subpackage actions_QTIform_interaction
 */
abstract class taoItems_actions_QTIform_interaction_GraphicInteraction
    extends taoItems_actions_QTIform_interaction_BlockInteraction
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method setCommonElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function setCommonElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000509D begin
		
		parent::setCommonElements();
		
		$object = $this->interaction->getObject();
		
		//add the object form:
		$objectSrcElt = tao_helpers_form_FormFactory::getElement('object_data', 'Textbox');
		$objectSrcElt->setAttribute('class', 'qti-file-img');
		$objectSrcElt->setDescription(__('Image source url'));
		
		$objectWidthElt = tao_helpers_form_FormFactory::getElement('object_width', 'Textbox');
		$objectWidthElt->setDescription(__('Image width'));
		
		$objectHeightElt = tao_helpers_form_FormFactory::getElement('object_height', 'Textbox');
		$objectHeightElt->setDescription(__('Image height'));
		
		//note: no type element since it must be determined by the image type
		
		if(is_array($object)){
			if(isset($object['data'])){
				$objectSrcElt->setValue($object['data']);
			}
			if(isset($object['width'])){
				$objectWidthElt->setValue($object['width']);
			}
			if(isset($object['height'])){
				$objectHeightElt->setValue($object['height']);
			}
		}
		
		$this->form->addElement($objectSrcElt);
		$this->form->addElement($objectWidthElt);
		$this->form->addElement($objectHeightElt);
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000509D end
    }

} /* end of abstract class taoItems_actions_QTIform_interaction_GraphicInteraction */

?>
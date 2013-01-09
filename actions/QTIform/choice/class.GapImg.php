<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\choice\class.GapImg.php
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
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10290
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
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000504C-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000504C-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000504C-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000504C-constants end

/**
 * Short description of class taoItems_actions_QTIform_choice_GapImg
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10290
 * @subpackage actions_QTIform_choice
 */
class taoItems_actions_QTIform_choice_GapImg
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
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000504D begin
		
		parent::setCommonElements();
		
		$object = $this->choice->getObject();
		
		//the image label: 
		$objectLabelElt = tao_helpers_form_FormFactory::getElement('objectLabel', 'Textbox');
		$objectLabelElt->setDescription(__('Image label'));
		$objectLabel = (string) $this->choice->getOption('objectLabel');
		$objectLabelElt->setValue($objectLabel);
		$this->form->addElement($objectLabelElt);
		
		//add the object form:
		$objectSrcElt = tao_helpers_form_FormFactory::getElement('object_data', 'Textbox');
		$objectSrcElt->setAttribute('class', 'qti-file-img qti-with-preview qti-with-resizer');
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
		
		$matchMaxElt = tao_helpers_form_FormFactory::getElement('matchMax', 'Textbox');
		$matchMaxElt->setDescription(__('Maximal number of matching'));
		$matchMax = (string) $this->choice->getOption('matchMax');
		$matchMaxElt->setValue($matchMax);
		$this->form->addElement($matchMaxElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('fixed', 'object_width', 'object_height', 'matchGroup', 'matchMax'));
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000504D end
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

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000504F begin
		foreach($this->interaction->getGroups() as $group){
			$returnValue[$group->getIdentifier()] = $group->getIdentifier();
		}
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000504F end

        return (array) $returnValue;
    }

} /* end of class taoItems_actions_QTIform_choice_GapImg */

?>
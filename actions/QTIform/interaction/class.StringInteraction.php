<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\interaction\class.StringInteraction.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 13.01.2011, 09:33:58 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10297
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
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005089-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005089-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005089-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005089-constants end

/**
 * Short description of class
 *
 * @abstract
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10297
 * @subpackage actions_QTIform_interaction
 */
abstract class taoItems_actions_QTIform_interaction_StringInteraction
    extends taoItems_actions_QTIform_interaction_Interaction
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
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000508E begin
		
		$interaction = $this->getInteraction();
		$response = $interaction->getResponse();
		$isNumeric = false;
		if(!is_null($response)){
			if($response->getOption('baseType') == 'integer' || $response->getOption('baseType') == 'float'){
				$isNumeric = true;
			}
		}
		
		parent::setCommonElements();
		
		$baseElt = tao_helpers_form_FormFactory::getElement('base', 'Textbox');
		$baseElt->setDescription(__('Number base for value interpretation'));
		$baseElt->addValidator(tao_helpers_form_FormFactory::getValidator('Integer'));
		$base = $interaction->getOption('base');
		if(!$isNumeric){
			$baseElt->addAttribute('disabled', true);
			if(!empty($base)){
				$baseElt->setValue($base);
			}
		}
		else{
			if(!empty($base)){
				$baseElt->setValue($base);
			}
			else{
				$baseElt->setValue(10);
			}
		}
		$this->form->addElement($baseElt);
		
		$stringIdentifierElt = tao_helpers_form_FormFactory::getElement('stringIdentifier', 'Textbox');
		$stringIdentifierElt->setDescription(__('String identifier'));
		$stringIdentifier = $interaction->getOption('stringIdentifier');
		if(!$isNumeric){
			$stringIdentifierElt->addAttribute('disabled', true);
		}		
		
		if(!empty($stringIdentifier)){
			$stringIdentifierElt->setValue($stringIdentifier);
		}
		$this->form->addElement($stringIdentifierElt);
		
		$expectedLengthElt = tao_helpers_form_FormFactory::getElement('expectedLength', 'Textbox');
		$expectedLengthElt->setDescription(__('Expected length'));
		$expectedLengthElt->addValidator(tao_helpers_form_FormFactory::getValidator('Integer'));
		$expectedLength = $interaction->getOption('expectedLength');
		if(!empty($expectedLength)){
			$expectedLengthElt->setValue($expectedLength);
		}
		$this->form->addElement($expectedLengthElt);
		
		$patternMaskElt = tao_helpers_form_FormFactory::getElement('patternMask', 'Textbox');
		$patternMaskElt->setDescription(__('Pattern mask'));
		$patternMask = $interaction->getOption('patternMask');
		if(!empty($patternMask)){
			$patternMaskElt->setValue($patternMask);
		}
		$this->form->addElement($patternMaskElt);
		
		$placeHolderTextElt = tao_helpers_form_FormFactory::getElement('placeHolderText', 'Textbox');
		$placeHolderTextElt->setDescription(__('Place holder text'));
		$placeHolderText = $interaction->getOption('placeHolderText');
		if(!empty($placeHolderText)){
			$placeHolderTextElt->setValue($placeHolderText);
		}
		$this->form->addElement($placeHolderTextElt);
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000508E end
    }

    /**
     * Short description of method newOperation
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return mixed
     */
    public function newOperation()
    {
        // section 10-13-1-39--340dbb51:12d5574289f:-8000:0000000000002F66 begin
        // section 10-13-1-39--340dbb51:12d5574289f:-8000:0000000000002F66 end
    }

} /* end of abstract class taoItems_actions_QTIform_interaction_StringInteraction */

?>
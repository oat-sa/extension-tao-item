<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\choice\class.SimpleAssociableChoice.php
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
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10277
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
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005020-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005020-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005020-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005020-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10277
 * @subpackage actions_QTIform_choice
 */
class taoItems_actions_QTIform_choice_SimpleAssociableChoice
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
     * @return mixed
     */
    public function initElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005022 begin
		
		parent::setCommonElements();
		
		//add other elements if needed:
		
		//add textarea:
		$dataElt = tao_helpers_form_FormFactory::getElement('data', 'Textarea');//should be an htmlarea... need to solve the conflict with the 
		$dataElt->setDescription(__('Value'));
		$dataElt->setAttribute('class', 'qti-html-area');
		$choiceData = taoItems_models_classes_QtiAuthoringService::getFilteredData($this->choice);
		if(!empty($choiceData)){
			$dataElt->setValue($choiceData);
		}
		$this->form->addElement($dataElt);
		
		$matchMaxElt = tao_helpers_form_FormFactory::getElement('matchMax', 'Textbox');
		$matchMaxElt->setDescription(__('Maximal number of matching'));
		$matchMax = $this->choice->getOption('matchMax');
		// if(!empty($matchMax)){
			$matchMaxElt->setValue($matchMax);//mandatory!
		// }
		$this->form->addElement($matchMaxElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('fixed', 'matchMax', 'matchGroup'));
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005022 end
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

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000502A begin
		
		switch(strtolower($this->interaction->getType())){
			case 'associate':{
				foreach($this->interaction->getChoices() as $choice){
					$returnValue[$choice->getIdentifier()] = $choice->getIdentifier();
				}
				break;
			}
			case 'match':{
			
				$groups = $this->interaction->getGroups();
				
				//find the current group:
				$currentGroupSerial = '';
				$choicesInAnotherGroup = array();
				
				foreach($groups as $group){
					$choices = $group->getChoices();
					if(in_array($this->choice->getSerial(), $choices)){
						$currentGroupSerial = $group->getSerial();
					}else{
						$choicesInAnotherGroup = array_merge($choicesInAnotherGroup, $choices);
					}
				}
				if(!empty($currentGroupSerial)){
					$qtiService = taoItems_models_classes_QTI_Service::singleton();
					
					$choicesInAnotherGroup = array_unique($choicesInAnotherGroup);
					foreach($choicesInAnotherGroup as $choiceSerial){
						$choice = $qtiService->getDataBySerial($choiceSerial, 'taoItems_models_classes_QTI_Choice');
						if(!is_null($choice)){
							$returnValue[$choice->getIdentifier()] = $choice->getIdentifier();
						}
					}
				}
				break;
			}
		}
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:000000000000502A end

        return (array) $returnValue;
    }

} /* end of class taoItems_actions_QTIform_choice_SimpleAssociableChoice */

?>
<?php

error_reporting(E_ALL);

/**
 * This container initialize the qti item form:
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This container initialize the login form.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */
class taoItems_actions_QTIform_choice_SimpleAssociableChoice
    extends taoItems_actions_QTIform_choice_AssociableChoice{
	
	public function initElements(){
		
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
	}
	
	public function getMatchGroupOptions(){
	
		$returnValue = array();
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
					$qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
					
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
				
		return $returnValue;
		
	}

}

?>
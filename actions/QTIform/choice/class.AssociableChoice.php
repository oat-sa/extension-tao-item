<?php

error_reporting(E_ALL);

/**
 * This container initialize the qti choice form:
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions_form
 */
abstract class taoItems_actions_QTIform_choice_AssociableChoice
    extends taoItems_actions_QTIform_choice_Choice{
	
	/**
     * the class resource to create the form from
     *
     * @access protected
     * @var interaction
     */
    protected $interaction = null;
	
	public function __construct(taoItems_models_classes_QTI_Choice $choice){
		$qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
		$interaction = $qtiService->getComposingData($choice);
		if($interaction instanceof taoItems_models_classes_QTI_Interaction){
			$this->interaction = $interaction;
		}else{
			throw new Exception('cannot find the parent interaction');
		}
		
		$returnValue = parent::__construct($choice);
		
	}
	
	public function setCommonElements(){
		
		parent::setCommonElements();
		
		$matchGroupElt = tao_helpers_form_FormFactory::getElement('matchGroup', 'Checkbox');
		$matchGroupElt->setDescription(__('Match Group'));
		$matchGroupOption = $this->getMatchGroupOptions();
		$matchGroupElt->setOptions($matchGroupOption);
		
		$matchGroups = $this->choice->getOption('matchGroup');
		if(!empty($matchGroups)){
			if(is_array($matchGroups)){
				foreach($matchGroups as $choiceSerial){
					$matchGroupElt->setValue($choiceSerial);
				}
			}else{
				$matchGroupElt->setValue((string)$matchGroups);
			}
		}else{
			//default empty values indicates to the authoring controller that there is no restriction to the associated choices
			foreach($matchGroupOption as $choiceSerial=>$choiceIdentifier){
				$matchGroupElt->setValue($choiceSerial);
			}
		}
		$this->form->addElement($matchGroupElt);
		
	}
	
	public function getMatchGroupOptions(){
	
		$returnValue = array();
		$groups = $this->interaction->getGroups();
		if(empty($groups)){
			foreach($this->interaction->getChoices() as $choice){
				$returnValue[$choice->getSerial()] = $choice->getIdentifier();
			}
		}else{
			//get the current group:
			$currentGroupSerial = '';
			$options = array();
			foreach($groups as $group){
				$choices = $group->getChoices();
				if(in_array($this->choice->getSerial(), $choices)){
					$currentGroupSerial = $group->getSerial();
				}else{
					$options = array_merge($options, $choices);
				}
			}
			if(!empty($currentGroupSerial)){
				$qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
				
				$options = array_unique($options);
				foreach($options as $choiceSerial){
					$choice = $qtiService->getDataBySerial($choiceSerial, 'taoItems_models_classes_QTI_Choice');
					if(!is_null($choice)){
						$returnValue[$choice->getSerial()] = $choice->getIdentifier();
					}
				}
				
			}
		}
		
		
		return $returnValue;
		
	}

}

?>
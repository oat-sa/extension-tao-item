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
class taoItems_actions_QTIform_choice_GapText
    extends taoItems_actions_QTIform_choice_AssociableChoice{
	
	public function initElements(){
		
		parent::setCommonElements();
		
		//add Textbox:
		$dataElt = tao_helpers_form_FormFactory::getElement('data', 'Textbox');
		$dataElt->setDescription(__('Value'));
		$choiceData = $this->choice->getData();
		if(!empty($choiceData)){
			$dataElt->setValue($choiceData);
		}
		$this->form->addElement($dataElt);
		
		$matchMaxElt = tao_helpers_form_FormFactory::getElement('matchMax', 'Textbox');
		$matchMaxElt->setDescription(__('Maximal number of matching'));
		$matchMax = $this->choice->getOption('matchMax');
		if(!empty($matchMax)){
			$matchMaxElt->setValue($matchMax);
		}
		$this->form->addElement($matchMaxElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('fixed', 'matchMax', 'matchGroup'));
	}
	
	public function getMatchGroupOptions(){
	
		$options = array();
		
		foreach($this->interaction->getGroups() as $group){
			$options[$group->getIdentifier()] = $group->getIdentifier();
		}
		
		return $options;
		
	}

}

?>
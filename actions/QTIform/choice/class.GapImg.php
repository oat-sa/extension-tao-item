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
class taoItems_actions_QTIform_choice_GapImg
    extends taoItems_actions_QTIform_choice_AssociableChoice{
	
	public function initElements(){
		
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
		
		$matchMaxElt = tao_helpers_form_FormFactory::getElement('matchMax', 'Textbox');
		$matchMaxElt->setDescription(__('Maximal number of matching'));
		$matchMax = (string) $this->choice->getOption('matchMax');
		$matchMaxElt->setValue($matchMax);
		$this->form->addElement($matchMaxElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('fixed', 'object_width', 'object_height', 'matchGroup', 'matchMax'));
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
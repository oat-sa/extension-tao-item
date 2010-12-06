<?php

error_reporting(E_ALL);

/**
 * This container initialize the qti HotspotChoice form:
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This container initialize the HotspotChoice form.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */
class taoItems_actions_QTIform_choice_AssociableHotspot
    extends taoItems_actions_QTIform_choice_AssociableChoice{
	
	public function __construct(taoItems_models_classes_QTI_Data $choice){
		parent::__construct($choice);
		
		// if(strtolower($this->interaction->getType()) == 'graphicgapmatch'){
			// $this->formName = 'GroupForm_'.$this->choice->getSerial();
		// }
		
	}
	
	public function initElements(){
		
		parent::setCommonElements();
		
		//add hotspot label:
		$labelElt = tao_helpers_form_FormFactory::getElement('hotspotLabel', 'Textbox');
		$labelElt->setDescription(__('Label'));
		$labelElt->setValue($this->choice->getOption('hotspotLabel'));
		$this->form->addElement($labelElt);
		
		$shapeElt = tao_helpers_form_FormFactory::getElement('shape', 'Combobox');
		$shapeElt->setDescription(__('Shape'));
		$shapeElt->setAttribute('class', 'qti-shape');
		$shapeElt->setOptions(array(
			'default' => __('default'),
			'circle' => __('circle'),
			'ellipse' => __('ellipse'),
			'rect' => __('rectangle'),
			'poly' => __('polygon')
		));
		$shapeElt->setValue($this->choice->getOption('shape'));
		$this->form->addElement($shapeElt);
		
		$coordsElt = tao_helpers_form_FormFactory::getElement('coords', 'Hidden');
		$coordsElt->setValue($this->choice->getOption('coords'));
		$this->form->addElement($coordsElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('hotspotLabel', 'fixed', 'matchGroup'));
	}
	
	public function getMatchGroupOptions(){
	
		$returnValue = array();
		
		foreach($this->interaction->getChoices() as $choice){
			$returnValue[$choice->getIdentifier()] = $choice->getIdentifier();
		}
				
		return $returnValue;
	}
}

?>
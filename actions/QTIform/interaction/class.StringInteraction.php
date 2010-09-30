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
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('tao/helpers/form/class.FormContainer.php');

/**
 * This container initialize the login form.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */
abstract class taoItems_actions_QTIform_interaction_StringInteraction
    extends taoItems_actions_QTIform_interaction_Interaction
{
	
	public function setCommonElements(){
	
		$interaction = $this->getInteraction();
		
		parent::setCommonElements();
		
		$baseElt = tao_helpers_form_FormFactory::getElement('base', 'TextBox');
		$baseElt->setDescription(__('Number base for value interpretation'));
		//validator: is int??
		$base = $interaction->getOption('base');
		if(!empty($base)){
			$baseElt->setValue($base);
		}
		$this->form->addElement($baseElt);
		
		$stringIdentifierElt = tao_helpers_form_FormFactory::getElement('stringIdentifier', 'TextBox');
		$stringIdentifierElt->setDescription(__('String identifier'));
		$stringIdentifier = $interaction->getOption('stringIdentifier');
		if(!empty($stringIdentifier)){
			$stringIdentifierElt->setValue($stringIdentifier);
		}
		$this->form->addElement($stringIdentifierElt);
		
		$expectedLengthElt = tao_helpers_form_FormFactory::getElement('expectedLength', 'TextBox');
		$expectedLengthElt->setDescription(__('Expected length'));
		$expectedLength = $interaction->getOption('expectedLength');
		if(!empty($expectedLength)){
			$expectedLengthElt->setValue($expectedLength);
		}
		$this->form->addElement($expectedLengthElt);
		
		$patternMaskElt = tao_helpers_form_FormFactory::getElement('patternMask', 'TextBox');
		$patternMaskElt->setDescription(__('Pattern mask'));
		//validator: is int??
		$patternMask = $interaction->getOption('patternMask');
		if(!empty($patternMask)){
			$patternMaskElt->setValue($patternMask);
		}
		$this->form->addElement($patternMaskElt);
		
		$placeHolderTextElt = tao_helpers_form_FormFactory::getElement('placeHolderText', 'TextBox');
		$placeHolderTextElt->setDescription(__('Place holder text'));
		//validator: is int??
		$placeHolderText = $interaction->getOption('placeHolderText');
		if(!empty($placeHolderText)){
			$placeHolderTextElt->setValue($placeHolderText);
		}
		$this->form->addElement($placeHolderTextElt);
		
	}
}

?>
<?php

error_reporting(E_ALL);

/**
 * This container initialize the qti item form:
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/**
 * This container initialize the login form.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */
class taoItems_actions_QTIform_ChoiceInteraction
    extends taoItems_actions_QTIform_BlockInteraction
{

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
		$interaction = $this->getInteraction();
		
		//TODO: group identical form elts in a parent form container class, e.g. block, graphic, etc.
		$this->setCommonElements();
				
		$shuffleElt = tao_helpers_form_FormFactory::getElement('shuffle', 'CheckBox');
		$shuffleElt->setDescription(__('Shuffle'));
		$shuffle = $interaction->getOption('shuffle');
		$shuffleElt->setOptions(array('true' => ''));
		if(!empty($shuffle)){
			if($shuffle === 'true' || $shuffle === true){
				$shuffleElt->setValue('true');
			}
		}
		$this->form->addElement($shuffleElt);
		
		//the "maxChoices" attr shall be set automatically?
		$maxChoiceElt = tao_helpers_form_FormFactory::getElement('maxChoices', 'TextBox');
		$maxChoiceElt->setDescription(__('Maximum Number of Choice'));
		//validator: is int??
		$maxChoices = $interaction->getOption('maxChoices');
		if(!empty($maxChoices)){
			$maxChoiceElt->setValue($maxChoices);
		}
		$this->form->addElement($maxChoiceElt);
		
		// $this->form->createGroup('interactionPropOptions', __('Advanced properties'), array('shuffle', 'maxChoices'));
    }
	
	public function setCommonElements(){
		parent::setCommonElements();
	}
}

?>
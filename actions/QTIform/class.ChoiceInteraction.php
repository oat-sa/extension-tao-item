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
    extends taoItems_actions_QTIform_Interaction
{

    //initForm() in the parent class...

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
		
		//the prompt field is the interaction's data for a block interaction, that's why the id is data and not 
		$promptElt = tao_helpers_form_FormFactory::getElement('data', 'Textarea');//should be a text... need to solve the conflict with the 
		$promptElt->setDescription(__('Prompt'));
		// $promptElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));//no validator required for prompt
		$interactionData = $interaction->getData();
		if(!empty($interactionData)){
			$promptElt->setValue($interactionData);
		}
		$this->form->addElement($promptElt);
		
		
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
		
		//the "maxAssociations" attr shall be set automatically?
		$maxAssocElt = tao_helpers_form_FormFactory::getElement('maxAssociations', 'TextBox');
		$maxAssocElt->setDescription(__('Maximum Number of Choice'));
		//validator: is int??
		$maxAssociations = $interaction->getOption('maxAssociations');
		if(!empty($maxAssociations)){
			$maxAssocElt->setValue($maxAssociations);
		}
		$this->form->addElement($maxAssocElt);
		
		// $this->form->createGroup('interactionPropOptions', __('Advanced properties'), array('shuffle', 'maxAssociations'));
    }
	
	public function setCommonElements(){
		parent::setCommonElements();
	}
}

?>
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
abstract class taoItems_actions_QTIform_BlockInteraction
    extends taoItems_actions_QTIform_Interaction
{
	
	public function setCommonElements(){
		parent::setCommonElements();
		
		//the prompt field is the interaction's data for a block interaction, that's why the id is data and not 
		$promptElt = tao_helpers_form_FormFactory::getElement('prompt', 'Textarea');//should be a text... need to solve the conflict with the 
		$promptElt->setDescription(__('Prompt'));
		// $promptElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));//no validator required for prompt
		$interactionData = $this->interaction->getPrompt();
		if(!empty($interactionData)){
			$promptElt->setValue($interactionData);
		}
		$this->form->addElement($promptElt);
	}
}

?>
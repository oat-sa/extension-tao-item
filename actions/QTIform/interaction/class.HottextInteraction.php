<?php

error_reporting(E_ALL);

/**
 * This container initialize the qti interaction form:
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
class taoItems_actions_QTIform_interaction_HottextInteraction
    extends taoItems_actions_QTIform_interaction_BlockInteraction
{

    /**
     * Short description of method initElements
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */
    public function initElements()
    {
		$interaction = $this->getInteraction();
		
		$this->setCommonElements();
				
		$maxChoiceElt = tao_helpers_form_FormFactory::getElement('maxChoices', 'TextBox');
		$maxChoiceElt->setDescription(__('Maximum Number of Choice'));
		//validator: is int??
		$maxChoices = $interaction->getOption('maxChoices');
		if(!empty($maxChoices)){
			$maxChoiceElt->setValue($maxChoices);
		}
		$this->form->addElement($maxChoiceElt);
		
    }
	
	public function setCommonElements(){
		parent::setCommonElements();
	}
}

?>
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
class taoItems_actions_QTIform_interaction_ChoiceInteraction
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
		
		//shuffle element:		
		$this->form->addElement(taoItems_actions_QTIform_AssessmentItem::createBooleanElement($interaction, 'shuffle', __('Shuffle choices')));
		
		//the "maxChoices" attr shall be set automatically?
		$this->form->addElement(taoItems_actions_QTIform_AssessmentItem::createTextboxElement($interaction, 'maxChoices', __('Maximum number of choices')));
    }
	
	public function setCommonElements(){
		parent::setCommonElements();
	}
}

?>
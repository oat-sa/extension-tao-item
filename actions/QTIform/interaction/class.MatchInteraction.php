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
class taoItems_actions_QTIform_interaction_MatchInteraction
    extends taoItems_actions_QTIform_interaction_BlockInteraction
{

	public function initForm()
    {
		$interactionType = $this->interaction->getType();
		$this->form = tao_helpers_form_FormFactory::getForm('InteractionForm_'.strtolower($interactionType).'Interaction');
		
		//custom actions only:
		$actions = array();
		
		$saveElt = tao_helpers_form_FormFactory::getElement('save', 'Free');
		$saveElt->setValue("<a href='#' class='interaction-form-submitter' ><img src='".TAOBASE_WWW."/img/save.png'  /> ".__('Apply')."</a>");
		$actions[] = $saveElt;
				
		$this->form->setActions($actions, 'top');
		$this->form->setActions(array(), 'bottom');
		
    }
	
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
		
		//TODO: group identical form elts in a parent form container class, e.g. block, graphic, etc.
		$this->setCommonElements();
				
		$this->form->addElement(taoItems_actions_QTIform_AssessmentItem::createBooleanElement($interaction, 'shuffle', __('Shuffle choices')));
		
		$this->form->addElement(taoItems_actions_QTIform_AssessmentItem::createTextboxElement($interaction, 'maxAssociations', __('Maximum number of associations')));
    }
	
	public function setCommonElements(){
		parent::setCommonElements();
	}
}

?>
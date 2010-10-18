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
class taoItems_actions_QTIform_AssessmentItem
    extends tao_helpers_form_FormContainer
{
	
	/**
     * the class resource to create the form from
     *
     * @access protected
     * @var Item
     */
    protected $item = null;
	
	public function __construct(taoItems_models_classes_QTI_Item $item){
		
		$this->item = $item;
		$returnValue = parent::__construct(array(), array());
		
	}
	
	/**
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */
    public function initForm(){
	
		$this->form = tao_helpers_form_FormFactory::getForm('AssessmentItem_Form');
		
		$actions = array();
		
		// $saveElt = tao_helpers_form_FormFactory::getElement('save', 'Free');
		// $saveElt->setValue("<a href='#' class='item-form-submitter' ><img src='".TAOBASE_WWW."/img/save.png'  /> ".__('Apply')."</a>");
		// $actions[] = $saveElt;
		
		$this->form->setActions($actions, 'top');
		$this->form->setActions(array(), 'bottom');
		
    }
	
	public function getItem(){
		return $this->item;
	}
	
	public function initElements(){
		//serial
		$serialElt = tao_helpers_form_FormFactory::getElement('itemSerial', 'Hidden');
		$serialElt->setValue($this->item->getSerial());
		$this->form->addElement($serialElt);
		
		//identifier (editable unique name)
		// $idElt = tao_helpers_form_FormFactory::getElement('identifier', 'Textbox');
		// $idElt->setDescription(__('Identifier'));
		// $idElt->setValue($this->item->getIdentifier());
		// $this->form->addElement($idElt);
		
		//title:
		$titleElt = tao_helpers_form_FormFactory::getElement('title', 'Textbox');
		$titleElt->setDescription(__('Title'));
		$titleElt->setValue($this->item->getOption('title'));
		$this->form->addElement($titleElt);
		
		//label:
		$labelElt = tao_helpers_form_FormFactory::getElement('label', 'Textbox');
		$labelElt->setDescription(__('Label'));
		$labelElt->setValue($this->item->getOption('label'));
		$this->form->addElement($labelElt);
		
		$this->form->addElement(self::createBooleanElement($this->item, 'timeDependent', 'Time dependent', array('no', 'yes')));		
		$this->form->addElement(self::createBooleanElement($this->item, 'adaptive', '', array('no', 'yes')));
		
		// $this->form->createGroup('interactionPropOptions', __('Advanced properties'), array('shuffle', 'maxChoices'));
    }
	
	public static function createBooleanElement(taoItems_models_classes_QTI_Data $qtiObject, $optionName, $elementLabel = '', $boolean = array('false', 'true')){
		
		if(count($boolean) != 2){
			throw new Exception('invalid number of elements in boolean array definition');
		}
		$boolElt = tao_helpers_form_FormFactory::getElement($optionName, 'Radiobox');
		
		if(empty($elementLabel)) $elementLabel = __(ucfirst(strtolower($optionName)));
		$boolElt->setDescription($elementLabel);
		$boolElt->setOptions(array(0 => $boolean[0], 1=>$boolean[1]));
		
		$optionValue = $qtiObject->getOption($optionName);
		
		$optionSet = false;
		if(!empty($optionValue)){
			if($optionValue === 'true' || $optionValue === true){
				$optionSet = true;
			}
		}
		if($optionSet) $boolElt->setValue(1);
		
		return $boolElt;
	}
}

?>
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
class taoItems_actions_QTIform_choice_Gap
    extends taoItems_actions_QTIform_choice_Choice{
	
	/**
     * the class resource to create the form from
     *
     * @access protected
     * @var group
     */
    protected $group = null;
	
	/**
     * the class resource to create the form from
     *
     * @access protected
     * @var interaction
     */
    protected $interaction = null;
	
	//the group is a choice here!!
	public function __construct(taoItems_models_classes_QTI_Group $group, taoItems_models_classes_QTI_Interaction $interaction){
		
		$this->group = $group;
		$this->formName = 'ChoiceForm_'.$this->group->getSerial();
		$this->interaction = $interaction;
		// try{
			$returnValue = parent::__construct(null);
		// }catch(Exception $e){
			// echo $e;
		// }
	}
	
	public function initElements(){
		
		// parent::setCommonElements();//not applicable here!
		
		//reimplement the matchGroup element with the group:
		//add hidden id element, to know what the old id is:
		$oldIdElt = tao_helpers_form_FormFactory::getElement('groupSerial', 'Hidden');
		$oldIdElt->setValue($this->group->getSerial());
		$this->form->addElement($oldIdElt);
		
		//id element: need for checking unicity
		$labelElt = tao_helpers_form_FormFactory::getElement('groupIdentifier', 'TextBox');
		$labelElt->setDescription(__('Identifier'));
		$labelElt->setValue($this->group->getIdentifier());
		$this->form->addElement($labelElt);
		
		//the fixed attribute element
		$fixedElt = tao_helpers_form_FormFactory::getElement('fixed', 'CheckBox');
		$fixedElt->setDescription(__('Fixed'));
		$fixedElt->setOptions(array('true' => ''));//empty label because the description of the element is enough
		$fixed = $this->group->getOption('fixed');
		if(!empty($fixed)){
			if($fixed === 'true' || $fixed === true){
				$fixedElt->setValue('true');
			}
		}
		$this->form->addElement($fixedElt);
		
		//associable choice special property:
		$matchGroupElt = tao_helpers_form_FormFactory::getElement('matchGroup', 'CheckBox');
		$matchGroupElt->setDescription(__('match group'));
		$options = array();
		foreach($this->interaction->getChoices() as $choice){
			$options[$choice->getSerial()] = $choice->getIdentifier();
		}
		$matchGroupElt->setOptions($options);
		
		foreach($this->group->getChoices() as $choice){
			$matchGroupElt->setValue($choice->getSerial());
		}//the default empty value indicates to the authoring controller that there is no restriction to the associated choices
				
		$this->form->addElement($matchGroupElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->group->getSerial(), __('Advanced properties'), array('fixed', 'matchGroup'));
	}

}

?>
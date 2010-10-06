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
    extends tao_helpers_form_FormContainer{
	
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
	
	protected $formName = 'ChoiceForm_';
	
	//the group is a choice here!!
	public function __construct(taoItems_models_classes_QTI_Group $group){
		
		$this->group = $group;
		$this->formName = 'ChoiceForm_'.$this->group->getSerial();//GroupForm...however it is considered as a choice
		
		$qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
		$interaction = $qtiService->getComposingData($group);
		if($interaction instanceof taoItems_models_classes_QTI_Interaction){
			$this->interaction = $interaction;
		}else{
			var_dump($group, $interaction);
			throw new Exception('cannot find the parent interaction');
		}
		
		$returnValue = parent::__construct(array(), array());
	}
	
	/**
     * The method initForm for all types of choice form
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */
    public function initForm()
    {
		$this->form = tao_helpers_form_FormFactory::getForm($this->formName);
		$this->form->setActions(array(), 'bottom');
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
		
		foreach($this->group->getChoices() as $choiceSerial){
			$matchGroupElt->setValue($choiceSerial);
		}//the default empty value indicates to the authoring controller that there is no restriction to the associated choices
				
		$this->form->addElement($matchGroupElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->group->getSerial(), __('Advanced properties'), array('fixed', 'matchGroup'));
	}

}

?>
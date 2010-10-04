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
		$this->interaction = $interaction;
		$returnValue = parent::__construct($group->getChoices());
		
	}
	
	public function initElements(){
		
		parent::setCommonElements();
		
		//reimplement thr matchGroup element with the group
		$matchGroupElt = tao_helpers_form_FormFactory::getElement('matchGroup', 'CheckBox');
		$matchGroupElt->setDescription(__('match group'));
		$options = array();
		foreach($this->interaction->getChoices() as $choice){
			$options[$choice->getSerial()] = $choice->getIdentifier();
		}
		$matchGroupElt->setOptions($options);
		
		for($this->group->getChoices() as $choice){
			$matchGroupElt->setValue($choice->getSerial());
		}//the default empty value indicates to the authoring controller that there is no restriction to the associated choices
				
		$this->form->addElement($matchGroupElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('fixed', 'matchGroup'));
	}

}

?>
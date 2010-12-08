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
abstract class taoItems_actions_QTIform_choice_AssociableChoice
    extends taoItems_actions_QTIform_choice_Choice{
	
	/**
     * the class resource to create the form from
     *
     * @access protected
     * @var interaction
     */
    protected $interaction = null;
	
	public function __construct(taoItems_models_classes_QTI_Data $choice){
		$qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
		$interaction = $qtiService->getComposingData($choice);
		if($interaction instanceof taoItems_models_classes_QTI_Interaction){
			$this->interaction = $interaction;
		}else{
			throw new Exception('cannot find the parent interaction of the current choice');
		}
		
		$returnValue = parent::__construct($choice);
		
	}
	
	public function setCommonElements(){
		
		parent::setCommonElements();
		
		$matchGroupElt = tao_helpers_form_FormFactory::getElement('matchGroup', 'Checkbox');
		$matchGroupElt->setDescription(__('Match Group'));
		$matchGroupOption = $this->getMatchGroupOptions();
		if(!empty($matchGroupOption)){
			$matchGroupElt->setOptions($matchGroupOption);
			
			if($this->choice instanceof taoItems_models_classes_QTI_Choice){
				$matchGroups = $this->choice->getOption('matchGroup');
				if(!empty($matchGroups)){
					if(is_array($matchGroups)){
						foreach($matchGroups as $choiceIdentifierOrSerial){
							$matchGroupElt->setValue($choiceIdentifierOrSerial);
						}
					}else{
						$matchGroupElt->setValue((string)$matchGroups);
					}
				}
			}else if($this->choice instanceof taoItems_models_classes_QTI_Group){
				foreach($this->choice->getChoices() as $choiceSerial){
					$choice = taoItems_models_classes_QTI_Service::getDataBySerial($choiceSerial, 'taoItems_models_classes_QTI_Choice');
					$matchGroupElt->setValue($choice->getIdentifier());
				}
			}
			
			$this->form->addElement($matchGroupElt);
		}
		
		
	}
	
	protected abstract function getMatchGroupOptions();

}

?>
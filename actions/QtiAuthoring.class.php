<?php
/**
 * QTiAuthoring Controller provide actions to edit a QTI item
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class QTiAuthoring extends CommonModule {
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){
		
		parent::__construct();
		
		$this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
		$this->service = tao_models_classes_ServiceFactory::get('taoItems_models_classes_QtiAuthoringService');
		$this->defaultData();
		
		taoItems_models_classes_QTI_Data::setPersistance(true);
	}
	
	/*
	* get the current item object either from the file or from session or create a new one
	*/
	public function getCurrentItem(){
		
		$item = null;
		
		$itemUri = '';
		$itemId = '';
		if($this->hasRequestParameter('instance')){
			$itemUri = $this->getRequestParameter('instance');
			$itemId = 'qti_item_'.tao_helpers_Uri::getUniqueId(tao_helpers_Uri::decode($this->getRequestParameter('instance')));
		}elseif($this->hasRequestParameter('itemId')){
			$itemId = tao_helpers_Uri::decode($this->getRequestParameter('itemId'));
		}else{
			throw new Exception('no current id for the item');
		}
		
		$itemFile = $this->getRequestParameter('xml');
		if(empty($itemFile)){
			
			//debug
			// var_dump(unserialize(Session::getAttribute($itemId)));
			
			//get item from serialized object in session:
			$item = $this->qtiService->getItemById($itemId);
			
			if(is_null($item)){
				//create a new qti xml file:
				$item = $this->service->createNewItem($itemUri);//TODO: change variable type
				if(empty($item)){
					throw new Exception('a new qti item xml cannot be created');
				}
			}
		}else{
			//if there is a file in the parameter, overwrite the current item completely
			//get the item from xml file:
			$qtiParser = new taoItems_models_classes_QTI_Parser($itemFile);
			$item = $qtiParser->load();
			if(empty($item)){
				throw new Exception('cannot load the item from the file: '.$itemFile);
			}
		}
		
		if(is_null($item)){
			throw new Exception('there is no item');
		}
		
		return $item;
	}

	public function index(){
	
		// $itemData = $this->getCurrentItem()->getData();
		$currentItem = $this->getCurrentItem();
		// var_dump($currentItem);
		$itemData = $this->service->getItemData($currentItem);
		
		// $this->setData('htmlbox_wysiwyg_path', BASE_WWW.'js/HtmlBox_4.0/');//script that is not working
		$this->setData('itemId', $currentItem->getId());
		$this->setData('itemData', $itemData);
		$this->setData('jwysiwyg_path', BASE_WWW.'js/jwysiwyg/');
		$this->setData('simplemodal_path', BASE_WWW.'js/simplemodal/');
		$this->setData('qtiAuthoring_path', BASE_WWW.'js/qtiAuthoring/');
		$this->setView("QTIAuthoring/authoring.tpl");
	}
	
	public function saveItemData(){
		$saved = false;
		
		$itemData = $this->getRequestParameter('itemData');
		if(!empty($itemData)){
			//save to qti:
			$this->service->saveItemData($this->getCurrentItem(), $itemData);
			$saved = true;
		}
		
		echo json_encode(array(
			'saved'=>$saved
		));
	}
	
	public function reviewItem(){
		//compile the item then display the result...
	}
	
	public function addInteraction(){
		$added = false;
		$interactionId = '';
		
		$interactionType = $this->getRequestParameter('interactionType');
		$itemData = urldecode($this->getRequestParameter('itemData'));
		// echo "<pre>$itemData</pre>";
		
		$item = $this->getCurrentItem();
		if(!empty($interactionType)){
			$interaction = $this->service->addInteraction($item, $interactionType);
			
			if(!is_null($interaction)){
				//save the itemData, i.e. the location at which the new interaction shall be inserted
				//the location has been marked with {qti_interaction_new}
				$itemData = preg_replace("/{qti_interaction_new}/", "{{$interaction->getId()}}", $itemData, 1);
				$this->service->saveItemData($item, $itemData);
				// var_dump('item', $item);
				$itemData = $this->service->getItemData($item);//do not convert to html entities...
				
				//everything ok:
				$added = true;
				$interactionId = $interaction->getId();
			}
		}
		
		echo json_encode(array(
			'added' => $added,
			'interactionId' => $interactionId,
			'itemData' => html_entity_decode($itemData)
		));
	}
	
	public function addChoice(){
		$added = false;
		$choiceId = '';
		$choiceForm = '';
		
		$interaction = $this->getCurrentInteraction();
		if(!is_null($interaction)){
			$choice = $this->service->addChoice($interaction);
			
			//return id and form:
			
			$choiceId = $choice->getId();
			$choiceForm = $choice->toForm()->render();
			$added = true;
		}
		
		echo json_encode(array(
			'added' => $added,
			'choiceId' => $choiceId,
			'choiceForm' => $choiceForm
		));
	}
	
	
	public function deleteInteractions(){
		// var_dump($this->getCurrentItem(), $this->getRequestParameter('interactionIds'));
		
		$deleted = false;
		
		$interactionIds = array();
		if($this->hasRequestParameter('interactionIds')){
			$interactionIds = $this->getRequestParameter('interactionIds');
		}
		if(empty($interactionIds)){
			throw new Exception('no interaction ids found to be deleted');
		}else{
			$item = $this->getCurrentItem();
			$deleteCount = 0;
			
			//delete interactions:
			foreach($interactionIds as $interactionId){
				$interaction = $this->qtiService->getInteractionById($interactionId);
				if(!empty($interaction)){
					$this->service->deleteInteraction($item, $interaction);
					$deleteCount++;
				}else{
					throw new Exception('no interaction found to be deleted with the id: '.$interactionId);
				}
			}
			
			if($deleteCount == count($interactionIds)){
				$deleted = true;
			}
		}
		
		echo json_encode(array(
			'deleted' => $deleted
		));
		
	}
	
	public function deleteChoice(){
		$interaction = $this->getCurrentInteraction();
		$choice = $this->getCurrentChoice();
		if(!is_null($interaction) && !is_null($choice)){
			$this->service->deleteChoice($interaction, $choice);
		}
		
		echo json_encode(array(
			'deleted' => true
		));
	}
	
	
	//to be used to dynamically update the main itemData editor frame:
	public function getInteractionTag(){
		$interaction = $this->getCurrentInteraction();
		echo $this->service->getInteractionTag($interaction);
	}
	
	
	public function getCurrentInteraction(){
		$returnValue = null;
		if($this->hasRequestParameter('interactionId')){
			$interaction = $this->qtiService->getInteractionById($this->getRequestParameter('interactionId'));
			if(!empty($interaction)){
				$returnValue = $interaction;
			}
		}else{
			throw new Exception('no request parameter "interactionId" found');
		}
		
		return $returnValue;
	}
	
	public function getCurrentChoice(){
		$returnValue = null;
		if($this->hasRequestParameter('choiceId')){
			$choice = $this->qtiService->getDataById($this->getRequestParameter('choiceId'), 'taoItems_models_classes_QTI_Choice');
			if(!empty($choice)){
				$returnValue = $choice;
			}
		}else{
			throw new Exception('no request parameter "choiceId" found');
		}
		
		return $returnValue;
	}
	
	//to be called at the same time as edit response
	public function editInteraction(){
		$interaction = $this->getCurrentInteraction();
		
		//build the form with its method "toForm"
		$myForm = $interaction->toForm();
		
		
		//build the choices, no matter the way they shall be displayed (e.g. one/two column(s)), the template shall manage that
		$choices = array();
		foreach($interaction->getChoices() as $choice){
			// $choices[] = $choice->toForm();//first, the simple version: choice are editable immediately. 
			$choices[$choice->getId()] = $choice->toForm()->render();
		}
		
		//display the template, according to the type of interaction
		$templateName = 'QTIAuthoring/form_interaction_'.strtolower($interaction->getType()).'.tpl';
		// $this->setData('formId', $formName);
		$this->setData('interactionId', $interaction->getId());
		$this->setData('formInteraction', $myForm->render());
		$this->setData('formChoices', $choices);
		$this->setView($templateName);
	}
	
	
	public function saveInteraction(){
		$interaction = $this->getCurrentInteraction();
		$myForm = $interaction->toForm();
		$saved = false;
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				// var_dump($myForm->getValues());
				$values = $myForm->getValues();
				
				if($values['interactionId'] != $values['newId']){
					// check unicity of the new id $values['newId']:
					$unique = true;
					if($unique){
						// save id
						$this->service->setInteractionId($interaction, $values['newId']);
					}
				}
				
				if(isset($values['data'])){
					$this->service->setData($interaction, $values['data']);
					unset($values['data']);
				}
				
				unset($values['interactionId']);
				unset($values['newId']);
				
				$this->service->setOptions($interaction, $values);
				
				$saved  = true;
				// $group = $this->service->bindProperties($group, $myForm->getValues());
				
				// $this->setData('message', __('Interaction saved'));
				// $this->setData('reload', true);
			}
		}
		
		echo json_encode(array(
			'saved' => $saved
		));
		
	}
	
	public function saveChoice(){
		$choice = $this->getCurrentChoice();
		$myForm = $choice->toForm();
		$saved = false;
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				// var_dump($myForm->getValues());
				$values = $myForm->getValues();
				
				if($values['choiceId'] != $values['newId']){
					// check unicity of the new id $values['newId']:
					$unique = true;
					if($unique){
						// save id
						$this->service->setInteractionId($choice, $values['newId']);
					}
				}
				
				if(isset($values['data'])){
					$this->service->setData($choice, $values['data']);
					unset($values['data']);
				}
				
				unset($values['choiceId']);
				unset($values['newId']);
				$this->service->setOptions($choice, $values);
				
				$saved  = true;
			}
		}
		
		echo json_encode(array(
			'saved' => $saved,
			'choiceId' => $choice->getId()
		));
	}
	
	
	public function editChoice(){
		$choice = $this->getCurrentChoice();
	}
}
?>
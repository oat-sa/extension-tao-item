<?php
/**
 * QtiAuthoring Controller provide actions to edit a QTI item
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class QtiAuthoring extends CommonModule {
	
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
		$itemSerial = '';
		$itemIdentifier = tao_helpers_Uri::getUniqueId($itemUri);//TODO: remove coopling to TAO
		
		//when actions are executed in the authroing tool, retrieve the item with the serial:
		if($this->hasRequestParameter('itemSerial')){
			$itemSerial = tao_helpers_Uri::decode($this->getRequestParameter('itemSerial'));
			$item = $this->qtiService->getItemBySerial($itemSerial);
		}else{
			//try creating a new item:
			$itemFile = html_entity_decode($this->getRequestParameter('xml'));
			if(empty($itemFile)){
			
				//temp check to allow page reloading without xml file:
				$itemUri = $this->getRequestParameter('instance');
				if(!empty($itemUri)){
					if(isset($_SESSION['tao_qti_item_uris'][tao_helpers_Uri::getUniqueId($itemUri)])){
						$item = $this->qtiService->getItemBySerial($_SESSION['tao_qti_item_uris'][tao_helpers_Uri::getUniqueId($itemUri)]);
					}
				}
				
				if(empty($item)){
					//create a new item object:
					$item = $this->service->createNewItem($itemIdentifier);
					$_SESSION['tao_qti_item_uris'][tao_helpers_Uri::getUniqueId($itemUri)] = $item->getSerial();
				}
				
				if(empty($item)){
					throw new Exception('a new qti item xml cannot be created');
				}
			}else{
				//import it:
				// $qtiParser = new taoItems_models_classes_QTI_Parser($itemFile);
				// $qtiParser->validate();
				// if(!$qtiParser->isValid()){
					// var_dump($itemFile);
					// echo $qtiParser->displayErrors();
					// return null;
				// }
				// $item = $qtiParser->load();
				// if(empty($item)){
					// throw new Exception('cannot load the item from the file: '.$itemFile);
				// }
			}
		}
		
		
		/*
		if($this->hasRequestParameter('instance')){
			$itemUri = $this->getRequestParameter('instance');
			$itemSerial = 'qti_item_'.tao_helpers_Uri::getUniqueId(tao_helpers_Uri::decode($this->getRequestParameter('instance')));
		}elseif($this->hasRequestParameter('itemSerial')){
			$itemSerial = tao_helpers_Uri::decode($this->getRequestParameter('itemSerial'));
		}else{
			throw new Exception('no current id for the item');
		}
		
		$itemFile = $this->getRequestParameter('xml');
		if(empty($itemFile)){
			
			//debug
			// var_dump(unserialize(Session::getAttribute($itemSerial)));
			
			//get item from serialized object in session:
			$item = $this->qtiService->getItemBySerial($itemSerial);
			
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
		*/
		
		
		if(is_null($item)){
			throw new Exception('there is no item');
		}
		
		return $item;
	}

	public function index(){
	
		$currentItem = $this->getCurrentItem();
		var_dump($currentItem);
		$itemData = $this->service->getItemData($currentItem);
		
		// $this->setData('htmlbox_wysiwyg_path', BASE_WWW.'js/HtmlBox_4.0/');//script that is not working
		$this->setData('itemSerial', $currentItem->getSerial());
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
	
	public function saveInteractionData(){
		$saved = false;
		
		$interactionData = $this->getRequestParameter('interactionData');
		if(!empty($interactionData)){
			$this->service->setInteractionData($this->getCurrentInteraction(), $interactionData);
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
		$interactionSerial = '';
		
		$interactionType = $this->getRequestParameter('interactionType');
		$itemData = urldecode($this->getRequestParameter('itemData'));
		// echo "<pre>$itemData</pre>";
		
		$item = $this->getCurrentItem();
		if(!empty($interactionType)){
			$interaction = $this->service->addInteraction($item, $interactionType);
			
			if(!is_null($interaction)){
				//save the itemData, i.e. the location at which the new interaction shall be inserted
				//the location has been marked with {qti_interaction_new}
				$itemData = preg_replace("/{qti_interaction_new}/", "{{$interaction->getSerial()}}", $itemData, 1);
				$this->service->saveItemData($item, $itemData);
				$itemData = $this->service->getItemData($item);//do not convert to html entities...
				
				//everything ok:
				$added = true;
				$interactionSerial = $interaction->getSerial();
			}
		}
		
		echo json_encode(array(
			'added' => $added,
			'interactionSerial' => $interactionSerial,
			'itemData' => html_entity_decode($itemData)
		));
	}
	
	public function addHotText(){
		$added = false;
		$choiceSerial = '';//the hot text basically is a "choice"
		$textContent = '';
		
		$interactionData = urldecode($this->getRequestParameter('interactionData'));
		// echo "<pre>$interactionData</pre>";
		
		$interaction = $this->getCurrentInteraction();
		
		$choice = $this->service->addChoice($interaction, '', null, null, $interactionData);
		
		if(!is_null($choice)){
			$interactionData = $this->service->getInteractionData($interaction);//do not convert to html entities...
			
			//everything ok:
			$added = true;
			$choiceSerial = $choice->getSerial();
		}
		
		
		echo json_encode(array(
			'added' => $added,
			'choiceSerial' => $choiceSerial,
			'choiceForm' => $choice->toForm()->render(),
			'interactionData' => html_entity_decode($interactionData)
		));
	}
	
	public function addChoice(){
		$added = false;
		$choiceSerial = '';
		$choiceForm = '';
		$groupSerial = '';
		
		$interaction = $this->getCurrentInteraction();
		if(!is_null($interaction)){
			try{
				//not null in case of a match or gapmatch interaction:
				$group = null;
				$group = $this->getCurrentGroup();
			}catch(Exception $e){}
		
			$choice = $this->service->addChoice($interaction, '', null, $group);
			
			//return id and form:
			if(!is_null($group)) $groupSerial = $group->getSerial();
			$choiceSerial = $choice->getSerial();
			$choiceForm = $choice->toForm()->render();
			$added = true;
		}
		
		echo json_encode(array(
			'added' => $added,
			'choiceSerial' => $choiceSerial,
			'choiceForm' => $choiceForm,
			'groupSerial' => $groupSerial
		));
	}
	
	
	public function deleteInteractions(){
		// var_dump($this->getCurrentItem(), $this->getRequestParameter('interactionSerials'));
		
		$deleted = false;
		
		$interactionSerials = array();
		if($this->hasRequestParameter('interactionSerials')){
			$interactionSerials = $this->getRequestParameter('interactionSerials');
		}
		if(empty($interactionSerials)){
			throw new Exception('no interaction ids found to be deleted');
		}else{
			$item = $this->getCurrentItem();
			$deleteCount = 0;
			
			//delete interactions:
			foreach($interactionSerials as $interactionSerial){
				$interaction = $this->qtiService->getInteractionBySerial($interactionSerial);
				if(!empty($interaction)){
					$this->service->deleteInteraction($item, $interaction);
					$deleteCount++;
				}else{
					throw new Exception('no interaction found to be deleted with the serial: '.$interactionSerial);
				}
			}
			
			if($deleteCount == count($interactionSerials)){
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
		if($this->hasRequestParameter('interactionSerial')){
			$interaction = $this->qtiService->getInteractionBySerial($this->getRequestParameter('interactionSerial'));
			if(!empty($interaction)){
				$returnValue = $interaction;
			}
		}else{
			throw new Exception('no request parameter "interactionSerial" found');
		}
		
		return $returnValue;
	}
	
	public function getCurrentChoice(){
		$returnValue = null;
		if($this->hasRequestParameter('choiceSerial')){
			$choice = $this->qtiService->getDataBySerial($this->getRequestParameter('choiceSerial'), 'taoItems_models_classes_QTI_Choice');
			if(!empty($choice)){
				$returnValue = $choice;
			}
		}else{
			throw new Exception('no request parameter "choiceSerial" found');
		}
		
		return $returnValue;
	}
	
	public function getCurrentGroup(){
		$returnValue = null;
		if($this->hasRequestParameter('groupSerial')){
			$group = $this->qtiService->getDataBySerial($this->getRequestParameter('groupSerial'), 'taoItems_models_classes_QTI_Group');
			if(!empty($group)){
				$returnValue = $group;
			}
		}else{
			throw new Exception('no request parameter "groupSerial" found');
		}
		
		return $returnValue;
	}
	
	public function getCurrentResponse(){
		$returnValue = null;
		if($this->hasRequestParameter('responseSerial')){
			$response = $this->qtiService->getDataBySerial($this->getRequestParameter('responseSerial'), 'taoItems_models_classes_QTI_Response');
			if(!empty($response)){
				$returnValue = $response;
			}
		}else{
			try{
				//second change: try getting the response from the interaction, is set in the request parameter
				$interaction = $this->getCurrentInteraction();
				if(!empty($interaction)){
					$response = $this->service->getInteractionResponse($interaction);
					if(!empty($response)){
						$returnValue = $response;
					}
				}
			}catch(Exception $e){
				throw new Exception('cannot find the response no request parameter "responseSerial" found');
			}
			
		}
		
		return $returnValue;
	}
	
	//to be called at the same time as edit response
	public function editInteraction(){
		
		$interaction = $this->getCurrentInteraction();
		
		//build the form with its method "toForm"
		$myForm = $interaction->toForm();
		
		//get the itnteraction's choices
		$choices = $this->service->getInteractionChoices($interaction);
		$choiceForms = array();
		
		$interactionType = strtolower($interaction->getType());
		if($interactionType == 'match' || $interactionType == 'gapmatch'){
			$i = 0;
			$groupSerials = array();
			foreach($choices as $groupSerial=>$group){
				
				$groupSerials[$i] = $groupSerial;
				$choiceForms[$groupSerial] = array();
				foreach($group as $choice){
					$choiceForms[$groupSerial][$choice->getSerial()] = $choice->toForm()->render();
				}
				$i++;
			}
			$this->setData('groupSerials', $groupSerials);
		}else{
			foreach($choices as $order=>$choice){
				$choiceForms[$choice->getSerial()] = $choice->toForm()->render();
			}
		}
		
		//display the template, according to the type of interaction
		$templateName = 'QTIAuthoring/form_interaction_'.strtolower($interaction->getType()).'.tpl';
		$this->setData('interactionSerial', $interaction->getSerial());
		$this->setData('formInteraction', $myForm->render());
		$this->setData('formChoices', $choiceForms);
		$this->setData('interactionData', $this->service->getInteractionData($interaction));
		$this->setData('orderedChoices', $choices);
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
				
				if(isset($values['interactionIdentifier'])){
					// die('set identifier');
					if($values['interactionIdentifier'] != $interaction->getIdentifier()){
						$this->service->setIdentifier($interaction, $values['interactionIdentifier']);
					}
					unset($values['interactionIdentifier']);
				}
				
				if(isset($values['prompt'])){
					$interaction->setPrompt($values['prompt']);
					unset($values['prompt']);
				}
				
				$data = '';
				if($this->hasRequestParameter('data')){
					$data = $this->getRequestParameter('data');
				}
				
				unset($values['interactionSerial']);
				$this->service->setOptions($interaction, $values);
				
				$choiceOrder = array();
				if(isset($_POST['choiceOrder'])){
				
					$choiceOrder = $_POST['choiceOrder'];
					
				}elseif( isset($_POST['choiceOrder0']) ){//for match and gapmatch interaction
					
					$groupOrder0 = $_POST['choiceOrder0'];
					if(isset($groupOrder0['groupSerial'])){
						$groupSerial = $groupOrder0['groupSerial'];
						unset($groupOrder0['groupSerial']);
						$choiceOrder[$groupSerial] = $groupOrder0;
					}
					
					if(isset($_POST['choiceOrder1'])){//for match interaction only
						$groupOrder1 = $_POST['choiceOrder1'];
						if(isset($groupOrder1['groupSerial'])){
							$groupSerial = $groupOrder1['groupSerial'];
							unset($groupOrder1['groupSerial']);
							$choiceOrder[$groupSerial] = $groupOrder1;
						}
					}
					
				}
				$this->service->setInteractionData($interaction, $data, $choiceOrder);
				
				$saved  = true;
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
			
				$values = $myForm->getValues();
								
				if(isset($values['choiceIdentifier'])){
					if($values['choiceIdentifier'] != $choice->getIdentifier()){
						$this->service->setIdentifier($choice, $values['choiceIdentifier']);
					}
					unset($values['choiceIdentifier']);
				}
				
				if(isset($values['data'])){
					$this->service->setData($choice, $values['data']);
					unset($values['data']);
				}
				
				unset($values['choiceSerial']);
				$this->service->setOptions($choice, $values);
				
				$saved = true;
			}
		}
		
		echo json_encode(array(
			'saved' => $saved,
			'choiceSerial' => $choice->getSerial()
		));
	}
	
	
	public function editChoice(){
		$choice = $this->getCurrentChoice();
	}
	
	public function editResponseProcessing(){
	
		$item = $this->getCurrentItem();
		
		$formContainer = new taoItems_actions_QTIform_ResponseProcessing($item);
		$myForm = $formContainer->getForm();
		
		// $this->setData('interactionSerial', $interaction->getSerial());
		$this->setData('form', $myForm->render());
		$processingType = $formContainer->getProcessingType();
		$responseMappingMode = false;
		if($processingType == QTI_RESPONSE_TEMPLATE_MAP_RESPONSE || $processingType == QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT){
			$responseMappingMode = true;
		}
		$this->setData('responseMappingMode', $responseMappingMode);
		$this->setView('QTIAuthoring/form_response_processing.tpl');
	}
	
	public function saveResponseProcessing(){
		
		$item = $this->getCurrentItem();
		$responseProcessingType = tao_helpers_Uri::decode($this->getRequestParameter('responseProcessingType'));
		$customRule = $this->getRequestParameter('customRule');
		
		$saved = $this->service->setResponseProcessing($item, $responseProcessingType, $customRule);
		
		echo json_encode(array(
			'saved' => $saved
		));
	}
	
	
	public function editMappingOptions(){
		$response = $this->getCurrentResponse();
		
		$formContainer = new taoItems_actions_QTIform_Mapping($response);
		// var_dump($formContainer->getForm()->render());
		$this->setData('form', $formContainer->getForm()->render());
		$this->setView('QTIAuthoring/form_response_mapping.tpl');
		
	}
	
	public function saveMappingOptions(){
		$response = $this->getCurrentResponse();
		
		$mappingOptions = $_POST;
		
		$this->service->setMappingOptions($response, $mappingOptions);
		$saved = true;
		
		echo json_encode(array(
			'saved' => $saved
		));
	}
	
	public function saveResponse(){
		
		$saved = false;
		
		//get the response from the interaction:
		$interaction = $this->getCurrentInteraction();
		
		if($this->hasRequestParameter('responseDataString')){
			
			$responseData = json_decode(html_entity_decode($this->getRequestParameter('responseDataString')));
			
			$saved = $this->service->saveInteractionResponse($interaction, $responseData);
		}
		
		echo json_encode(array(
			'saved' => $saved
		));
	}
	
	//edit the interaction response:
	public function editResponse(){
		$interaction = $this->getCurrentInteraction();
		$item = $this->getCurrentItem();
		$responseProcessing = $item->getResponseProcessing();
		
		//get model:
		$columnModel = $this->service->getInteractionResponseColumnModel($interaction, $responseProcessing);
		$responseData = $this->service->getInteractionResponseData($interaction);
		
		if(strtolower($interaction->getType()) == 'order'){
			//special case for order interaction:
			
		}
		
		echo json_encode(array(
			'ok' => true,
			'colModel' => $columnModel,
			'data' => $responseData
		));
		
	}
}
?>
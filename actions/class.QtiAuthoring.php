<?php
/**
 * QtiAuthoring Controller provide actions to edit a QTI item
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

class taoItems_actions_QtiAuthoring extends tao_actions_CommonModule {

	protected $debugMode = false;

	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){

		parent::__construct();

		$this->debugMode = false;
		$this->qtiService = taoItems_models_classes_QTI_Service::singleton();
		$this->service = taoItems_models_classes_QtiAuthoringService::singleton();
		$this->defaultData();

		taoItems_models_classes_QTI_Data::setPersistence(true);
	}

	public function getCurrentItemResource(){

		$itemResource = null;

		if($this->hasRequestParameter('itemUri')){
			$itemResource = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('itemUri')));
		}else{
			throw new Exception('no item rsource uri found');
		}

		return $itemResource;
	}
	/*
	* get the current item object either from the file or from session or create a new one
	*/
	public function getCurrentItem(){

		$item = null;

		$itemUri = tao_helpers_Uri::decode($this->getRequestParameter('instance'));;
		$itemSerial = '';
		$itemIdentifier = tao_helpers_Uri::getUniqueId($itemUri);//TODO: remove coopling to TAO

		//when actions are executed in the authroing tool, retrieve the item with the serial:
		if($this->hasRequestParameter('itemSerial')){
			$itemSerial = tao_helpers_Uri::decode($this->getRequestParameter('itemSerial'));
			$item = $this->qtiService->getItemBySerial($itemSerial);
		}else{
			//try creating a new item:
			$itemFile = html_entity_decode($this->getRequestParameter('xml'));//gonna be ignored

			if(empty($itemFile)){

				//check to allow page reloading without xml file: debug mode on
				if(!empty($itemUri) && $this->debugMode){
					if(isset($_SESSION['tao_qti_item_uris'][tao_helpers_Uri::getUniqueId($itemUri)])){
						$item = $this->qtiService->getItemBySerial($_SESSION['tao_qti_item_uris'][tao_helpers_Uri::getUniqueId($itemUri)]);
					}
				}
				if(empty($item)){
					$itemResource = new core_kernel_classes_Resource($itemUri);

					if(!$this->debugMode) {
						$item = $this->qtiService->getDataItemByRdfItem($itemResource);//i1282039875024462900
					}

					if(is_null($item)){

						//create a new item object:
						$item = $this->service->createNewItem($itemIdentifier, $itemResource->getLabel());
						$_SESSION['tao_qti_item_uris'][tao_helpers_Uri::getUniqueId($itemUri)] = $item->getSerial();
					}
				}

				if(empty($item)){
					throw new Exception('a new qti item xml cannot be created');
				}
			}else{
				//intermediate state??

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

		if(is_null($item)){
			throw new Exception('there is no item');
		}

		return $item;
	}

	public function index(){
		if($this->debugMode){
			$this->setData('debugMode', true);
		}else{
			$this->setData('debugMode', false);

			//clear the QTI session data before doing anything else:
			taoItems_models_classes_QTI_QTISessionCache::singleton()->purge();
			Session::removeAttribute(taoItems_models_classes_QTI_Data::IDENTIFIERS_KEY);
		}

		//required for saving the item in tao:
		$itemUri = $this->getRequestParameter('instance');
		$this->setData('itemUri', tao_helpers_Uri::encode($itemUri));

		$itemResource = new core_kernel_classes_Resource($itemUri);
		foreach($itemResource->getTypes() as $itemClass){
			$this->setData('itemClassUri', tao_helpers_Uri::encode((!is_null($itemClass))?$itemClass->uriResource:''));
			break;
		}

		$currentItem = $this->getCurrentItem();
		$itemData = $this->service->getItemData($currentItem);
		$this->setData('itemSerial', $currentItem->getSerial());
		$this->setData('itemForm', $currentItem->toForm()->render());
		$this->setData('itemData', $itemData);
		$this->setData('jsFramework_path', BASE_WWW.'js/jsframework/');
		$this->setData('qtiAuthoring_path', BASE_WWW.'js/qtiAuthoring/');
		$this->setData('qtiAuthoring_img_path', BASE_WWW.'img/qtiAuthoring/');

		if(isset($_GET['STANDALONE_MODE']) && $_GET['STANDALONE_MODE']){
			$this->setData('includedView', DIR_VIEWS . 'templates/' . "QTIAuthoring/authoring.tpl");
			return parent::setView('sas.tpl', true);
		}else{
			$this->setView("QTIAuthoring/authoring.tpl");
		}

	}

	public function saveItemData(){
		$saved = false;

		$itemData = $this->getPostedItemData();

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

		$interactionData = $this->getPostedInteractionData();
		if(!empty($interactionData)){
			$this->service->setInteractionData($this->getCurrentInteraction(), $interactionData);
			$saved = true;
		}

		if(tao_helpers_Request::isAjax()){
			echo json_encode(array(
				'saved'=>$saved
			));
		}else{
			return $saved;
		}
	}

	public function saveItem(){
		$saved = false;

		$itemData = $this->getPostedItemData();

		$itemObject = $this->getCurrentItem();
		//save item properties in the option array:
		$options = array(
			'title' => $itemObject->getIdentifier(),
			'label' => '',
			'timeDependent' => false,
			'adaptive' => false
		);
		if($this->getRequestParameter('title') != '') $options['title'] = $this->getRequestParameter('title');
		if($this->hasRequestParameter('label')) $options['label'] = $this->getRequestParameter('label');
		if($this->hasRequestParameter('timeDependent')) $options['timeDependent'] = $this->getRequestParameter('timeDependent');
		if($this->hasRequestParameter('adaptive')) $options['adaptive'] = $this->getRequestParameter('adaptive');
		$this->service->setOptions($itemObject, $options);

		if(!empty($itemData)){
			//save item data:
			// print_r($itemData);
			$this->service->saveItemData($itemObject, $itemData);
			//save to qti:



		}

		$itemResource = $this->getCurrentItemResource();
		$saved = $this->qtiService->saveDataItemToRdfItem($itemObject, $itemResource);


		if(tao_helpers_Request::isAjax()){
			echo json_encode(array(
				'saved' => $saved
			));
		}

		return $saved;
	}

	public function preview(){
		$parameters = array(
			'root_url' 				=> ROOT_URL,
        	'base_www' 				=> BASE_WWW,
        	'taobase_www' 			=> TAOBASE_WWW,
			'delivery_server_mode' 	=> false,
			'raw_preview'			=> true,
			'debug'					=> true,
        	'qti_lib_www'			=> BASE_WWW .'js/QTI/',
			'qti_base_www'			=> BASE_WWW .'js/QTI/'
		);
		taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');

		$output = $this->qtiService->renderItem($this->getCurrentItem());

		$output = taoItems_models_classes_QtiAuthoringService::filteredData($output);

		$this->setData('output', $output);
		$this->setView("QTIAuthoring/preview.tpl");
	}

	public function debug(){
		$itemObject = $this->getCurrentItem();

		$this->setData('itemObject', $itemObject);
		$this->setData('sessionData', array('not supported'));
		$this->setView("QTIAuthoring/debug.tpl");
	}

	protected function getPostedItemData(){
		return $this->getPostedData('itemData');
	}

	protected function getPostedInteractionData(){
		return $this->getPostedData('interactionData');
	}

	protected function getPostedData($key, $required = false){
		$returnValue = '';

		if($this->hasRequestParameter($key)){

			$returnValue = html_entity_decode(urldecode($this->getRequestParameter($key)), null, "UTF-8");
			$returnValue = $_POST[$key];
			$returnValue = $this->cleanPostedData($returnValue);

		}else{
			if($required){
				throw new Exception('the request data "'.$key.'" cannot be found');
			}
		}

		return $returnValue;
	}

	protected function cleanPostedData($data){

		$returnValue = '';

		$returnValue = trim($data);

		if(!empty($returnValue)){

			$tidy = new tidy();
			$returnValue = $tidy->repairString (
				$returnValue,
				array(
					'output-xhtml' => true,
					'numeric-entities' => true,
					'show-body-only' => true,
					'quote-nbsp' => false,
					'indent' => 'auto',
					'preserve-entities' => false,
					'quote-ampersand' => true,
					'uppercase-attributes' => false,
					'uppercase-tags' => false
				),
				'UTF8'
			);

			if(!empty($returnValue)){
				try{//Parse data and replace img src by the media service URL
					$updated = false;
					$doc = new DOMDocument;
					if($doc->loadHTML($returnValue)){

						$tags 		= array('img', 'object');
						$srcAttr 	= array('src', 'data');
						$xpath 		= new DOMXpath($doc);
						$query 		= implode(' | ', array_map(create_function('$a', "return '//'.\$a;"), $tags));
						foreach($xpath->query($query) as $element) {
							foreach($srcAttr as $attr){
								if($element->hasAttribute($attr)){
									$source = trim($element->getAttribute($attr));
									if(preg_match("/taoItems\/Items\/getMediaResource\?path=/", $source)){
										$path = substr($source, strpos($source, '?path=') + 6);
										if (strpos($path, '&')) {
											$path = substr($path, 0, strpos($path, '&'));
										}
										$element->setAttribute($attr,  urldecode($path));
										$updated = true;
									}
								}
							}
						}
					}

					if($updated){
						$returnValue = $doc->saveHTML();
					}
				}
				catch(DOMException $de){
					//we render it anyway
				}
			}

		}

		return $returnValue;
	}

	public function addInteraction(){
		$added = false;
		$interactionSerial = '';

		$interactionType = $this->getRequestParameter('interactionType');
		$itemData = $this->getPostedItemData();

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
			'itemData' => $itemData
		));
	}

	public function addHotText(){
		$added = false;
		$choiceSerial = '';//the hot text basically is a "choice"
		$textContent = '';

		$interactionData = $this->getPostedInteractionData();

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
			'groupSerial' => $groupSerial,
			'reload' => ($added)?$this->requireChoicesUpdate($interaction):false
		));
	}


	public function deleteInteractions(){

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
		$deleted = false;

		try{
			$choice = null;
			$choice = $this->getCurrentChoice();
		}catch(Exception $e){}
		if(!is_null($interaction) && !is_null($choice)){
			$this->service->deleteChoice($interaction, $choice);
			$deleted = true;
		}

		if(!$deleted){
			try{
				//for gapmatch interaction, where a gorup is considered as a choice:
				$group = null;
				$group = $this->getCurrentGroup();

				if(!is_null($interaction) && !is_null($group)){
					$this->service->deleteGroup($interaction, $group);
					$deleted = true;
				}
			}catch(Exception $e){
				throw new Exception('cannot delete the choice');
			}
		}

		echo json_encode(array(
			'deleted' => $deleted,
			'reload' => ($deleted)?$this->requireChoicesUpdate($interaction):false,
			'reloadInteraction' => ($deleted)?$this->requireInteractionUpdate($interaction):false
		));
	}

	protected function requireChoicesUpdate(taoItems_models_classes_QTI_Interaction $interaction){

		$reload = false;

		//basically, interactions that have choices with the "matchgroup" property
		if(!is_null($interaction)){
			switch(strtolower($interaction->getType())){
				case 'associate':
				case 'match':
				case 'gapmatch':
				case 'graphicgapmatch':{
					$reload = true;
					break;
				}
			}
		}

		return $reload;
	}

	protected function requireInteractionUpdate(taoItems_models_classes_QTI_Interaction $interaction){

		$reload = false;

		//basically, interactions that need a wysiwyg data editor:
		if($this->getRequestParameter('reloadInteraction')){
			if(!is_null($interaction)){
				switch(strtolower($interaction->getType())){
					case 'hottext':
					case 'gapmatch':{
						$reload = true;
						break;
					}
				}
			}
		}

		return $reload;
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
				//second chance: try getting the response from the interaction, is set in the request parameter
				$interaction = $this->getCurrentInteraction();
				if(!empty($interaction)){
					$response = $this->service->getInteractionResponse($interaction);
					if(!empty($response)){
						$returnValue = $response;
					}
				}
			}catch(Exception $e){
				throw new common_exception_Error('cannot find the response no request parameter "responseSerial" found');
			}

		}

		return $returnValue;
	}

	public function getCurrentResponseProcessing(){
		$returnValue = null;
		if($this->hasRequestParameter('responseprocessingSerial')){
			$responseprocessing = $this->qtiService->getDataBySerial($this->getRequestParameter('responseprocessingSerial'), 'taoItems_models_classes_QTI_response_ResponseProcessing');
			if(!empty($responseprocessing)){
				$returnValue = $responseprocessing;
			}
		}else{
			try{
				//second chance: try getting the responseprocessing from the item
				$item = $this->getCurrentItem();
				if(!empty($item)){
					$responseprocessing = $this->service->getResponseProcessing($item);
					if(!empty($responseprocessing)){
						$returnValue = $responseprocessing;
					}
				}
			}catch(Exception $e){
				throw new Exception('cannot find the responseProcessing no request parameter "responseprocessingSerial" found');
			}

		}

		return $returnValue;
	}

	public function getCurrentOutcome(){
		$returnValue = null;
		if($this->hasRequestParameter('outcomeSerial')){
			$outcome = $this->qtiService->getDataBySerial($this->getRequestParameter('outcomeSerial'), 'taoItems_models_classes_QTI_Outcome');
			if(!empty($outcome)){
				$returnValue = $outcome;
			}
		}else{
			throw new common_exception_Error('cannot find the outcome no request parameter "outcomeSerial" found');
		}

		return $returnValue;
	}

	public function editItem(){

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
		switch($interactionType){
			case 'match':{
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
				break;
			}
			case 'gapmatch':{
				/*
				//get group form:
				$groupForms = array();
				foreach($this->service->getInteractionGroups($interaction) as $group){
					//order does not matter:
					$groupForms[] = $group->toForm($interaction)->render();
				}
				$this->setData('formGroups', $groupForms);

				//get choice forms:
				foreach($choices as $order=>$choice){
					$choiceForms[$choice->getSerial()] = $choice->toForm()->render();
				}
				*/
				break;
			}
			//graphic interactions:
			case 'graphicgapmatch':{
				$groups = array();
				foreach($interaction->getGroups() as $group){
					$groups[] = $group->getSerial();
				}

				$this->setData('groups', $groups);
			}
			case 'hotspot':
			case 'graphicorder':
			case 'graphicassociate':
			case 'selectpoint':
			case 'positionobject':{
				$object = $interaction->getObject();

				$bgImagePath = '';
				if(isset($object['data'])){
					if(!empty($object['data'])){
						$bgImagePath = trim($object['data']);
						//in case of relative path, we use the service
						if(!preg_match("/^http/", $bgImagePath)){
							$bgImagePath =  _url('getMediaResource', 'Items', 'taoItems',array('path' => urlencode($bgImagePath)));
						}
					}
				}
				$this->setData('backgroundImagePath',$bgImagePath);

				if(isset($object['width'])){
					$this->setData('width', (intval($object['width'])>0)?$object['width']:'');
				}
				if(isset($object['height'])){
					$this->setData('height', (intval($object['height'])>0)?$object['height']:'');
				}
				break;
			}
			default:{
				//get choice forms:
				foreach($choices as $order=>$choice){
					$choiceForms[$choice->getSerial()] = $choice->toForm()->render();
				}
			}
		}

		//display the template, according to the type of interaction
		$templateName = 'QTIAuthoring/form_interaction_'.strtolower($interaction->getType()).'.tpl';
		$this->setData('interactionSerial', $interaction->getSerial());
		$this->setData('formInteraction', $myForm->render());
		$this->setData('formChoices', $choiceForms);
		$this->setData('interactionData', $this->service->getInteractionData($interaction));
		//$this->setData('interactionData', html_entity_decode($this->service->getInteractionData($interaction)));
		$this->setData('orderedChoices', $choices);
		$this->setView($templateName);
	}

	//called on interaction edit form loaded
	//called when the choices forms need to be reloaded
	public function editChoices(){

		$interaction = $this->getCurrentInteraction();

		//get the itnteraction's choices
		$choices = $this->service->getInteractionChoices($interaction);
		$choiceForms = array();

		$interactionType = strtolower($interaction->getType());
		switch($interactionType){
			case 'match':{
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
				break;
			}
			case 'gapmatch':
			case 'graphicgapmatch':{
				//get group form:
				$groupForms = array();
				foreach($this->service->getInteractionGroups($interaction) as $group){
					//order does not matter:
					$groupForms[$group->getSerial()] = $group->toForm($interaction)->render();
				}
				$this->setData('formGroups', $groupForms);

				//get choice forms:
				foreach($choices as $order=>$choice){
					$choiceForms[$choice->getSerial()] = $choice->toForm()->render();
				}
				break;
			}
			default:{
				//get choice forms:
				foreach($choices as $order=>$choice){
					$choiceForms[$choice->getSerial()] = $choice->toForm()->render();
				}
			}
		}

		$templateName = 'QTIAuthoring/form_choices_'.strtolower($interaction->getType()).'.tpl';
		$this->setData('formChoices', $choiceForms);
		$this->setData('orderedChoices', $choices);
		$this->setView($templateName);
	}

	public function saveInteraction(){

		$interaction = $this->getCurrentInteraction();

		$myForm = $interaction->toForm();

		$saved = false;
		$reloadResponse = false;
		$newGraphicObject = array();

		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$values = $myForm->getValues();

				if(isset($values['interactionIdentifier'])){
					if($values['interactionIdentifier'] != $interaction->getIdentifier()){
						$this->service->setIdentifier($interaction, $values['interactionIdentifier']);
					}
					unset($values['interactionIdentifier']);
				}

				//for block interactions
				if(isset($values['prompt'])){
					$this->service->setPrompt($interaction, $this->getPostedData('prompt'));
					unset($values['prompt']);
				}

				//for graphic interactions:
				if(isset($values['object_width'])){
					if(intval($values['object_width'])) $newGraphicObject['width'] = intval($values['object_width']);
					unset($values['object_width']);
				}

				if(isset($values['object_height'])){
					if(intval($values['object_height'])) $newGraphicObject['height'] = intval($values['object_height']);
					unset($values['object_height']);
				}

				$errorMessage = '';
				if(isset($values['object_data'])){

					$oldObject = $interaction->getObject();

					//get mime type
					$imageFilePath = trim($values['object_data']);
					$imgProperties = $this->getImageProperties($imageFilePath);
					if(!empty($imgProperties)){
						$newGraphicObject['data'] = $imageFilePath;
						$newGraphicObject['type'] = $imgProperties['mime'];
					}else{
						$errorMessage = __('invalid image mime type');
					}
					unset($values['object_data']);
				}

				$interaction->setObject($newGraphicObject);
				if(!empty($errorMessage)) $newGraphicObject['errorMessage'] = $errorMessage;

				unset($values['interactionSerial']);

				foreach($values as $key=>$value){
					if(preg_match('/^max/', $key)){
						if($interaction->getOption($key) != $value){
							$reloadResponse = true;
						}
						break;
					}
				}

				//save all options before updating the interaction response
				$this->service->editOptions($interaction, $values);
				if($reloadResponse){
					//update the cardinality, just in case it has been changed:
					//may require upload of the response form, since the maximum allowed response may have changed!
					$this->service->updateInteractionResponseOptions($interaction);

					//costly...
					//then simulate get+save response data to filter affected response variables
					$this->service->saveInteractionResponse($interaction, $this->service->getInteractionResponseData($interaction));
				}

				$choiceOrder = array();
				if(isset($_POST['choiceOrder'])){

					$choiceOrder = $_POST['choiceOrder'];

				}elseif( isset($_POST['choiceOrder0']) && isset($_POST['choiceOrder1'])){//for match interaction

					for($i=0; $i<2; $i++){//TODO: to be tested...
						$groupOrder = $_POST['choiceOrder'.$i];
						if(isset($groupOrder['groupSerial'])){
							$groupSerial = $groupOrder['groupSerial'];
							unset($groupOrder['groupSerial']);
							$choiceOrder[$groupSerial] = $groupOrder;
						}
					}

				}
				$this->service->setInteractionData($interaction, $this->getPostedInteractionData(), $choiceOrder);

				$saved  = true;
			}
		}

		echo json_encode(array(
			'saved' => $saved,
			'reloadResponse' => $reloadResponse,
			'newGraphicObject' => $newGraphicObject
		));

	}

	private function getImageProperties($imageFilePath){

		$returnValue = array();

		if(!empty($imageFilePath)){

			if(!preg_match("/^http/", $imageFilePath)){
				if(Session::hasAttribute('uri') && Session::hasAttribute('classUri')){
					$itemService = taoItems_models_classes_ItemsService::singleton();
					$classUri = tao_helpers_Uri::decode(Session::getAttribute('classUri'));
					if($itemService->isItemClass(new core_kernel_classes_Class($classUri))){
						$item = new core_kernel_classes_Resource( tao_helpers_Uri::decode(Session::getAttribute('uri')));
						if(!is_null($item)){
							$folder 	= $itemService->getItemFolder($item);
							$imageFilePath 	= tao_helpers_File::concat(array($folder, $imageFilePath));
						}
					}
				}
			}

			if (@fclose(@fopen($imageFilePath, "r"))){//check if file remotely exists, might be improved with cURL

				$mimeType = tao_helpers_File::getMimeType($imageFilePath);
				$validImageType = array(
					'image/png',
					'image/jpeg',
					'image/bmp',
					'image/gif',
					'image/vnd.microsoft.icon',
					'image/tiff'
				);

				if(in_array($mimeType, $validImageType)){
					$returnValue['mime'] = $mimeType;
				}

			}

		}

		return $returnValue;
	}

	public function saveChoice(){
		$choice = $this->getCurrentChoice();

		$myForm = $choice->toForm();
		$saved = false;
		$identifierUpdated = false;
		$errorMessage = '';

		if($myForm->isSubmited()){
			if($myForm->isValid()){

				$values = $myForm->getValues();
				unset($values['choiceSerial']);//choiceSerial to be deleted since only used to get the choice qti object

				if(isset($values['choiceIdentifier'])){
					if($values['choiceIdentifier'] != $choice->getIdentifier()){
						$this->service->setIdentifier($choice, $values['choiceIdentifier']);
						$identifierUpdated = true;
					}
					unset($values['choiceIdentifier']);
				}

				if(isset($values['data'])){
					$this->service->setData($choice, $this->getPostedData('data'));
					unset($values['data']);
				}

				//for graphic interactions:
				$newGraphicObject = array();
				if(isset($values['object_width'])){
					if(intval($values['object_width'])) $newGraphicObject['width'] = intval($values['object_width']);
					unset($values['object_width']);
				}
				if(isset($values['object_height'])){
					if(intval($values['object_height'])) $newGraphicObject['height'] = intval($values['object_height']);
					unset($values['object_height']);
				}
				if(isset($values['object_data'])){

					// $oldObject = $choice->getObject();

					//get mime type
					$imageFilePath = trim($values['object_data']);
					$imgProperties = $this->getImageProperties($imageFilePath);
					if(!empty($imgProperties)){
						$newGraphicObject['data'] = $imageFilePath;
						$newGraphicObject['type'] = $imgProperties['mime'];
					}else{
						$errorMessage = __('invalid image mime type for the image file '.$imageFilePath);
					}
					unset($values['object_data']);
				}
				$choice->setObject($newGraphicObject);
				// unset($values['object_data']);

				//finally save the other options:
				$this->service->setOptions($choice, $values);

				$saved = true;
			}
		}

		$choiceFormReload = false;
		if($identifierUpdated){
			$interaction = $this->qtiService->getComposingData($choice);
			$choiceFormReload = $this->requireChoicesUpdate($interaction);
			$interaction->addChoice($choice);
			$interaction = null;
		}

		echo json_encode(array(
			'saved' => $saved,
			'choiceSerial' => $choice->getSerial(),
			'identifierUpdated' => $identifierUpdated,
			'reload' => $choiceFormReload,
			'errorMessage' => (string)$errorMessage
		));
	}

	//save the group properties, specific to gapmatch interaction where a group is considered as a gap:
	//not called when the choice order has been changed, such changes are done by saving the itneraction data
	public function saveGroup(){
		$group = $this->getCurrentGroup();
		$interaction = $this->qtiService->getComposingData($group);

		$myForm = $group->toForm();
		$saved = false;
		$identifierUpdated = false;
		$newIdentifier = '';

		if($myForm->isSubmited()){
			if($myForm->isValid()){

				$values = $myForm->getValues();

				if(isset($values['groupIdentifier'])){
					if($values['groupIdentifier'] != $group->getIdentifier()){
						$newIdentifier = $values['groupIdentifier'];
						$identifierUpdated = $this->service->setIdentifier($group, $values['groupIdentifier']);
					}
				}

				$matchGroup = array();
				if(!empty($values['matchGroup']) && is_array($values['matchGroup'])){
					foreach($values['matchGroup'] as $choiceIdentifier){
						$choice = $this->service->getInteractionChoiceByIdentifier($interaction, $choiceIdentifier);
						if(!is_null($choice)){
							$matchGroup[] = $choice->getSerial();
						}
					}
				}
				unset($values['matchGroup']);
				$group->setChoices($matchGroup);


				$choiceOrder = array();
				if(isset($_POST['choiceOrder'])){
					$choiceOrder = $_POST['choiceOrder'];
				}
				$this->service->setGroupData($group, $choiceOrder, null, true);//the 3rd parameter interaction is not required as the method only depends on the group

				unset($values['groupSerial']);
				unset($values['groupIdentifier']);
				$this->service->setOptions($group, $values);

				$saved = true;
			}
		}

		$choiceFormReload = false;
		if($identifierUpdated){

			$choiceFormReload = $this->requireChoicesUpdate($interaction);
			$interaction->addGroup($group);

		}
		$interaction = null;

		echo json_encode(array(
			'saved' => $saved,
			'groupSerial' => $group->getSerial(),
			'identifierUpdated' => $identifierUpdated,
			'newIdentifier' => $newIdentifier,
			'reload' => $choiceFormReload
		));
	}

	public function addGroup(){
		$added = false;
		$groupSerial = '';//a gap basically is a "group", the content of which is by default all available choices in the interaction
		$textContent = '';
		$interaction = null;
		$interaction = $this->getCurrentInteraction();
		$interactionData = $this->getPostedInteractionData();

		$group = $this->service->addGroup($interaction, $interactionData);

		if(!is_null($group)){
			$interactionData = $this->service->getInteractionData($interaction);//do not convert to html entities...

			//everything ok:
			$added = true;
			$groupSerial = $group->getSerial();
		}

		echo json_encode(array(
			'added' => $added,
			'groupSerial' => $groupSerial,
			'groupForm' => $group->toForm()->render(),
			'interactionData' => html_entity_decode($interactionData),
			'reload' => ($added)?$this->requireChoicesUpdate($interaction):false
		));
	}

	public function editResponseProcessing(){

		$item = $this->getCurrentItem();

		$formContainer = new taoItems_actions_QTIform_ResponseProcessing($item);
		$myForm = $formContainer->getForm();

		$this->setData('form', $myForm->render());
		$processingType = $formContainer->getProcessingType();

		// $responseMappingMode = false;
		// if($processingType == QTI_RESPONSE_TEMPLATE_MAP_RESPONSE || $processingType == QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT){
			// $responseMappingMode = true;
		// }
		// $this->setData('responseMappingMode', $responseMappingMode);//no longer definied in the item response proc:

		$warningMessage = '';
		if($processingType == 'custom'){
			$warningMessage = __('The custom response processing type is currently not fully supported in this tool. Removing interactions or choices is not recommended.');
		}

		$this->setData('warningMessage', $warningMessage);
		$this->setView('QTIAuthoring/form_response_processing.tpl');
	}

	public function saveItemResponseProcessing(){

		$item = $this->getCurrentItem();
		$responseProcessingType = tao_helpers_Uri::decode($this->getRequestParameter('responseProcessingType'));
		$customRule = $this->getRequestParameter('customRule');

		$saved = $this->service->setResponseProcessing($item, $responseProcessingType, $customRule);

		echo json_encode(array(
			'saved' => $saved,
			'responseMode' => $this->isResponseMappingMode($responseProcessingType)
		));
	}

	public function saveInteractionResponseProcessing(){
		$response = $this->getCurrentResponse();
		$rp = $this->getCurrentResponseProcessing();

		if(!is_null($response) && !is_null($rp)){
			if ($rp instanceof taoItems_models_classes_QTI_response_TemplatesDriven) {
				$saved					= false;
				$setResponseMappingMode	= false;
				$templateHasChanged		= false;
				if($this->hasRequestParameter('processingTemplate')){
					$processingTemplate = tao_helpers_Uri::decode($this->getRequestParameter('processingTemplate'));
					if ($rp->getTemplate($response) != $processingTemplate) {
						$templateHasChanged = true;
					}
					$saved = $rp->setTemplate($response, $processingTemplate);
					if($saved) $setResponseMappingMode = $this->isResponseMappingMode($processingTemplate);
				}
				echo json_encode(array(
					'saved'						=> $saved,
					'setResponseMappingMode'	=> $setResponseMappingMode,
					'hasChanged'				=> $templateHasChanged
				));
			} elseif ($rp instanceof taoItems_models_classes_QTI_response_Composite) {
				$currentIRP		= $rp->getInteractionResponseProcessing($response);
				$currentClass	= get_class($currentIRP);
				$saved			= false;
				$classID		= $currentClass::CLASS_ID;

				if($this->hasRequestParameter('interactionResponseProcessing')) {
					$item = $this->qtiService->getComposingData($rp);
					$classID		= $this->getRequestParameter('interactionResponseProcessing');
					if ($currentClass::CLASS_ID != $classID) {
						$newIRP = taoItems_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing::create(
							$classID,
							$response,
							$item
						);
						$rp->replace($newIRP);
						$saved = true;
					}
				}
				echo json_encode(array(
					'saved'						=> $saved,
					'setResponseOptionsMode'	=> $classID
				));
			}
		}

	}

	protected function isResponseMappingMode($processingType){
		$responseMappingMode = false;
		if($processingType == QTI_RESPONSE_TEMPLATE_MAP_RESPONSE || $processingType == QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT){
			$responseMappingMode = true;
		}

		return $responseMappingMode;
	}


	public function editMappingOptions(){
		$response = $this->getCurrentResponse();

		$formContainer = new taoItems_actions_QTIform_Mapping($response);

		$this->setData('form', $formContainer->getForm()->render());
		$this->setView('QTIAuthoring/form_response_mapping.tpl');

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

	public function saveResponseProperties(){

		$saved = false;
		$response = $this->getCurrentResponse();

		if(!is_null($response)){

			if($this->hasRequestParameter('baseType')){
				if($this->hasRequestParameter('baseType')){
					$this->service->editOptions($response, array('baseType'=>$this->getRequestParameter('baseType')));
					$saved = true;
				}

				if($this->hasRequestParameter('ordered')){
					if(intval($this->getRequestParameter('ordered')) == 1){
						$this->service->editOptions($response, array('cardinality'=>'ordered'));
					}else{
						//reset the cardinality:
						$parentInteraction = $this->qtiService->getComposingData($response);
						if(!is_null($parentInteraction)){
							$this->service->editOptions($response, array('cardinality' => $parentInteraction->getCardinality() ));
							// taoItems_models_classes_QTI_Service::saveDataToSession($parentInteraction);
							$parentInteraction = null;//destroy it!
						}else{
							throw new Exception('cannot find the parent interaction');
						}

					}
					$saved = true;
				}
			}

		}

		echo json_encode(array(
			'saved' => $saved,
		));
	}

	public function saveResponseCodingOptions(){

		$interaction = $this->getCurrentInteraction();
		$rp = $this->getCurrentResponseProcessing();
		$form = null;

		// cases
		if ($rp instanceof taoItems_models_classes_QTI_response_TemplatesDriven) {
			$form = 'template';
		} elseif ($rp instanceof taoItems_models_classes_QTI_response_Composite) {
			$irp = $rp->getInteractionResponseProcessing($interaction->getResponse());
			if ($irp instanceof taoItems_models_classes_QTI_response_interactionResponseProcessing_None) {
				$form = 'manual';
			} elseif (in_array(get_class($irp), array(
				'taoItems_models_classes_QTI_response_interactionResponseProcessing_MatchCorrectTemplate',
				'taoItems_models_classes_QTI_response_interactionResponseProcessing_MapResponseTemplate',
				'taoItems_models_classes_QTI_response_interactionResponseProcessing_MapResponsePointTemplate'))) {

				$form = 'template';
			}
		}

		if ($form == 'template') {
			$response = $interaction->getResponse();
			$mappingOptions = $_POST;

			$this->service->setMappingOptions($response, $mappingOptions);
			$saved = true;

			echo json_encode(array(
				'saved' => $saved
			));

		} elseif ($form == 'manual') {
			$irp = $rp->getInteractionResponseProcessing($interaction->getResponse());
			$saved = false;
			$outcome = $this->getCurrentOutcome();

			// set guidelines
			if ($this->hasRequestParameter('guidelines')) {
				$values = array(
					'interpretation' => $this->getRequestParameter('guidelines')
				);
				$saved = $this->service->editOptions($outcome, $values) || $saved;
			}

			// set correct answer
			if ($this->hasRequestParameter('correct')) {
				$responseData = array(array(
					'choice1' => $this->getRequestParameter('correct'),
					'correct' => 'yes'
				));
				$saved = $this->service->saveInteractionResponse($this->getCurrentInteraction(), $responseData) || $saved;
			}

			// set guidelines
			if ($this->hasRequestParameter('defaultValue')) {
				$irp->setDefaultValue($this->getRequestParameter('defaultValue'));
				$saved = true;
			}

			// set scale
			if ($this->hasRequestParameter('scaletype')) {

				if (strlen(trim($this->getRequestParameter('scaletype'))) > 0) {
					$uri = tao_helpers_Uri::decode($this->getRequestParameter('scaletype'));
					$scale = taoItems_models_classes_Scale_Scale::createByClass($uri);

					if ($this->hasRequestParameter('min')) {
						$scale->lowerBound = (floatval($this->getRequestParameter('min')));
					}
					if ($this->hasRequestParameter('max')) {
						$scale->upperBound = (floatval($this->getRequestParameter('max')));
					}
					if ($this->hasRequestParameter('dist')) {
						$scale->distance = (floatval($this->getRequestParameter('dist')));
					}
					$outcome->setScale($scale);
					$saved = true;
				} else {
					$outcome->removeScale();
					$saved = true;
				}
			}

			echo json_encode(array(
				'saved' => $saved
			));
		} else {
			echo json_encode(array(
				'saved' => false
			));
		}

	}

	//edit the interaction response:
	public function editResponse(){

		$item = $this->getCurrentItem();
		$responseProcessing = $item->getResponseProcessing();
		$interaction = $this->getCurrentInteraction();
		$response = $this->service->getInteractionResponse($interaction);

		$displayGrid = false;
		$isResponseMappingMode = false;
		$columnModel = array();
		$responseData = array();
		$xhtmlForms = array();
		$interactionType = strtolower($interaction->getType());

		//response options independant of processing
		$response = $this->service->getInteractionResponse($interaction);
		$responseForm = $response->toForm();
		if(!is_null($responseForm)){
			$xhtmlForms[] = $responseForm->render();
		}

		//set the processing mode
		$rpform = $responseProcessing->getForm($response);
		if (!is_null($rpform)) {
			$xhtmlForms[] = $rpform->render();
		}

		$data = array(
			'ok' => true,
			'interactionType' => $interactionType,
			'maxChoices' => intval($interaction->getCardinality(true)),
			'forms'	=> $xhtmlForms,
		);
		//proccessing related form
		foreach (taoItems_helpers_qti_InteractionAuthoring::getIRPData($item, $interaction) as $key => $value) {
			if (isset($data[$key]) && is_array($data[$key])) {
				foreach ($value as $v) {
					$data[$key][] = $v;
				}
			} else {
				$data[$key]= $value;
			}
		}
		$data['responseForm'] = implode('', $data['forms']);

		echo json_encode($data);

	}

	public function manageStyleSheets(){
		//create upload form:
		$item = $this->getCurrentItem();
		$formContainer = new taoItems_actions_QTIform_CSSuploader($item, $this->getRequestParameter('itemUri'));
		$myForm = $formContainer->getForm();

		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$data = $myForm->getValues();

				if(isset($data['css_import']['uploaded_file'])){
					//get the file and store it in the proper location:
					$baseName = basename($data['css_import']['uploaded_file']);

					$fileData = $this->getCurrentStyleSheet($baseName);

					if(!empty($fileData)){
						tao_helpers_File::move($data['css_import']['uploaded_file'], $fileData['path']);

						$cssFiles = $item->getStyleSheets();
						$cssFiles[] = array(
							'title' => empty($data['title'])?$data['css_import']['name']:$data['title'],
							'href' => $fileData['href'],
							'type' => 'text/css',
							'media' => 'qti item body'
						);
						$item->setStyleSheets($cssFiles);
					}
				}

			}
		}

		$cssFiles = array();
		foreach($item->getStyleSheets() as $file){
			$cssFiles[] = array(
				'href' => $file['href'],
				'title' => $file['title'],
				'downloadUrl' => _url('getStyleSheet', null, null, array(
						'itemSerial' => tao_helpers_Uri::encode($item->getSerial()),
						'itemUri' 	=> tao_helpers_Uri::encode($this->getCurrentItemResource()->uriResource),
						'css_href' => $file['href']
				))
			);
		}

		$this->setData('formTitle', __('Manage item content'));
		$this->setData('myForm', $myForm->render());
		$this->setData('cssFiles', $cssFiles);
		$this->setView('QTIAuthoring/css_manager.tpl');
	}

	public function deleteStyleSheet(){

		$deleted = false;

		$fileData = $this->getCurrentStyleSheet();

		//get the full path of the file and unlink the file:
		if(!empty($fileData)){
			tao_helpers_File::remove($fileData['path']);

			$item = $this->getCurrentItem();

			$files = $item->getStylesheets();

			foreach($files as $key=>$file){
				if($file['href'] == $fileData['href']){
					unset($files[$key]);
				}
			}

			$item->setStylesheets($files);

			$deleted = true;
		}

		echo json_encode(array('deleted' => $deleted));
	}

	public function getStyleSheet(){
		$fileData = $this->getCurrentStyleSheet();
		if (!empty($fileData)) {
			$fileName = basename($fileData['path']);

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: public");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Description: File Transfer");
			header("Content-Type: text/css");
			header("Content-Disposition: attachment; filename=\"$fileName\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($fileData['path']));

			echo file_get_contents($fileData['path']);
		} else {
			throw new Exception('The style file cannot be found');
		}
	}

	public function getCurrentStyleSheet($baseName=''){
		$returnValue = array();
		$itemResource = $this->getCurrentItemResource();
		$basePath = taoItems_models_classes_ItemsService::singleton()->getItemFolder($itemResource);
		$baseWWW = taoItems_models_classes_ItemsService::singleton()->getRuntimeFolder($itemResource);

		if (!empty($baseName)) {
			//creation mode:
			$css_href = 'style/'.$baseName;

			$returnValue = array(
				'href' => $css_href,
				'type' => 'text/css',
				'title' => $baseName,
				'path' => $basePath.'/'.$css_href,
				'hrefAbsolute' => $baseWWW.'/'.$css_href
			);
		} else {
			//get mode:
			$css_href = $this->getRequestParameter('css_href');
			if (!empty($css_href)) {
				$files = $this->getCurrentItem()->getStylesheets();
				foreach ($files as $file) {
					if ($file['href'] == $css_href) {
						$returnValue = $file;
						$returnValue['path'] = $basePath.'/'.$css_href;
						$returnValue['hrefAbsolute'] = $baseWWW.'/'.$css_href;
						break;
					}
				}
			}
		}

		return $returnValue;
	}

	public function addObject() {
		$object = new taoItems_models_classes_QTI_Object(null, array('data' => '', 'type' => ''));
		$this->getCurrentItem()->addObject($object);
		common_Logger::d('Added object '.$object->getSerial(), array('TAOITEMS', 'QTI'));
		echo json_encode(array(
				'success'	=> true,
				'objectSerial'	=> $object->getSerial(),
				'objectData'	=> $this->service->getObjectTag($object)
			));
	}

	public function deleteObjects() {
		$deleted = false;

		$objectSerials = array();
		if ($this->hasRequestParameter('objectSerials')) {
			$objectSerials = $this->getRequestParameter('objectSerials');
		}
		if (empty($objectSerials)) {
			throw new Exception('no object ids found to be deleted');
		} else {
			$item = $this->getCurrentItem();
			$deleteCount = 0;

			//delete objects:
			foreach ($objectSerials as $objectSerial) {
				$object = $this->qtiService->getDataBySerial($objectSerial);
				if (!empty($object)) {
					$this->service->deleteObject($item, $object);
					$deleteCount++;
				} else {
					throw new Exception('no object found to be deleted with the serial: '.$objectSerial);
				}
			}

			if ($deleteCount == count($objectSerials)) {
				$deleted = true;
			}
		}

		echo json_encode(array(
			'deleted' => $deleted
		));
	}

	public function editObject() {
		//instantiate the item content form container
		foreach ($this->getCurrentItem()->getObjects() as $object) {
			if ($object->getSerial() == $this->getRequestParameter('objectSerial')) {
				$myObject = $object;
				break;
			}
		}
		if (!isset($myObject)) {
			throw new common_Exception('Object not found');
		}
		common_Logger::d('editObject '.$myObject->getSerial());

		$formContainer = new taoItems_actions_QTIform_EditObject($myObject, $this->getCurrentItem());
		$myForm = $formContainer->getForm();

		if ($myForm->isSubmited() && $myForm->isValid()) {
			//Url
			$url = $this->getRequestParameter('objecturl');
			$myObject->setOption('data', $url);

			//Mime-type
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
			$content = curl_exec($ch);
			if (!curl_errno($ch)) {
				$info = curl_getinfo($ch);
				$myObject->setOption('type', $info['content_type']);
			} else {
				common_Logger::d('Error getting the ressource : '.$url, array('TAOITEMS', 'QTI'));
			}
			curl_close($ch);
			common_Logger::d('Type : '.$myObject->getOption('type'), array('TAOITEMS', 'QTI'));

			//Width
			if ($this->hasRequestParameter('width') && intval($this->getRequestParameter('width')) > 0) {
				$myObject->setOption('width', $this->getRequestParameter('width'));
			}

			//Height
			if ($this->hasRequestParameter('height') && intval($this->hasRequestParameter('height')) > 0) {
				$myObject->setOption('height', $this->getRequestParameter('height'));
			}

			common_Logger::d('Edited object '.$myObject->getSerial(), array('TAOITEMS', 'QTI'));
			echo json_encode(array(
				'success'	=> true,
			));
		} else {
			echo json_encode(array('html' => $myForm->render(), 'title' =>  __('Edit object')));
		}
	}

}
?>

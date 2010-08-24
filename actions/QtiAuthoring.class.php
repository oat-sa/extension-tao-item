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
		
		$itemId = '';
		if($this->hasRequestParameter('instance')){
			$itemId = 'qti_item_'.tao_helpers_Uri::getUniqueId(tao_helpers_Uri::decode($this->getRequestParameter('instance')));
		}elseif($this->hasRequestParameter('itemId')){
			$itemId = tao_helpers_Uri::decode($this->getRequestParameter('itemId'));
		}else{
			throw new Exception('no current id for the item');
		}
		
		$itemFile = $this->getRequestParameter('xml');
		if(empty($itemFile)){
			
			//debug
			var_dump(unserialize(Session::getAttribute($itemId)));
			
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
		$itemData = $this->service->getItemData($this->getCurrentItem());
		
		// $this->setData('htmlbox_wysiwyg_path', BASE_WWW.'js/HtmlBox_4.0/');//script that is not working
		$this->setData('itemData', $itemData);
		$this->setData('jwysiwyg_path', BASE_WWW.'js/jwysiwyg/');
		$this->setData('simplemodal_path', BASE_WWW.'js/simplemodal/');
		$this->setData('qtiAuthoring_path', BASE_WWW.'js/qtiAuthoring/');
		$this->setView("QTIAuthoring/authoring.tpl");
	}
	
	public function saveItem(){
		//replace all interaction authoring tag with the corresponding {id}:
		$itemData = $this->getRequestParameter('itemData');
		if(!empty($itemData)){
			// $cleanedItemData = $this->stripInteractionTag();
			
			//save to qti:
			$this->service->saveItem($this->getCurrentItem(), $itemData);
		}
	}
	
	public function reviewItem(){
		//compile the item then display the result...
	}
	
	public function addInteraction(){
		$added = false;
		$interactionId = '';
		
		$interactionType = $this->getRequestParameter('interactionType');
		$itemData = urldecode($this->getRequestParameter('itemData'));
		
		if(!empty($interactionType)){
			$interaction = $this->service->addInteraction($this->getCurrentItem(), $interactionType);
			if(!is_null($interaction)){
				
				//save the itemData, i.e. the location at which the new interaction shall be inserted
				//the location has been marked with {qti_interaction_new}
				$itemData = preg_replace("/{qti_interaction_new}/", "{{$interaction->getId()}}", $itemData, 1);
				
				//everything ok:
				$added = true;
				$interactionId = $interaction->getId();
			}
		}
		
		echo json_encode(array(
			'added' => $added,
			'interactionId' => $interactionId
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
	
	public function editInteraction(){
		$interaction = $this->getCurrentInteraction();
		
		//build the form with its method "toForm"
	}
}
?>
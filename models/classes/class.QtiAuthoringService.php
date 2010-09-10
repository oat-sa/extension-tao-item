<?php

error_reporting(E_ALL);

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

require_once('tao/models/classes/class.GenerisService.php');

/**
 * Service methods to manage the QTI authoring business
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage models_classes
 */
class taoItems_models_classes_QtiAuthoringService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The RDFS top level item class
     *
     * @access protected
     * @var Class
     */
    protected $itemClass = null;
	
	protected $qtiService = null;
	
    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return void
     */
    public function __construct()
    {
		parent::__construct();
		$this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
    }
	
	/**
     * This method creates a new item object to be used as the data container of the qtiAuthoring tool
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return taoItems_models_classes_QTI_Item
     */
	public function createNewItem($itemIdentifier=''){
		
		$returnValue = null;
		
		$returnValue = new taoItems_models_classes_QTI_Item($itemIdentifier, array());
		
		// $itemId = tao_helpers_Uri::getUniqueId($itemUri);
		// if(empty($itemId)){
			// throw new Exception('wrong format of itemUri given');
		// }else{
			// $itemId = 'qti_item_'.$itemId;
			// $returnValue = new taoItems_models_classes_QTI_Item($itemId, array());
		// }
		// var_dump($itemId, $returnValue);
		
		return $returnValue;
	}
	
	/**
     * Returns the item data after replacing the interaction tags with the element identifier of the authoring tool
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return taoItems_models_classes_QTI_Item
     */
	public function getItemData(taoItems_models_classes_QTI_Item $item){
		$itemData = $item->getData();
		
		//insert the interaction tags:
		foreach($item->getInteractions() as $interaction){
			//replace the interactions by a identified tag with the authoring elements
			$pattern = "/{{$interaction->getSerial()}}/";
			$itemData = preg_replace($pattern, $this->getInteractionTag($interaction), $itemData, 1);
		}
		
		return $itemData;
	}
	
	public function getInteractionTag(taoItems_models_classes_QTI_Interaction $interaction){
		$returnValue = '';
		// $returnValue .= "<input type='button' id='{$interaction->getSerial()}' class='qti_interaction_link' value='{$interaction->getType()} Interaction'/>";
		$returnValue .= "<input type='button' id='{$interaction->getSerial()}' class='qti_interaction_link' value='{$interaction->getType()} Interaction'/>";
		
		return $returnValue;
	}
	
	public function getInteractionData(taoItems_models_classes_QTI_Interaction $interaction){
		$data = $interaction->getdata();
		
		//depending on the type of interaciton, strip the choice identifier or transfor it to editable elt
		$interactionType = strtolower($interaction->getType());
		if($interactionType == 'gapmatch'){
			//transform group reference to clickable choice buttons
			foreach($interaction->getGroups() as $group){
				$pattern = "/{{$group->getSerial()}}/";
				// $data = preg_replace($pattern, $this->getGapmatchTag($interaction), $data, 1);
				break; //there should be only one..
			}
		}else{
			if($interactionType == 'match'){
				foreach($interaction->getGroups() as $group){
					$pattern = "/{{$group->getSerial()}}/";
					$data = preg_replace($pattern, '', $data, 1);
				}
			}else{
				foreach($interaction->getChoices() as $choice){
					$pattern = "/{{$choice->getSerial()}}/";
					$data = preg_replace($pattern, '', $data, 1);
				}
			}
		}
	}
	
	//return an ordered array of choices:
	public function getInteractionChoices(taoItems_models_classes_QTI_Interaction $interaction){
		
		$returnValue = array();
		
		if(!is_null($interaction)){
			
			$data = $interaction->getData();
			switch(strtolower($interaction->getType())){
				case 'choice':
				case 'order':{
					$choices = array();
					foreach($interaction->getChoices() as $choiceId => $choice){
						//get the order from the interaction data:
						$order = false;
						$order = strpos($data, '{'.$choiceId.'}');
						if($order === false){
							throw new Exception("the position of the choice {$choiceId} cannot be found in the interaction data");//need to save the choice in the data everytime
							// continue;
						}else{
							$choices[$order] = $choice;
						}
					}
					
					//sort the choices
					ksort($choices);
					foreach($choices as $choice){
						$returnValue[] = $choice;
					}
					
					break;
				}
				case 'match':
				case 'gapmatch':{
					//get groups and do the same for each group:
					$groups = array();//1 or 2 maximum
					foreach($interaction->getGroups() as $groupSerial => $group){
						//get the order from the interaction data:
						$order = false;
						$order = strpos($data, '{'.$groupSerial.'}');
						if($order === false){
							throw new Exception("the position of the group {$groupSerial} cannot be found in the interaction data");//need to save the choice in the data everytime
							// continue;
						}else{
							$groups[$order] = $group;
						}
					}
					
					//sort the groups
					ksort($groups);
					$i = 0;
					foreach($groups as $group){
						$returnValue[$i] = array();
						//get the choice for each group:
						$choices = $group->getChoices();
						foreach($choices as $choiceId){
							$returnValue[$i][] = $this->qtiService->getDataBySerial($choiceId, 'taoItems_models_classes_QTI_Choice');
						}
						$i++;
					}
					
					break;
				}
				default:{
					throw new Exception('unknown interaction type: '.$interaction->getType());
				}
					
			}
			
		}
		
		return $returnValue;
	}
	
	//get the choices of a
	private function getChoices(taoItems_models_classes_QTI_Data $dataObj, $ordered=true){
		//check type interaction or 
		if($dataObj instanceof taoItems_models_classes_QTI_Interaction || $dataObj instanceof taoItems_models_classes_QTI_Group){
			
		}
	}
	
	
	public function saveItemData(taoItems_models_classes_QTI_Item $item, $itemData){
		if(!is_null($item)){
			
			//clean the interactions' editing elements:
			foreach($item->getInteractions() as $interaction){
				//replace the interactions by a identified tag with the authoring elements
				$pattern0 = $this->getInteractionTag($interaction);
				// $pattern = "/{$pattern0}/";
				// $itemData = preg_replace($pattern, "{{$interaction->getSerial()}}", $itemData, 1);
				$count = 0;
				$itemData = str_replace($pattern0, "{{$interaction->getSerial()}}", $itemData, $count);
			}
			
			//item saved in session:
			$item->setData($itemData);
		}
	}
	
	/**
     * This method creates a new item object to be used as the data container of the qtiAuthoring tool
     *
     * @access public
     * @param  taoItems_models_classes_QTI_Item item
	 * @param  string interactionType
     * @return taoItems_models_classes_QTI_Interaction
     */
	public function addInteraction(taoItems_models_classes_QTI_Item $item, $interactionType){
		
		$returnValue = null;
		
		$authorizedInteractions = array(
			'choice',
			'match',
			'gap',
			'hottext',
			'graphicassociate',
			'graphicgapmatch'
		);
		
		$interactionType = strtolower($interactionType);
		
		if(!is_null($item) && in_array($interactionType, $authorizedInteractions)){
			//create interaction:
			$interaction = new taoItems_models_classes_QTI_Interaction($interactionType);
			
			//add to the item object:
			$item->addInteraction($interaction);
			// $item->setData($itemData);
			
			//insert the required group immediately:
			if($interactionType == 'match'){
				$group1 = new taoItems_models_classes_QTI_Group();
				$group2 = new taoItems_models_classes_QTI_Group();
				$interaction->addGroups($group1);
				$interaction->addGroups($group2);
				$interaction->setData('{'.$group1->getSerial().'}{'.$group2->getSerial().'}');
			}else if($interactionType == 'gapmatch'){
				$group1 = new taoItems_models_classes_QTI_Group();
				$interaction->addGroups($group1);
				$interaction->setData('{'.$group1->getSerial().'}');
			}
			
			$returnValue = $interaction;
		}
		
		return $returnValue;
	}
	
	
	public function addChoice(taoItems_models_classes_QTI_Interaction $interaction, $data='', $identifier=''){
		
		$returnValue = null;
		
		if(!is_null($interaction)){
			//create a new choice:
			//determine the type of choice automatically?
			$choice = new taoItems_models_classes_QTI_Choice(null);
			if(!empty($data)){
				$choice->setData($data);
			}
			
			//append the choice to the interaciton's choice list, both in the php object and in the data property:
			$interaction->addChoice($choice);
			$interactionType = strtolower($interaction->getType());
			if($interactionType == 'match' || $interactionType == 'gapmatch'){
				//insert into group:
			}else{
				// $interaction->getData();
				$interaction->setData($interaction->getData().'{'.$choice->getSerial().'}');
			}
			
			
			$returnValue = $choice;
		}
		
		return $returnValue;
	}
	
	public function editChoiceData(taoItems_models_classes_QTI_Choice $choice, $data=''){
		if(!is_null($choice)){
			$choice->setdata($data);
		}
	}
	
	public function deleteInteraction(taoItems_models_classes_QTI_Item $item, taoItems_models_classes_QTI_Interaction $interaction){
		//add specific method in the item class: deleteInteraction??
		$item->removeInteraction($interaction);
	}
	
	public function deleteChoice(taoItems_models_classes_QTI_Interaction $interaction, taoItems_models_classes_QTI_Choice $choice){
		//add specific method in the interaction class: deleteChoice??
		$interaction->removeChoice($choice);
	}
	
    /**
     * define the content of item to be inserted by default (to prevent null
     * after creation)
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @return core_kernel_classes_Resource
     */
    public function setDefaultItemContent( core_kernel_classes_Resource $item)
    {
        $returnValue = null;

        // section 127-0-1-1-c213658:12568a3be0b:-8000:0000000000001CE9 begin
		
		try{
			$itemContent = $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));//it is ok if it is null
			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			
			if($itemModel instanceof core_kernel_classes_Resource){
				$content = (string)$itemContent;
				if(trim($content) == ''){
					switch($itemModel->uriResource){
						
						case TAO_ITEM_MODEL_WATERPHENIX:
							$content = file_get_contents(TAO_ITEM_HAWAI_TPL_FILE);
							$content = str_replace('{ITEM_URI}', $item->uriResource, $content);
							
							$item = $this->bindProperties($item, array(
								TAO_ITEM_CONTENT_PROPERTY => $content
							));
							break;
						case TAO_ITEM_MODEL_CAMPUS:
							$content = file_get_contents(TAO_ITEM_CAMPUS_TPL_FILE);
							$content = str_replace('{ITEM_URI}', $item->uriResource, $content);
							
							$item = $this->bindProperties($item, array(
								TAO_ITEM_CONTENT_PROPERTY => $content
							));
							break;
					}
				}
			}
		}
		catch(Exception $e){}
		$returnValue = $item;
		
        // section 127-0-1-1-c213658:12568a3be0b:-8000:0000000000001CE9 end

        return $returnValue;
    }

    /**
     * Get the file linked to an item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string itemUri
     * @return string
     */
    public function getAuthoringFileUriByItem($itemUri)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-188be92e:12507f7441c:-8000:0000000000001B79 begin
		
		if(strlen($itemUri) > 0){
			$returnValue = TAO_ITEM_AUTHORING_BASE_URI.'/'.tao_helpers_Uri::encode($itemUri).'.xml';			
		}
        // section 127-0-1-1-188be92e:12507f7441c:-8000:0000000000001B79 end

        return (string) $returnValue;
    }

    /**
     * get the item uri linked to the given file
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return string
     */
    public function getAuthoringFileItemByUri($uri)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-188be92e:12507f7441c:-8000:0000000000001B7D begin
		if(strlen($uri) > 0){
			if(file_exists($uri)){
				$returnValue = tao_helpers_Uri::decode(
					str_replace(TAO_ITEM_AUTHORING_BASE_URI.'/', '',
						str_replace('.xml', '', $uri)
					)
				);
			}
		}
        // section 127-0-1-1-188be92e:12507f7441c:-8000:0000000000001B7D end

        return (string) $returnValue;
    }

    /**
     * Get the file linked to an item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string itemUri
     * @return string
     */
    public function getAuthoringFile($itemUri)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-34d7bcb9:1250bcb34b1:-8000:0000000000001B6E begin
		$uri = $this->getAuthoringFileUriByItem($itemUri);
		
		if(!file_exists($uri)){
			file_put_contents($uri, '<?xml version="1.0" encoding="utf-8" ?>');
		}
		$returnValue = $uri;
		
        // section 127-0-1-1-34d7bcb9:1250bcb34b1:-8000:0000000000001B6E end

        return (string) $returnValue;
    }

    /**
     * Service to get the temporary authoring file
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string itemUri
     * @param  boolean fallback
     * @return string
     */
    public function getTempAuthoringFile($itemUri, $fallback = false)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5249fce9:12694acf215:-8000:0000000000001E84 begin
		
		if(strlen($itemUri) > 0){
			$returnValue = TAO_ITEM_AUTHORING_BASE_URI.'/tmp_'.tao_helpers_Uri::encode($itemUri).'.xml';	
			if(!file_exists($returnValue)){
				if($fallback){	//fallback in case of error otheerwise create  the file
					$returnValue = $this->getAuthoringFile($itemUri);
				}
			}
		}
		
        // section 127-0-1-1-5249fce9:12694acf215:-8000:0000000000001E84 end

        return (string) $returnValue;
    }
	
	//deprecated...
	// public function setInteractionId(taoItems_models_classes_QTI_Interaction $interaction, $newId){
		// if(!is_null($interaction) && !empty($newId)){
			// try{
				// $interaction->setId($newId);
				// $interaction->setId($newId);
			// }catch(InvalidArgumentException $e){
				// var_dump($_SESSION);
				// throw new Exception('the given interaction id already exists');
			// }
		// }
	// }
		
	public function setChoiceId(taoItems_models_classes_QTI_Choice $choice, $newId){
		if(!is_null($choice) && !empty($newId)){
			try{
				$choice->setId($newId);
			}catch(InvalidArgumentException $e){
				throw new Exception('the given choice id already exists');
			}
		}
	}
	
	public function setOptions(taoItems_models_classes_QTI_Data $data, $newOptions=array()){
		
		if(!is_null($data) && !empty($newOptions)){
		
			$options = array();
		
			foreach($newOptions as $key=>$value){
				if(is_array($value)){
					if(count($value)==1 && isset($value[0])){
						$options[$key] = $value[0];
					}else if(count($value)>1){
						$options[$key] = array();
						foreach($value as $val){
							$options[$key][] = $val;
						}
					}
				}else{
					$options[$key] = $value;
				}
			}
			
			$data->setOptions($options);
		}
		
	}
	
	public function setData(taoItems_models_classes_QTI_Data $qtiObject, $data = ''){
		$qtiObject->setData($data);
	}
	
	public function setIdentifier(taoItems_models_classes_QTI_Data $qtiObject, $identifier = ''){
		$qtiObject->setIdentifier($identifier);
	}
	
	public function setInteractionData(taoItems_models_classes_QTI_Interaction $interaction, $data = '', $choiceOrder=array()){
		//append the choices id to the interaction data:
		switch(strtolower($interaction->getType())){
			case 'choice':{
				for($i=0; $i<count($choiceOrder); $i++){
					$data .= '{'.$choiceOrder[$i].'}';
				}
				$interaction->setData($data);
				break;
			}
			case 'match':
			case 'gapmatch':{
				//append directly yo the group(s):
				//note: there must be only one group for 'gapmatch' but two for 'match'
				foreach($choiceOrder as $groupSerial=>$groupChoiceOrder){
					$group = null;
					$group = $this->qtiService->getDataBySerial($groupSerial, 'taoItems_models_classes_QTI_Group');
					if(!is_null($group)){
						$choices = array();
						foreach($groupChoiceOrder as $order => $choiceSerial){
							$choices[] = $this->qtiService->getDataBySerial($choiceSerial, 'taoItems_models_classes_QTI_Choice');
						}
						$group->setChoices($choices);
					}
				}
				$interaction->setData($data);
				break;
			}
			default:{
				throw new Exception('unknown type of interaction');
			}
		}
		
	}
	
	public function setResponseProcessing(taoItems_models_classes_QTI_Item $item, $type){
		
		$returnValue = false;
		
		if(!is_null($item)){
			//create a responseProcessing object
			$responseTemplates = array(
				QTI_RESPONSE_TEMPLATE_MATCH_CORRECT,
				QTI_RESPONSE_TEMPLATE_MAP_RESPONSE,
				QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT
			);
			
			$responseProcessing = null;
			if(in_array($type, $responseTemplates)){
				//it is one of the available qti default templates:
				$responseProcessing = new taoItems_models_classes_QTI_response_Template($type);
			}else{
				//a custom rule:
				$responseProcessing = new taoItems_models_classes_QTI_response_CustomRule();
				//parse the rule and assign it to the processing object
			}
			
			if(!is_null($responseProcessing)){
				$item->setResponseProcessing($responseProcessing);
				$returnValue = true;
			}
		}
		
		return $returnValue;
	}
	
	public function getResponseProcessing(taoItems_models_classes_QTI_Item $item){
		
		$returnValue = null;
		
		if(!is_null($item)){
			$returnValue = $item->getResponseProcessing();
		}
		
		return $returnValue;
	}
} /* end of class taoItems_models_classes_QtiAuthoringService */

?>
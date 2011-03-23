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
	public function createNewItem($itemIdentifier='', $title=''){
		
		$returnValue = null;
		
		$returnValue = new taoItems_models_classes_QTI_Item($itemIdentifier, array());
		
		//add default responseProcessing:
		$this->setResponseProcessing($returnValue, 'template');
		
		$returnValue->setOption('title', empty($title)?'QTI item':$title);
		
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
		$itemData = self::getFilteredData($item);
		// print_r($itemData);
		
		//inserting white spaces to allow easy item selecting:
		$itemData = preg_replace('/}{/', '}&nbsp;{', $itemData);
		$itemData = preg_replace('/^{/', '&nbsp;{', $itemData);
		$itemData = preg_replace('/}$/', '}&nbsp;', $itemData);
		
		//strip the starting and ending <div> tag if exists:
		$itemData = preg_replace('/^<div>(.*)<\/div>$/ims', '\1', trim($itemData));
		
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
		if($interaction->isBlock()){
			$returnValue .= "<br/><div class='qti_interaction_block qti_interaction_box'>";
		}else{
			//inline:
			$returnValue .= "<div class='qti_interaction_inline qti_interaction_box'>";
		}
		$returnValue .= "<input id=\"{$interaction->getSerial()}\" class=\"qti_interaction_link\" value=\"{$interaction->getType()} Interaction\" type=\"button\"/>";
		$returnValue .= "</div>";
		
		return $returnValue;
	}
	
	//for hot text interaction only
	public function getChoiceTag(taoItems_models_classes_QTI_Choice $choice){
		
		$choiceData = trim(strip_tags($choice->getData()));
		$value = (!empty($choiceData))?$choiceData:'{empty}';
		
		$returnValue = '';
		$returnValue .= "<div class='qti_choice_box'>";
		$returnValue .= "<input type='button' id='{$choice->getSerial()}' class='qti_choice_link' value='{$value}'/>";
		$returnValue .= "<span />";
		$returnValue .= "</div>";
		
		return $returnValue;
	}
	
	public function getGroupTag(taoItems_models_classes_QTI_Group $group){
		$returnValue = '';
		$returnValue .= "<div class='qti_choice_box'>";
		$returnValue .= "<input type=\"button\" id=\"{$group->getSerial()}\" class=\"qti_choice_link\" value=\"{$group->getIdentifier()}\"/>";
		$returnValue .= "<span />";
		$returnValue .= "</div>";
		
		return $returnValue;
	}
	
	public function getInteractionData(taoItems_models_classes_QTI_Interaction $interaction){
		$data = self::getFilteredData($interaction);
		
		//depending on the type of interaciton, strip the choice identifier or transfor it to editable elt
		$interactionType = strtolower($interaction->getType());
		switch($interactionType){
			case 'gapmatch':{
				//replace the "gaps" by their "interaction buttons"
				foreach($interaction->getGroups() as $gap){
					$pattern = "/{{$gap->getSerial()}}/";
					$data = preg_replace($pattern, $this->getGroupTag($gap), $data, 1);
				}
				//replace the invisible "choices"
				foreach($interaction->getChoices() as $choice){
					$pattern = "/{{$choice->getSerial()}}/";
					$data = preg_replace($pattern, '', $data, 1);
				}
				$data = preg_replace("/^<div>(.*)?<\/div>$/ims", '\1', trim($data), 1);
				break;
			}
			case 'hottext':{
				foreach($interaction->getChoices() as $choice){
					$pattern = "/{{$choice->getSerial()}}/";
					$data = preg_replace($pattern, $this->getChoiceTag($choice), $data, 1);
				}
				break;
			}
			default:{
				foreach($interaction->getChoices() as $choice){
					$pattern = "/{{$choice->getSerial()}}/";
					$data = preg_replace($pattern, '', $data, 1);
				}
			}
		}
		$data = preg_replace('/^<(p|div)>(.*)<\/\1>$/im', '\2', trim(html_entity_decode($data)));
		
		return $data;
	}
	
	public function getInteractionGroups(taoItems_models_classes_QTI_Interaction $interaction){
		$returnValue = array();
		
		if(!is_null($interaction)){
			switch(strtolower($interaction->getType())){
				case 'match':
				case 'gapmatch':
				case 'graphicgapmatch':{
					$returnValue = $interaction->getGroups();
					break;
				}
				default:{
					throw new Exception('no group accessible');
				}
			}
		}
		
		return $returnValue;
		
	}
	
	//return an ordered array of choices:
	public function getInteractionChoices(taoItems_models_classes_QTI_Interaction $interaction){
		
		$returnValue = array();
		
		if(!is_null($interaction)){
			
			$data = $interaction->getData();
			switch(strtolower($interaction->getType())){
				case 'choice':
				case 'associate':
				case 'order':
				case 'inlinechoice':
				case 'gapmatch':
				case 'hotspot':
				case 'graphicorder':
				case 'graphicassociate':
				case 'graphicgapmatch':{
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
				case 'match':{
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
						$returnValue[$group->getSerial()] = array();
						//get the choice for each group:
						$choices = $group->getChoices();
						ksort($choices);
						foreach($choices as $choiceId){
							$returnValue[$group->getSerial()][] = $this->qtiService->getDataBySerial($choiceId, 'taoItems_models_classes_QTI_Choice');
						}
						$i++;
					}
					
					break;
				}
				case 'hottext':{
					//note: hot text interactions do not require ordered choices
					foreach($interaction->getChoices() as $choiceId => $choice){
						$returnValue[] = $choice;
					}
					break;
				}
				case 'textentry':
				case 'extendedtext':
				case 'selectpoint':
				case 'positionobject':
				case 'slider':
				case 'upload':
				case 'endattempt':{
					//no choices allowed
					break;
				}
				default:{
					throw new Exception('unknown type of interaction to select choices: '.$interaction->getType());
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
				$itemData = $this->filterData($interaction, $itemData);
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
			'order',
			'associate',
			'match',
			'gapmatch',
			'inlinechoice',
			'textentry',
			'extendedtext',
			'hottext',
			'hotspot',
			'graphicorder',
			'graphicassociate',
			'graphicgapmatch',
			'selectpoint',
			'positionobject',
			'upload',
			'slider',
			'endattempt'
		);
		
		if(!is_null($item) && in_array(strtolower($interactionType), $authorizedInteractions)){
			//create interaction:
			$interaction = new taoItems_models_classes_QTI_Interaction($interactionType);//keep the case here!
			
			//insert the required group immediately:
			switch(strtolower($interactionType)){
				case 'choice':{
					//init mandatory attibute values:
					$interaction->setOption('shuffle', false);
					$interaction->setOption('maxChoices', 1);
					break;
				}
				case 'associate':{
					//init mandatory attibute values:
					$interaction->setOption('shuffle', false);
					$interaction->setOption('maxAssociations', 1);
					break;
				}
				case 'order':
				case 'inlinechoice':{
					$interaction->setOption('shuffle', false);
					break;
				}
				case 'match':{
					//adding 2 groups for match interaction
					for($i=0; $i<2; $i++){
						$newGroup = new taoItems_models_classes_QTI_Group();
						$newGroup->setType('simpleMatchSet');
						$interaction->addGroup($newGroup);
						$interaction->setData($interaction->getData().'{'.$newGroup->getSerial().'}');
					}
					
					$interaction->setOption('shuffle', false);
					$interaction->setOption('maxAssociations', 1);
					break;
				}
				case 'gapmatch':{
					//note: 'groups' == 'gaps' in this case
					$interaction->setOption('shuffle', false);
					break;
				}
				case 'hottext':
				case 'hotspot':
				case 'selectpoint':
				case 'positionobject':{
					//init mandatory attibute values:
					$interaction->setOption('maxChoices', 1);
					break;
				}
				case 'graphicassociate':{
					$interaction->setOption('maxAssociations', 1);
					break;
				}
				case 'graphicorder':
				case 'graphicgapmatch':{
					//no default options required
					break;
				}
				case 'slider':{
					$interaction->setOption('lowerBound', 0);
					$interaction->setOption('upperBound', 10);//arbitray
					$interaction->setOption('stepLabel', false);
					$interaction->setOption('reverse', false);
					break;
				}
				case 'endattempt':{
					$interaction->setOption('title', __('end attempt now'));
					break;
				}
			}
			
			//add a response object, even though it is empty at the beginning:
			$this->createInteractionResponse($interaction, $item);
			
			//finally, add to the item object:
			$item->addInteraction($interaction);
			
			$returnValue = $interaction;
		}
		
		return $returnValue;
	}
	
	//TODO: place all optionnal and special parameters in the option array
	public function addChoice(taoItems_models_classes_QTI_Interaction $interaction, $data='', $identifier=null, taoItems_models_classes_QTI_Group $group=null, $interactionData = ''){
		
		$returnValue = null;
		
		if(!is_null($interaction)){
			//create a new choice:
			//determine the type of choice according to the type of the interaction:
			$choiceType = '';
			$matchMax = null;
			$interactionType = strtolower($interaction->getType());
			switch($interactionType){
				case 'choice':
				case 'order':{
					$choiceType = 'simpleChoice';//case sensitive! used to get the xml qti element tag + the choice form
					break;
				}
				case 'associate':
				case 'match':{
					$choiceType = 'simpleAssociableChoice';
					$matchMax = 0;
					break;
				}
				case 'gapmatch':{
					$choiceType = 'gapText';
					$matchMax = 0;
					break;
				}
				case 'inlinechoice':{
					$choiceType = 'inlineChoice';
					break;
				}
				case 'hottext':{
					$choiceType = 'hottext';
					break;
				}
				case 'hotspot':
				case 'graphicorder':{
					$choiceType = 'hotspotChoice';
					break;
				}
				case 'graphicassociate':{
					$choiceType = 'associableHotspot';
					$matchMax = 0;
					break;
				}
				case 'graphicgapmatch':{
					$choiceType = 'gapImg';
					$matchMax = 0;
					break;
				}
				default:{
					throw new Exception('invalid interaction type');
				}
			}
			
			$choice = new taoItems_models_classes_QTI_Choice($identifier);
			$choice->setType($choiceType);
			
			if(!empty($data)){
				$choice->setData($data);
			}
			if(!is_null($matchMax)){
				$choice->setOption('matchMax', $matchMax);
			}
			$interaction->addChoice($choice);
			$this->qtiService->saveDataToSession($choice);
			
			switch($interactionType){
				case 'match':{
					//insert into group: which group?
					if(is_null($group)){
						throw new Exception('the group cannot be null');
					}else{
						//append to the choice list:
						$group->addChoices(array($choice));//add 1 choice
						$group->setData($group->getData().'{'.$choice->getSerial().'}');
					}
					break;
				}
				case 'gapmatch':{
					foreach($interaction->getGroups() as $group){
						//append to the choice list:
						$group->addChoices(array($choice));//add 1 choice
						$this->qtiService->saveDataToSession($group);
					}
					
					$data = $interaction->getData();
					$matched = array();
					if(preg_match_all("/{choice_[a-z0-9]*}/im", $data, $matched) > 0){
						$lastMatch = $matched[0][count($matched[0]) - 1];
						$data = str_replace($lastMatch, $lastMatch . '{'.$choice->getSerial().'}', $data);
					}
					else{
						$data = '{'.$choice->getSerial().'}'.$data;//make sure the choice tag is placed first in the interaction data
					}
					$interaction->setData($data);
					break;
				}
				case 'graphicgapmatch':{
					foreach($interaction->getGroups() as $group){
						//append to the choice list:
						$group->addChoices(array($choice));//add 1 choice
						$this->qtiService->saveDataToSession($group);
					}
					
					$data = $interaction->getData();
					$data .= '{'.$choice->getSerial().'}';//choices are appended to the interaction data
					$interaction->setData($data);
					break;
				}
				case 'hottext':{
					//do replacement of the new hottext tag:
					$count = 0;
					if(!empty($interactionData)){
						$interactionData = str_replace("{qti_hottext_new}", "{{$choice->getSerial()}}", $interactionData, $count);
					}
					
					if($count){
						$this->setInteractionData($interaction, $interactionData);
					}else{
						//
						$interaction->setData($interaction->getData().'{'.$choice->getSerial().'}');
					}
					break;
				}
				default:{
					//append the choice to the interaciton's choice list, both in the php object and in the data property:
					$interaction->setData($interaction->getData().'{'.$choice->getSerial().'}');
				}
			}
			
			$this->qtiService->saveDataToSession($interaction);
			$returnValue = $choice;
		}
		
		return $returnValue;
	}
	
	public function addGroup(taoItems_models_classes_QTI_Interaction $interaction, $interactionData=''){
	
		$returnValue = null;
		
		if(!is_null($interaction)){
			
			$group = new taoItems_models_classes_QTI_Group();
			foreach($this->getInteractionChoices($interaction) as $choice){
				$group->addChoices(array($choice));
			}
			
			
			switch(strtolower($interaction->getType())){
				case 'gapmatch':{
					$group->setType('gap');
				
					$count = 0;
					if(!empty($interactionData)){
						$interactionData = str_replace("{qti_gap_new}", "{{$group->getSerial()}}", $interactionData, $count);
					}
					
					if($count){
						$this->setInteractionData($interaction, $interactionData);
					}else{
						throw new Exception('Cannot find the new gap location in the interaction data.');
						//reappend to the interaction data, the stripped choice data
						// $choicesData = '';
						// foreach($interaction->getChoices() as $choice){
							// $choicesData .= "{{$choice->getSerial()}}";
						// }
						// $interaction->setData($interaction->getData().'{'.$group->getSerial().'}'.$choicesData);
					}
					break;
				}
				case 'graphicgapmatch':{
					$group->setType('associableHotspot');
					$group->setOption('matchMax', 0);
					
					$data = $interaction->getData();
					$data = '{'.$group->getSerial().'}'.$data;//groups are prepended to the interaction data
					//TODO: better, place it between the last group and the first choice
					
					$interaction->setData($data);
					break;
				}
			}
			
			$interaction->addGroup($group);
			
			//saving group and interaction into session
			$this->qtiService->saveDataToSession($group);
			$this->qtiService->saveDataToSession($interaction);
			
			$returnValue = $group;
		}
		
		return $returnValue;
		
	}
	
	
	public function editChoiceData(taoItems_models_classes_QTI_Choice $choice, $data=''){
		if(!is_null($choice)){
			$choice->setdata($data);
		}
	}
	
	public function deleteInteraction(taoItems_models_classes_QTI_Item $item, taoItems_models_classes_QTI_Interaction $interaction){
		
		$item->removeInteraction($interaction);
		
		//count the number of remaining interactions:
		$interactions = $item->getInteractions();
		
		if(count($interactions) == 1){
			foreach($interactions as $anInteraction){
				$uniqueResponse = $this->getInteractionResponse($anInteraction);
				// set its response to "RESPONSE":
				if($uniqueResponse->getIdentifier() != 'RESPONSE'){ 
					$uniqueResponse->setIdentifier('RESPONSE');
					$anInteraction->setResponse($uniqueResponse);
				}
				break;
			}
		}
		
		
	}
	
	public function deleteChoice(taoItems_models_classes_QTI_Interaction $interaction, taoItems_models_classes_QTI_Choice $choice){
		
		$interaction->removeChoice($choice);
		
		//then simulate get+save response data to filter affected response variables
		$this->saveInteractionResponse($interaction, $this->getInteractionResponseData($interaction));
	}
	
	public function deleteGroup(taoItems_models_classes_QTI_Interaction $interaction, taoItems_models_classes_QTI_Group $group){
		
		$interaction->removeGroup($group);
		
		//then simulate get+save response data to filter affected response variables
		$this->saveInteractionResponse($interaction, $this->getInteractionResponseData($interaction));
	}
		
	public function setOptions(taoItems_models_classes_QTI_Data $qtiObject, $newOptions=array()){
		
		if(!is_null($qtiObject) && !empty($newOptions)){
		
			$options = array();
			
			foreach($newOptions as $key=>$value){
				if(is_array($value)){
					if(count($value)==1 && isset($value[0])){
					
						if($value[0] !== '') $options[$key] = $value[0];
						
					}else if(count($value)>1){
						$options[$key] = array();
						foreach($value as $val){
						
							if($val !== '') $options[$key][] = $val;
							
						}
					}
				}else{
					if($value !== '') $options[$key] = $value;
				}
			}
			
			$qtiObject->setOptions($options);
		}
		
	}
	
	public function editOptions(taoItems_models_classes_QTI_Data $qtiObject, $newOptions=array()){
		if(!is_null($qtiObject) && !empty($newOptions)){
			foreach($newOptions as $key=>$value){
				if(is_array($value)){
					if(count($value)==1 && isset($value[0])){
					
						if($value[0] !== '') $qtiObject->setOption($key, $value[0]);
						
					}else if(count($value)>1){
					
						$values = array();
						foreach($value as $val){
							if($val !== '') $values[] = $val;
						}
						$qtiObject->setOption($key, $values);
						
					}
				}else{
					if($value !== '') $qtiObject->setOption($key, $value);
				}
			}
		}
	}
	
	public function setData(taoItems_models_classes_QTI_Data $qtiObject, $data = ''){
		$qtiObject->setData($data);
	}
	
	public function setPrompt($interaction, $prompt=''){
		//filter required: strip begining and ending <p> and <div> tags:
		
		$interaction->setPrompt($prompt);
	}
	
	public function setIdentifier(taoItems_models_classes_QTI_Data $qtiObject, $identifier){
		
		$identifier = preg_replace("/[^a-zA-Z0-9_]{1}/", '', $identifier);
		$oldIdentifier = $qtiObject->getIdentifier();
		if($identifier == $oldIdentifier){
			return true;
		}
		try{
			$qtiObject->setIdentifier($identifier);
		}catch(InvalidArgumentException $e){
			throw new Exception("the choice identifier \"{$identifier}\" is already used");
			return false;
		}
		
		//note: taoItems_models_classes_QTI_Group identifier editable for a "gap" of a gapmatch interaction only
		if($qtiObject instanceof taoItems_models_classes_QTI_Choice || $qtiObject instanceof taoItems_models_classes_QTI_Group){
			
			//update all reference in the response!
			$interaction = $this->qtiService->getComposingData($qtiObject);
			$response = $interaction->getResponse();
			if(is_null($response)){
				throw new Exception('no response found!');
			}
			
			$correctResponses = $response->getCorrectResponses();
			foreach($correctResponses as $key=>$choiceConcat){
				$correctResponses[$key] = preg_replace("/\b{$oldIdentifier}\b/", $identifier, $choiceConcat);
			}
			
			$mappings = $response->getMapping();
			foreach($mappings as $mapping => $score){
				$count = 0;
				$newMapping = preg_replace("/\b{$oldIdentifier}\b/", $identifier, $mapping, -1, $count);
				if($count){
					unset($mappings[$mapping]);
					$mappings[$newMapping] = $score;
				}
			}
			
			foreach($interaction->getChoices() as $choice){
				$matchGroup = $choice->getOption('matchGroup');
				if(!empty($matchGroup)){
					if(is_string($matchGroup)){
						$matchGroup = array($matchGroup);
					}
					foreach($matchGroup as $key=>$choiceOrGroupIdentifier){
						if($choiceOrGroupIdentifier == $oldIdentifier){
							$matchGroup[$key] = $identifier; //replace by the new identifier
							$choice->setOption('matchGroup', $matchGroup);
							$interaction->addChoice($choice);//important: set the choice in the interaction again, to make the changes on the choice option effective.
							break;//the identifier can exist only once in the list
						}
					}
				}
			}
			
			$interaction = null;
			$response->setCorrectResponses($correctResponses);
			$response->setMapping($mappings);
			
			return true;
		}
		
		return false;
	}
	
	public function setInteractionData(taoItems_models_classes_QTI_Interaction $interaction, $data = '', $choiceOrder=array()){
		
		// $data = $this->convertToXHTML($data);
		
		//append the choices id to the interaction data:
		switch(strtolower($interaction->getType())){
			case 'choice':
			case 'order':
			case 'associate':
			case 'inlinechoice':
			case 'hotspot':
			case 'graphicorder':
			case 'graphicassociate':
			case 'graphicgapmatch':{
				if(!empty($choiceOrder)){
					for($i=0; $i<count($choiceOrder); $i++){
						$data .= '{'.$choiceOrder[$i].'}';
					}
					$interaction->setData($data);
				}
				break;
			}
			case 'match':{
				//append directly to the group(s):
				//note: there must be only one group for 'gapmatch' but two for 'match'
				if(!empty($choiceOrder)){
					//the old data must contain all groups:
					$oldData = $interaction->getData();
					$interactionSerial = $interaction->getSerial();
					foreach($choiceOrder as $groupSerial=>$groupChoiceOrder){
						//need for reappending the group to the data
						$data .= "{{$groupSerial}}";
					}
					$interaction->setData($data);
					
					foreach($choiceOrder as $groupSerial=>$groupChoiceOrder){
						if(strpos($oldData, "{{$groupSerial}}") !== false){
							$group = null;
							$group = $this->qtiService->getDataBySerial($groupSerial, 'taoItems_models_classes_QTI_Group');
							if(!is_null($group)){
								$groupData = '';
								$choices = array();
								foreach($groupChoiceOrder as $order => $choiceSerial){
									$choice = $this->qtiService->getDataBySerial($choiceSerial, 'taoItems_models_classes_QTI_Choice');
									$choices[] = $choice;
									
									$group->removeChoice($choice);//remove it from the old data
									$groupData .= "{{$choiceSerial}}";
								}
								//sort only the choices in the group(s)
								$group->setChoices($choices);
								$group->setData($groupData);
								$interaction->addGroup($group);//overwrite the old version of the group that has the same groupSerial
								
								//TODO: replace the block with: $this->setGroupData($group, $groupChoiceOrder, $interaction, false);
							}else{
								throw new Exception("the group with the serial $groupSerial does not exist in session");
							}
						}else{
							throw new Exception("the group with the serial $groupSerial cannot be found in the intial interaction group data");
						}
					}
				}
				break;
			}
			case 'gapmatch':{
				//for THE choice order, get all groups:
				//for each group, delete not assigned choice from the array, then save the remaining choices, which are on a correct order already:
				if(empty($choiceOrder)){
					//restore the choice order in case it has not changed
					$choiceOrder = array();
					$choices = $this->getInteractionChoices($interaction);
					for($i=0; $i<count($choices); $i++){
						$choiceOrder[] = $choices[$i]->getSerial();
					}
				}
				//save the choices in the interaction data:
				$choicesData = '';
				for($i=0; $i<count($choiceOrder); $i++){
					$choicesData .= '{'.$choiceOrder[$i].'}';
				}
				
				
				foreach($interaction->getGroups() as $group){
					$data = $this->filterData($group, $data);
				}
				$data = '<div>'.$data.'</div>';
				
				$interaction->setData($choicesData.$data);
				break;
			}
			case 'textentry':
			case 'extendedtext':
			case 'slider':
			case 'upload':
			case 'endattempt':
			case 'selectpoint':
			case 'positionobject':{
				//nothing to do related to choices
				break;
			}
			case 'hottext':{
				//note for hottext: the chocies of hottext are inline string elements, the order of which are naturally set in the interaction data
				//clean the choices link tags:
				foreach($interaction->getChoices() as $choice){
					$data = $this->filterData($choice, $data);
				}
				
				// $interaction->setData('<div>'.$data.'</div>');
				$interaction->setData($data);
				break;
			}
			default:{
				throw new Exception('unknown type of interaction');
			}
		}
		
	}
	
	protected function filterData(taoItems_models_classes_QTI_Data $qtiObject, $data){
	
		$pattern = '/';
		if($qtiObject instanceof taoItems_models_classes_QTI_Interaction){
			if($qtiObject->isBlock()){
				$pattern .= "(<br(.[^<]*)?>)\n?";
			}
		}
		$pattern .= "<div(.[^<]*)?>(.[^<>]*)?<input(.[^<>]*){1}{$qtiObject->getSerial()}(.[^<>]*){1}>(.[^<>]*)?(<span(.[^>]*)?><\/span>|<span(.[^>]*)?\/>)?(<\/div>){1}/im";
		
		$data = preg_replace($pattern, "{{$qtiObject->getSerial()}}", $data);
		return $data;
	}
	
	public function convertToXHTML($data){
		$html = '<div>' . $data . '</div>';
		$doc = new DOMDocument('1.0', 'UTF-8');
		$doc->encoding = 'UTF-8';
		$doc->loadHTML($html);
		$doc->encoding = 'UTF-8';
		
		$data = substr($doc->saveXML($doc->getElementsByTagName('div')->item(0)), 5, -6);
		
		return utf8_decode($data);
	}
	
	public function setGroupData(taoItems_models_classes_QTI_Group $group, $choiceOrder=array(), taoItems_models_classes_QTI_Interaction $interaction=null, $edit=false){
		$groupData = ''; //note: group data only contains choices
		$oldOrder = $group->getChoices();
		$newOrder = $choiceOrder;
		foreach($newOrder as $newOrderKey => $choiceSerial){
			
			if($edit){
				//in the edit mode, delete not assigned choice from the array
				if(!in_array($choiceSerial, $oldOrder)){
					unset($newOrder[$newOrderKey]);
					continue;
				}
			}
			
			$groupData .= "{{$choiceSerial}}";//necessary??
			
		}
		
		//save the new compared and cleaned ordered array:
		$group->setChoices($newOrder);
		$group->setData($groupData);
		if(!is_null($interaction)){
			//important: if the interaction has been created before, their is need for reassigning the group to it to overwrite the old values in the itneraciton property, overwise, the alod valus will be saved at the destruction of the interaction
			$interaction->addGroup($group);//important! 
		}
		
	}
	
	
	public function setResponseProcessing(taoItems_models_classes_QTI_Item $item, $type, $customRule=''){
		
		$returnValue = false;
		
		if(!is_null($item)){
			//create a responseProcessing object
			$responseProcessing = null;
			switch(strtolower($type)){
				case 'template':{
					//add a default outcome to work with the reponseProcessing
					if(count($item->getOutcomes()) == 0){
						$item->setOutcomes(array(
							new taoItems_models_classes_QTI_Outcome('SCORE', array('baseType' => 'integer', 'cardinality' => 'single'))
						));
					}
					$responseProcessing = new taoItems_models_classes_QTI_response_TemplatesDriven();
					break;
				}
				case 'custom':
				case 'customtemplate':{
					throw new Exception("unavailable response processing type {$type}");
					break;
				}
			}
			
			if(!is_null($responseProcessing)){
				$item->setResponseProcessing($responseProcessing);//TODO: destroy from the session the old response processing object?
				$returnValue = true;
			}
		}
		
		return $returnValue;
	}
	
	public function setResponseTemplate(taoItems_models_classes_QTI_Response $response, $templateUri){
		
		$returnValue = false;
		
		//check if it is one of the available templates:
		if(taoItems_models_classes_QTI_response_TemplatesDriven::isSupportedTemplate($templateUri)){
			$response->setHowMatch($templateUri);
			$returnValue = true;
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
	
	public function getInteractionResponse(taoItems_models_classes_QTI_Interaction $interaction){
		$response = $interaction->getResponse();
		
		if(is_null($response)){
			//create a new one here, with default data model, according to the type of interaction:
			$this->createInteractionResponse($interaction);
		}
		
		return $response;
	}
	
	public function createInteractionResponse(taoItems_models_classes_QTI_Interaction $interaction, taoItems_models_classes_QTI_Item $item = null){
		$returnValue = false;
		
		$identifier = null;
		if(!is_null($item)){
			if(count($item->getInteractions())==0){
				$identifier = 'RESPONSE';
			}
		}
		//var_dump(Session::getAttribute('qti_identifiers'));
		$response = new taoItems_models_classes_QTI_Response($identifier);
		//var_dump($response);
		
		//set the default response template:
		$this->setResponseTemplate($response, QTI_RESPONSE_TEMPLATE_MATCH_CORRECT);
		
		//set the response to the interaction
		$interaction->setResponse($response);
		
		
		//set the default base type and cardinality to the response:
		$returnValue = $this->updateInteractionResponseOptions($interaction);
		if(!$returnValue){
			throw new Exception('the interaction response cannot be updated upon creation');
		}
		
		return $returnValue;
	}
	
	public function getInteractionResponseColumnModel(taoItems_models_classes_QTI_Interaction $interaction){
		$returnValue = array();
		$interactionType = strtolower($interaction->getType());
		switch($interactionType){
			case 'choice':
			case 'hottext':
			case 'hotspot':{
				$choices = array(); 
				foreach($interaction->getChoices() as $choice){
					$choices[] = $choice->getIdentifier();//and not serial, since the identifier is the name that is significant for the user
				}
				
				$i = 1;
				$editType = 'fixed';
				$returnValue[] = array(
					'name' => 'choice'.$i,
					'label' => __('Choice').' '.$i,
					'edittype' => $editType,
					'values' => $choices
				);
				
				break;
			}
			case 'order':
			case 'graphicorder':{
				$choices = array(); 
				foreach($interaction->getChoices() as $choice){
					$choices[] = $choice->getIdentifier();//and not serial, since the identifier is the name that is significant to the user
				}
				$editType = 'select';
				for($i=1;$i<=count($choices);$i++){
					$returnValue[] = array(
						'name' => 'choice'.$i,
						'label' => $i,
						'edittype' => $editType,
						'values' => $choices
					);
				}
				break;
			}
			case 'associate':
			case 'graphicassociate':{
				$choices = array(); 
				foreach($interaction->getChoices() as $choice){
					$choices[] = $choice->getIdentifier();//and not serial, since the identifier is the name that is significant for the user
				}
				$editType = 'select';
				
				for($i=1;$i<=2;$i++){
					$returnValue[] = array(
						'name' => 'choice'.$i,
						'label' => __('Choice').' '.$i,
						'edittype' => $editType,
						'values' => $choices
					);
				}
				
				break;
			}
			case 'match':{
				//get groups...
				$groups = $this->getInteractionChoices($interaction);
				$editType = 'select';
				$i = 1;
				foreach($groups as $objChoices){
					$choices = array(); 
					foreach($objChoices as $objChoice){
						$choices[] = $objChoice->getIdentifier();
					}
					$returnValue[] = array(
						'name' => 'choice'.$i,
						'label' => __('Choice').' '.$i,
						'edittype' => $editType,
						'values' => $choices
					);
					$i++;
				}
				break;
			}
			case 'gapmatch':
			case 'graphicgapmatch':{
				$groups = array();//list of gaps
				foreach($interaction->getGroups() as $group){
					$groups[] = $group->getIdentifier();//and not serial, since the identifier is the name that is significant for the user
				}
				$returnValue[] = $this->getInteractionResponseColumn(1, 'select', $groups);
				
				$choices = array();//list of gapTexts
				foreach($interaction->getChoices() as $choice){
					$choices[] = $choice->getIdentifier();//and not serial, since the identifier is the name that is significant for the user
				}
				$returnValue[] = $this->getInteractionResponseColumn(2, 'select', $choices);
				
				break;
			}
			case 'inlinechoice':{
				$choices = array(); 
				foreach($interaction->getChoices() as $choice){
					$choices[] = $choice->getIdentifier();//and not serial, since the identifier is the name that is significant for the user
				}
				$returnValue[] = $this->getInteractionResponseColumn(1, 'select', $choices);
				break;
			}
			case 'textentry':
			case 'extendedtext':
			case 'slider':{
				//values = mapping then...
				// $i = 1;
				// $editType = 'text';
				// $returnValue[] = array(
					// 'name' => 'choice'.$i,
					// 'label' => __('Choice').' '.$i,
					// 'edittype' => $editType
				// );
				$returnValue[] = $this->getInteractionResponseColumn(1, 'text');
				break;
			}
			case 'selectpoint':
			case 'positionobject':{
				$selectOptions = array(
					'point' => 'point',
					'circle' => 'circle',
					'ellipse' => 'ellipse',
					'rect' => 'rect',
					'poly' => 'poly',
					'default' => 'default'
				);
				$returnValue[] = $this->getInteractionResponseColumn(1, 'select', $selectOptions, array('label'=>__('Shape'), 'name'=>'shape'));
				$returnValue[] = $this->getInteractionResponseColumn(2, 'text', null, array('label'=>__('Coordinates'), 'name'=>'coordinates'));
				break;
			}
			default:{
				throw new Exception("the response column model of the interaction type {$interaction->getType()} is not applicable.");
				//note: upload and endattempt interactions have no response content
			}
			
		}
		
		if($interactionType != 'order' && $interactionType != 'graphicorder'){//no mapping allowed for order interaction for the time being
			//check if the response processing is a match or a map type, or a custom one:
			//correct response (mandatory):
			$returnValue[] = array(
				'name' => 'correct',
				'label' => __('Correct Responses'),
				'edittype' => 'checkbox',
				'values' => array('yes', 'no')
			);
			
			$response = $interaction->getResponse();
			if(is_null($response)){
				throw new Exception("no response found for the interaction {$interaction->getIdentifier()}");
			}else{
				$responseProcessingType = $response->getHowMatch();
				if(!empty($responseProcessingType)){
					if($responseProcessingType == QTI_RESPONSE_TEMPLATE_MAP_RESPONSE || $responseProcessingType == QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT){
						//mapping:
						$returnValue[] = array(
							'name' => 'score',
							'label' => __('Score'),
							'edittype' => 'text'
						);
					}
				}
			}
			
		}
		return $returnValue;
	}
	
	private function getInteractionResponseColumn($index, $editType, $choices = array(), $options = array()){
	
		$returnValue = array();
		
		if(intval($index)>0 && !empty($editType)){
			
			$returnValue['edittype'] = $editType;
			
			$name = 'choice'.intval($index);
			if(isset($options['name']) && !empty($options['name'])){
				$name = $options['name'];
			}
			$returnValue['name'] = $name;
			
			$label = __('Choice').' '.intval($index);
			if(!empty($options)){
				if(isset($options['label'])){
					$label = $options['label'];
				}
			}
			$returnValue['label'] = $label;
			
			if(is_array($choices) && !empty($choices)){
				$returnValue['values'] = $choices;
			}
			
		}
		
		return $returnValue;
	}
	
	//is a template or custome, if a template, which one?
	public function getResponseProcessingType(taoItems_models_classes_QTI_response_ResponseProcessing $responseProcessing = null){
		$returnValue = '';
		
		if($responseProcessing instanceof taoItems_models_classes_QTI_response_TemplatesDriven){
			
			$returnValue = 'template';
			
		}else if($responseProcessing instanceof taoItems_models_classes_QTI_response_Custom){
		
			$returnValue = 'custom';
			
		}else if($responseProcessing instanceof taoItems_models_classes_QTI_response_Template){
		
			$returnValue = 'customTemplate';
			
		}else{
			throw new Exception('invalid type of response processing');
		}
		
		return $returnValue;
	}
	
	//@return mixed
	public function getInteractionChoiceByIdentifier(taoItems_models_classes_QTI_Interaction $interaction, $identifier){
		$interactionType = strtolower($interaction->getType());
		
		if(!is_null($interaction) && !empty($identifier)){
			foreach($interaction->getChoices() as $choice){
				if($choice->getIdentifier() == $identifier){
					return $choice;
				}
			}
			
			if($interactionType == 'gapmatch' || $interactionType == 'graphicgapmatch'){
				//search group too to find the "gaps"
				foreach($interaction->getGroups() as $group){
					if($group->getIdentifier() == $identifier){
						return $group;
					}
				}
			}
			//note: for other types of interaction, there is no need for searching within group as the chocies are attached to the interaction too
			
		}
		
		return null;
	}
	
	public function saveInteractionResponse(taoItems_models_classes_QTI_Interaction $interaction, $responseData){
		
		$returnValue = false;
		
		if(!is_null($interaction)){
		
			$interactionResponse = $this->getInteractionResponse($interaction);
			
			//sort the key, according to the type of interaction:
			$correctResponses = array();
			$mapping = array();
			$mappingType = '';
			
			switch(strtolower($interaction->getType())){
				case 'choice':
				case 'inlinechoice':
				case 'hottext':
				case 'extendedtext':
				case 'hotspot':
				case 'textentry':
				case 'slider':{
				
					foreach($responseData as $response){
						$response = (array)$response;
						//if required identifier not empty:
						if(!empty($response['choice1'])){
						
							$choice1 = trim($response['choice1']);
							if(!is_null($choice1)){
								
								$responseValue = $choice1;
								
								if($response['correct'] === 'yes' || $response['correct'] === true){
									$correctResponses[] = $responseValue;
								}
								if(!empty($response['score'])){
									//0 is considered as empty:
									$mapping[$responseValue] = $response['score'];//float
								}
								
							}
							
						}
					}
					break;
				}
				case 'associate':
				case 'match':
				case 'gapmatch':
				case 'graphicassociate':
				case 'graphicgapmatch':{
					
					foreach($responseData as $response){
						$response = (array)$response;
						if(!empty($response['choice1']) && !empty($response['choice2'])){
							
							$choice1 = trim($response['choice1']);
							$choice2 = trim($response['choice2']);
							if(!is_null($choice1) && !is_null($choice2)){
							
								$responseValue = $choice1.' '.$choice2;
								
								if($response['correct'] == 'yes' || $response['correct'] === true){
									$correctResponses[] = $responseValue;
								}
								if(!empty($response['score'])){
									//0 is considered as empty:
									$mapping[$responseValue] = $response['score'];
								}
								
							}
						}
					}
					
					break;
				}
				case 'order':
				case 'graphicorder':{
					foreach($responseData as $response){
						$response = (array)$response;
						
						//find the correct order:
						$tempResponseValue = array();
						
						foreach($response as $choicePosition => $choiceValue){
							//check if it is a choice:
							if(strpos($choicePosition, 'choice') === 0 ){
								//ok:
								$pos = intval(substr($choicePosition, 6));
								if($pos>0){
									
									$choice = trim($choiceValue);
									if(!empty($choice)){
										//starting from 1... so need (-1):
										$tempResponseValue[$pos-1] = $choice;
									}
									
								}
							}
						}
						
						//check if order has been breached, i.e. user forgot an intermediate value:
						if(!empty($tempResponseValue)){
							$responseValue = array();
							for($i=0; $i<count($tempResponseValue); $i++){
								if(isset($tempResponseValue[$i])){
									$responseValue[$i] = $tempResponseValue[$i];
								}else{
									break;
								}
							}
							$correctResponses = $responseValue;
							$interactionResponse->setCorrectResponses($correctResponses);
							return true;
						}
						
						//temporary trick to make it work until the mapping rule is done:
						/*
						$response['correct'] = 'yes';
						if($response['correct'] == 'yes'){
							//set response array directly:
							$correctResponses = $responseValue;
						}
						if(!empty($response['score'])){
							//partial order... not available yet
						}*/
					}
					break;
				}
				case 'selectpoint':
				case 'positionobject':{
					$mappingType = 'area';
					foreach($responseData as $response){
						$response = (array)$response;
						if(!empty($response['shape']) && !empty($response['coordinates'])){
							
							$shape = strtolower(trim($response['shape']));
							$coordinates = trim($response['coordinates']);
							if(!is_null($shape) && !is_null($coordinates)){
							
								if(($response['correct'] == 'yes' || $response['correct'] === true) && $shape == 'point'){
									$coords = explode(',', $coordinates);
									if(count($coords)>=2){
										$coords = array_map('trim', $coords);
										$correctResponses[] = $coords[0].' '.$coords[1];
									}
								}
								
								if(!empty($response['score'])){
									$mapping[] = array(
										'shape' => $shape,
										'coords' => $coordinates,
										'mappedValue' => $response['score']
									);
								}
								
							}
						}
					}
					break;
				}
				default:{
					throw new Exception('invalid interaction type for response saving');
				}
			}
			
			//set correct responses & mapping
			//note: do not check if empty or not to allow erasing the values
			$interactionResponse->setCorrectResponses($correctResponses);
			$interactionResponse->setMapping($mapping, $mappingType);//method: unsetMapping + unsetCorrectResponses?
			
			//set the required cardinality and basetype attributes:
			$this->updateInteractionResponseOptions($interaction);
			
			$returnValue = true;
		}
		return $returnValue;
	}
	
	public function updateInteractionResponseOptions(taoItems_models_classes_QTI_Interaction $interaction){
		
		$returnValue = false;
		
		if(!is_null($interaction)){
			$responseOptions = array(
				'cardinality' => $interaction->getCardinality(),
				'baseType' => $interaction->getBaseType()
			);
			$response = $interaction->getResponse();
			if(!is_null($response)){
				$this->editOptions($response, $responseOptions);
				$returnValue = true;
			}
			
		}
		
		return $returnValue;
	}
	
	//correct responses + mapping
	public function getInteractionResponseData(taoItems_models_classes_QTI_Interaction $interaction){
		$reponse = $this->getInteractionResponse($interaction);
		
		$returnValue = array();
		$correctResponses = $reponse->getCorrectResponses();
		$mapping = $reponse->getMapping();
		$maxChoices = $interaction->getCardinality(true);
		
		$i = 0;
		$interactionType = strtolower($interaction->getType());
		switch($interactionType){
			case 'order':
			case 'graphicorder':{
				if(!empty($correctResponses)){
			
					$returnValue[$i] = array();
					$returnValue[$i]['correct'] = 'yes';
					$j = 1;
					foreach($correctResponses as $choiceIdentifier){
						$choice = $this->getInteractionChoiceByIdentifier($interaction, $choiceIdentifier);
						if(is_null($choice)){
							break;//important: do not take into account deleted choice
						}
						$returnValue[$i]["choice{$j}"] = $choiceIdentifier;
						$j++;
					}
					
					//note: there could only be one correct response so $i should be 0
					//note 2: there is no possible direct score mapping against correct response order: as a consequence, only the response tlp match can work for the time being
				}
				
				break;
			}
			case 'textentry':
			case 'extendedtext':
			case 'slider':{
				if(!empty($correctResponses)){
					foreach($correctResponses as $response){
						
						$returnValue[$i] = array(
							'choice1' => $response,
							'correct' => 'yes'
						);
												
						if(isset($mapping[$response])){
							$returnValue[$i]['score'] = $mapping[$response];
							unset($mapping[$response]);
						}
						
						$i++;
						
						//delete exceeding correct responses (0 means infinite)
						if($maxChoices){
							if($i>=$maxChoices) break;
						}
					}
				}
				
				if(!empty($mapping)){
					foreach($mapping as $response => $score){
						
						$returnValue[$i] = array(
							'choice1' => $response,
							'correct' => 'no',
							'score' => $score
						);
						
						$i++;
					}
				}
				
				break;
			}
			case 'selectpoint':
			case 'positionobject':{
				
				if(!empty($correctResponses)){
					foreach($correctResponses as $response){
						
						$response = explode(' ', $response);
						$response = array_map('trim', $response);
						if(count($response)==2){
							
							$returnValue[$i] = array(
								'shape' => 'point',
								'coordinates' => $response[0].', '.$response[1],
								'correct' => 'yes'
							);
							
							$i++;
							
							//delete exceeding correct responses (0 means infinite)
							if($maxChoices){
								if($i>=$maxChoices) break;
							}
							
						}
					}
				}
				$areaMapping = $reponse->getMapping('area');
				if(!empty($areaMapping)){
					foreach($areaMapping as $mapping){
						
						$returnValue[$i] = array(
							'shape' => $mapping['shape'],
							'coordinates' => $mapping['coords'],
							'correct' => 'no',
							'score' => $mapping['mappedValue']
						);
						
						$i++;
					}
				}
			
				break;
			}
			default:{
			
				if(!empty($correctResponses)){
					foreach($correctResponses as $choiceIdentifierConcat){
						
						$choiceIdentifiers = explode(' ', $choiceIdentifierConcat);
						
						$returnValue[$i] = array();
						$returnValue[$i]['correct'] = 'yes';
						
						$j = 1;//j<=2
						//set data as not persistent
						foreach($choiceIdentifiers as $choiceIdentifier){
							
							$choice = $this->getInteractionChoiceByIdentifier($interaction, $choiceIdentifier);//no type check here: could be either a choice or a group
							if(is_null($choice)){
								break(2);//important: do not take into account deleted choice
							}
							$returnValue[$i]["choice{$j}"] = $choiceIdentifier;
							
							$j++;
						}
						
						if(isset($mapping[$choiceIdentifierConcat])){
							$returnValue[$i]['score'] = $mapping[$choiceIdentifierConcat];
							unset($mapping[$choiceIdentifierConcat]);
						}
						
						$i++;
						
						if($maxChoices){
							if($i>=$maxChoices) break;//delete exceeding correct responses
						}
					}
				}
				if(!empty($mapping)){
					foreach($mapping as $choiceIdentifierConcat => $score){
						$choiceIdentifiers = explode(' ', $choiceIdentifierConcat);
						
						$returnValue[$i] = array();
						$returnValue[$i]['correct'] = 'no';
						
						$j = 1;//j<=2
						foreach($choiceIdentifiers as $choiceIdentifier){
							$choice = $this->getInteractionChoiceByIdentifier($interaction, $choiceIdentifier);//no type check: could be either a choice or a group
							if(is_null($choice)){
								break(2);//important: do not take into account deleted choice
							}
							$returnValue[$i]["choice{$j}"] = $choiceIdentifier;
							
							//add exception for textEntry interaction where the values are the $choiceIdentifier:
							
							$j++;
						}
						
						$returnValue[$i]['score'] = $score;
						
						$i++;
					}
				}
				
			}
		}
		
		return $returnValue;
	}
	
	public function setMappingOptions(taoItems_models_classes_QTI_Response $response, $mappingOptions=array()){
		
		$returnValue = false;
		
		if(!is_null($response)){
			if(isset($mappingOptions['defaultValue'])){
				$response->setMappingDefaultValue($mappingOptions['defaultValue']);
			}
			
			$options = array();
			if(isset($mappingOptions['lowerBound'])){
				if(!empty($mappingOptions['lowerBound'])) $options['lowerBound'] = $mappingOptions['lowerBound'];
			}
			if(isset($mappingOptions['upperBound'])){
				if(!empty($mappingOptions['upperBound'])) $options['upperBound'] = $mappingOptions['upperBound'];
			}
			$response->setOption('mapping', $options);
			
			$returnValue = true;
		}
		
		return $returnValue;
	}
	
	/**
	 * Load and filter the data attribute of a QTI instance.
	 * The relative path to images are replace by the right url.
	 *  
	 * @param taoItems_models_classes_QTI_Data $qtiInstance
	 * @return string the data
	 */
	public static function getFilteredData(taoItems_models_classes_QTI_Data $qtiInstance){
		
		$returnValue = '';
		if(!is_null($qtiInstance)){
			$returnValue = self::filteredData($qtiInstance->getData());
		}
		return $returnValue;
	}
	
	/**
	 * Filter the data for the authoring needs
	 * @param string $data
	 * @return string
	 */
	public static function filteredData($data){
	
		$returnValue = '';
		
		$returnValue = $data;
		
		$data = trim($data);
		
		if(!empty($data)){
			
			$tidy = new tidy();
			$data = $tidy->repairString (
				$data,
				array(
					'output-xhtml' => true,
					'doctype' => 'transitional',
					'show-body-only' => false,
					'quote-nbsp' => false,
					'indent' => 'auto',
					'preserve-entities' => false,
					'quote-ampersand' => true,
					'uppercase-attributes' => false,
					'uppercase-tags' => false
				),
				'UTF8'
			);
			
			//remove the buggy and useless qti_validate tag in internal preview
			$data = preg_replace('/<a(.[^>]*)?id="qti_validate"(.[^>]*)?>(.[^<]*)?<\/a>/im', '', $data);
			
			try{//Parse data and replace img src by the media service URL
				$updated = false;
				$doc = new DOMDocument;
				if($doc->loadHTML($data)){
					
					$tags 		= array('img', 'object');
					$srcAttr 	= array('src', 'data');
					$xpath = new DOMXpath($doc);
					$query = implode(' | ', array_map(create_function('$a', "return '//'.\$a;"), $tags));
					foreach($xpath->query($query) as $element) {
						foreach($srcAttr as $attr){
							if($element->hasAttribute($attr)){
								$source = trim($element->getAttribute($attr));
								if(!preg_match("/^http/", $source)){
									$updated = true;
									$element->setAttribute($attr,  _url('getMediaResource', 'Items', 'taoItems',array('path' => urlencode($source))));
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
		
		
		return $returnValue;
	}
	
} /* end of class taoItems_models_classes_QtiAuthoringService */

?>
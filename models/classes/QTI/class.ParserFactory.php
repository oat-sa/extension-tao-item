<?php

error_reporting(E_ALL);

/**
 * The ParserFactory provides some methods to build the QTI_Data objects from an
 * element.
 * SimpleXML is used as source to build the model.
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
	die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E9-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E9-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E9-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E9-constants end

/**
 * The ParserFactory provides some methods to build the QTI_Data objects from an
 * element.
 * SimpleXML is used as source to build the model.
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_ParserFactory
{
	// --- ASSOCIATIONS ---


	// --- ATTRIBUTES ---

	// --- OPERATIONS ---

	/**
	 * Build a QTI_Item from a SimpleXMLElement (the root tag of this element is
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  SimpleXMLElement data
	 * @return taoItems_models_classes_QTI_Item
	 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10010
	 */
	public static function buildItem( SimpleXMLElement $data)
	{
		$returnValue = null;

		// section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000248E begin

		//check on the root tag.

		if(isset($data['identifier'])){
			$itemId = (string) $data['identifier'];//might be an issue if the identifier given is no good, e.g. twice the same value...
		}
		
		common_Logger::i('Started parsing of QTI item'.(isset($itemId) ? ' '.$itemId : ''), array('TAOITEMS'));
			
		//retrieve the item attributes
		$options = array();
		foreach($data->attributes() as $key => $value){
			$options[$key] = (string) $value;
		}
		unset($options['identifier']);

		//create the item instance
		$myItem = new taoItems_models_classes_QTI_Item($itemId, $options);

		//get the stylesheets
		$styleSheets = array();
		$styleSheetNodes = $data->xpath("*[name(.) = 'stylesheet']");
		foreach($styleSheetNodes as $styleSheetNode){
			$styleSheets[] = array(
       			'href' 	=> (string) $styleSheetNode['href'],		//mandaory field
       			'title' => (isset($styleSheetNode['title'])) ? (string) $styleSheetNode['title'] : '', 
       			'media'	=> (isset($styleSheetNode['media'])) ? (string) $styleSheetNode['media'] : 'screen',
       			'type'	=> (isset($styleSheetNode['type']))  ? (string) $styleSheetNode['type'] : 'text/css',
			);
		}
		$myItem->setStylesheets($styleSheets);
			
		//parse the xml to find the interaction nodes
		$interactionNodes = $data->xpath("//*[contains(name(.), 'Interaction')]");
		foreach($interactionNodes as $interactionNode){
			//build an interaction instance by found node
			$interaction = self::buildInteraction($interactionNode);
			if(!is_null($interaction)){
				$myItem->addInteraction($interaction);
			}
		}

		//extract the item structure to separate the structural/style content to the item content
		$itemBodyNodes = $data->xpath("*[name(.) = 'itemBody']/*");

		$itemData = '';
		foreach($itemBodyNodes as $itemBodyNode){	//the node should be alone
			$itemData .= $itemBodyNode->asXml();
		}
			
		if(!empty($itemData)){
			foreach($myItem->getInteractions() as $interaction){
				//map the interactions by an identified tag: {interaction.serial}
				$tag = $interaction->getType().'Interaction';
				$pattern = "/<{$tag}\b[^>]*>(.*?)<\/{$tag}>|(<{$tag}\b[^>]*\/>)/is";
				$itemData = preg_replace($pattern, "{{$interaction->getSerial()}}", $itemData, 1);
			}
			$myItem->setData($itemData);
		}

		//extract thee responses
		$responseNodes = $data->xpath("*[name(.) = 'responseDeclaration']");
		foreach($responseNodes as $responseNode){
			$response = self::buildResponse($responseNode);
			if(!is_null($response)){
				foreach($myItem->getInteractions() as $interaction){
					if($interaction->getOption('responseIdentifier') == $response->getIdentifier()){
						$interaction->setResponse($response);
						break;
					}
				}
			}
		}

		//extract outcome variables
		$outcomes = array();
		$outComeNodes = $data->xpath("*[name(.) = 'outcomeDeclaration']");
		foreach($outComeNodes as $outComeNode){
			$outcome = self::buildOutcome($outComeNode);
			if(!is_null($outcome)){
				$outcomes[] = $outcome;
			}
		}
		if(count($outcomes) > 0){
			$myItem->setOutcomes($outcomes);
		}

		//extract the response processing
		$rpNodes = $data->xpath("*[name(.) = 'responseProcessing']");
		foreach($rpNodes as $rpNode){		//the node should be alone
			$rProcessing = self::buildResponseProcessing($myItem->getInteractions(), $rpNode);
			if(!is_null($rProcessing)){
				$myItem->setResponseProcessing($rProcessing);
			}
		}

		$returnValue = $myItem;

		// section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000248E end

		return $returnValue;
	}

	/**
	 * Build a QTI_Interaction from a SimpleXMLElement (the root tag of this
	 * is an 'interaction' node)
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  SimpleXMLElement data
	 * @return taoItems_models_classes_QTI_Interaction
	 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
	 */
	public static function buildInteraction( SimpleXMLElement $data)
	{
		$returnValue = null;

		// section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002491 begin

		$options = array();
		foreach($data->attributes() as $key => $value){
			if($key == "matchGroup") {
				$options[$key] = explode(' ', $value);
			}
			else{
				$options[$key] = (string) $value;
			}
		}
		try{
			$type = str_replace('Interaction', '', $data->getName());
			$myInteraction = new taoItems_models_classes_QTI_Interaction($type, null, $options);

			//build the interaction regarding it's type
			switch($type){

				case 'match':
					//extract simpleMatchSet choices
					$matchSetNodes = $data->xpath("*[name(.) = 'simpleMatchSet']");
					foreach($matchSetNodes as $matchSetNode){
						$choiceNodes = $matchSetNode->xpath("*[name(.) = 'simpleAssociableChoice']");
						$choices = array();
						foreach($choiceNodes as $choiceNode){
							$choice = self::buildChoice($choiceNode);
							if(!is_null($choice)){
								$myInteraction->addChoice($choice);
								$choices[] = $choice;
							}
						}
						//and create group with the sets
						if(count($choices) > 0){
							$group = new taoItems_models_classes_QTI_Group();
							$group->setType($matchSetNode->getName());
							$group->setChoices($choices);
							$myInteraction->addGroup($group);
						}
					}
					break;

				case 'gapMatch':
					//create choices with the gapText nodes
					$choiceNodes = $data->xpath("*[name(.)='gapText']");
					$choices = array();
					foreach($choiceNodes as $choiceNode){
						$choice = self::buildChoice($choiceNode);
						if(!is_null($choice)){
							$myInteraction->addChoice($choice);
							$choices[$choice->getIdentifier()] = $choice;
						}
					}
					//create a group with each gap node (this a particular use of the group)
					$gapNodes = $data->xpath(".//*[name(.)='gap']");
					foreach($gapNodes as $gapNode){
						$group = new taoItems_models_classes_QTI_Group((string) $gapNode['identifier']);
						$group->setType($gapNode->getName());
						if(isset($gapNode['matchGroup'])){
							$matchChoice = array();
							$group->setOption('matchGroup', explode(' ', (string) $gapNode['matchGroup']));
							foreach($group->getOption('matchGroup') as $choiceId){
								if(array_key_exists($choiceId, $choices)){
									$matchChoice[] = $choices[$choiceId];
								}
							}
							$group->setChoices($matchChoice);
						}
						else{
							$group->setChoices($choices);
						}

						$myInteraction->addGroup($group);
					}
					break;
				case 'graphicGapMatch':
					//extract the media object tag
					$objectNodes = $data->xpath("*[name(.)='object']");
					foreach($objectNodes as $objectNode){
						$objectData = array();
						foreach($objectNode->attributes() as $key => $value){
							$objectData[$key] = (string) $value;
						}

						if(count($objectNode->children()) > 0){
							//get the node xml content
							$pattern = array("/^<{$objectNode->getName()}([^>]*)?>/i", "/<\/{$data->getName()}([^>]*)?>$/i");
							$content = preg_replace($pattern, "", trim($objectNode->asXML()));
							if(empty($content)){
								$content = (string) $objectNode;
							}
							$objectData['_alt'] = $content;
						}
						else{
							$objectData['_alt'] = (string) $objectNode;
						}
						$myInteraction->setObject($objectData);
					}

					//create choices with the gapImg nodes
					$choiceNodes = $data->xpath("*[name(.)='gapImg']");
					$choices = array();
					foreach($choiceNodes as $choiceNode){
						$choice = self::buildChoice($choiceNode);
						if(!is_null($choice)){
							$myInteraction->addChoice($choice);
							$choices[$choice->getIdentifier()] = $choice;
						}
					}
					//create a group with each gap node (this a particular use of the group)
					$gapNodes = $data->xpath(".//*[name(.)='associableHotspot']");
					foreach($gapNodes as $gapNode){
						$group = new taoItems_models_classes_QTI_Group((string) $gapNode['identifier']);
						$group->setType($gapNode->getName());
						if(isset($gapNode['matchGroup'])){
							$matchChoice = array();
							$group->setOption('matchGroup', explode(' ', (string) $gapNode['matchGroup']));
							foreach($group->getOption('matchGroup') as $choiceId){
								if(array_key_exists($choiceId, $choices)){
									$matchChoice[] = $choices[$choiceId];
								}
							}
							$group->setChoices($matchChoice);
						}
						else{
							$group->setChoices($choices);
						}
						if(isset($gapNode['matchMax'])){
							$group->setOption('matchMax', (int) $gapNode['matchMax']);
						}
						if(isset($gapNode['shape'])){
							$group->setOption('shape', (string) $gapNode['shape']);
						}
						if(isset($gapNode['coords'])){
							$group->setOption('coords', (string) $gapNode['coords']);
						}
						$myInteraction->addGroup($group);
					}
					break;
				case 'hotspot':
				case 'selectPoint':
				case 'graphicOrder':
				case 'graphicAssociate':
					//extract the media object tag
					$objectNodes = $data->xpath("*[name(.)='object']");
					foreach($objectNodes as $objectNode){
						$objectData = array();
						foreach($objectNode->attributes() as $key => $value){
							$objectData[$key] = (string) $value;
						}

						if(count($objectNode->children()) > 0){
							//get the node xml content
							$pattern = array("/^<{$objectNode->getName()}([^>]*)?>/i", "/<\/{$data->getName()}([^>]*)?>$/i");
							$content = preg_replace($pattern, "", trim($objectNode->asXML()));
							if(empty($content)){
								$content = (string) $objectNode;
							}
							$objectData['_alt'] = $content;
						}
						else{
							$objectData['_alt'] = (string) $objectNode;
						}
						$myInteraction->setObject($objectData);
					}
				default :
					//parse, extract and build the choice nodes contained in the interaction
					$interactionData = simplexml_load_string($data->asXML());
					$exp= "*[contains(name(.),'Choice')] | *[name(.)='associableHotspot'] | //*[name(.)='hottext']";
					$choiceNodes = $interactionData->xpath($exp);
					foreach($choiceNodes as $choiceNode){
						$choice = self::buildChoice($choiceNode);
						if(!is_null($choice)){
							$myInteraction->addChoice($choice);
						}
					}
					break;
			}

			//extract the interaction structure to separate the structural/style content to the interaction content
			$interactionNodes = $data->children();

			//get the interaction data
			$interactionData = '';
			foreach($interactionNodes as $interactionNode){
				$interactionData .= $interactionNode->asXml();
			}
			if(!empty($interactionData)){

				switch($type){

					case 'match':{
						foreach($myInteraction->getGroups() as $group){
							//map the group by a identified tag: {group-serial}
							$tag = $group->getType();
							$pattern = "/(<{$tag}\b[^>]*>(.*?)<\/{$tag}>)|(<{$tag}\b[^>]*\/>)/is";
							$interactionData = preg_replace($pattern, "{{$group->getSerial()}}", $interactionData, 1);
						}

						break;
					}
					case 'gapMatch':
					case 'graphicGapMatch':{
						foreach($myInteraction->getGroups() as $group){
							//map the group by a identified tag: {group-serial}
							$tag = $group->getType();
							$pattern = "/(<{$tag}\b[^>]*>(.*?)<\/{$tag}>)|(<{$tag}\b[^>]*\/>)/is";
							$interactionData = preg_replace($pattern, "{{$group->getSerial()}}", $interactionData, 1);
						}
					}
					case 'hotspot':
					case 'selectPoint':
					case 'graphicOrder':
					case 'graphicAssociate':{
							
						$pattern = "/(<object\b[^>]*>(.*?)<\/object>)|(<object\b[^>]*\/>)/is";
						$interactionData = preg_replace($pattern, "", $interactionData);
					}
					default:{
						foreach($myInteraction->getChoices() as $choice){
							//map the choices by a identified tag: {choice-serial}
							$tag = $choice->getType();
							$pattern = "/(<{$tag}\b[^>]*>(.*?)<\/{$tag}>)|(<{$tag}\b[^>]*\/>)/is";
							$interactionData = preg_replace($pattern, "{{$choice->getSerial()}}", $interactionData, 1);
						}
					}
				}

				//extract the prompt tag to the attribute
				$promptData = '';
				$promptNodes = $data->xpath("*[name(.) = 'prompt']");
				foreach($promptNodes as $promptNode){
					if(count($promptNode->children()) > 0){
						$promptData .= preg_replace(array("/^<prompt([^>]*)?>/i", "/<\/prompt([^>]*)?>$/i"), "", trim($promptNode->asXML()));
					}
					else{
						$promptData .= (string) $promptNode;
					}
				}
				$myInteraction->setPrompt($promptData);

				//remove the prompt from the data string
				$pattern = "/(<prompt\b[^>]*>(.*?)<\/prompt>)|(<prompt\b[^>]*\/>)/is";
				$interactionData = preg_replace($pattern, "", $interactionData);
					
				//set the data string
				$myInteraction->setData($interactionData);
			}

			$returnValue = $myInteraction;
		}
		catch(InvalidArgumentException $iae){
			throw new taoItems_models_classes_QTI_ParsingException($iae);
		}

		// section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002491 end

		return $returnValue;
	}

	/**
	 * Build a QTI_Choice from a SimpleXMLElement (the root tag of this element an 'choice' node)
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  SimpleXMLElement data
	 * @return taoItems_models_classes_QTI_Choice
	 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10254
	 */
	public static function buildChoice( SimpleXMLElement $data)
	{
		$returnValue = null;

		// section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002494 begin

		$options = array();
		foreach($data->attributes() as $key => $value){
			if($key == "matchGroup") {
				$options[$key] = explode(' ', $value);
			}
			else{
				$options[$key] = (string) $value;
			}
		}
		unset($options['identifier']);

		if(!isset($data['identifier'])){
			throw new taoItems_models_classes_QTI_ParsingException("No identifier found for the choice {$data->getName()}");
		}

		$myChoice = new taoItems_models_classes_QTI_Choice((string) $data['identifier'], $options);
		$myChoice->setType($data->getName());

		if($myChoice->getType() == 'gapImg'){

			//extract the media object tag
			$objectNodes = $data->xpath("*[name(.)='object']");
			foreach($objectNodes as $objectNode){
				$objectData = array();
				foreach($objectNode->attributes() as $key => $value){
					$objectData[$key] = (string) $value;
				}

				if(count($objectNode->children()) > 0){
					//get the node xml content
					$pattern = array("/^<{$objectNode->getName()}([^>]*)?>/i", "/<\/{$data->getName()}([^>]*)?>$/i");
					$content = preg_replace($pattern, "", trim($objectNode->asXML()));
					if(empty($content)){
						$content = (string) $objectNode;
					}
					$objectData['_alt'] = $content;
				}
				else{
					$objectData['_alt'] = (string) $objectNode;
				}
				$myChoice->setObject($objectData);
			}
		}
		if(count($data->children()) > 0){
			//get the node xml content
			$pattern = array("/^<{$data->getName()}([^>]*)?>/i", "/<\/{$data->getName()}([^>]*)?>$/i");
			$content = preg_replace($pattern, "", trim($data->asXML()));
			if(empty($content)){
				$content = (string) $data;
			}
			$myChoice->setData($content);
		}
		else{
			$myChoice->setData((string) $data);
		}

		$returnValue = $myChoice;

		// section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002494 end

		return $returnValue;
	}

	/**
	 * Short description of method buildResponse
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  SimpleXMLElement data
	 * @return taoItems_models_classes_QTI_Response
	 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10074
	 */
	public static function buildResponse( SimpleXMLElement $data)
	{
		$returnValue = null;

		// section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002497 begin

		$options = array();
		foreach($data->attributes() as $key => $value){
			$options[$key] = (string) $value;
		}
		unset($options['identifier']);

		if(!isset($data['identifier'])){
			throw new taoItems_models_classes_QTI_ParsingException("No identifier found for {$data->getName()}");
		}

		$myResponse = new taoItems_models_classes_QTI_Response((string) $data['identifier'], $options);
		$myResponse->setType($data->getName());

		//set the correct responses
		$correctResponseNodes = $data->xpath("*[name(.) = 'correctResponse']");
		$responses = array();
		foreach($correctResponseNodes as $correctResponseNode){
			foreach($correctResponseNode->value as $value){
				$responses[] = (string) $value;
			}
			break;
		}
		$myResponse->setCorrectResponses($responses);

		//set the mapping if defined
		$mappingNodes = $data->xpath("*[name(.) = 'mapping']");
		foreach($mappingNodes as $mappingNode){

			if(isset($mappingNode['defaultValue'])){
				$myResponse->setMappingDefaultValue(floatval((string) $mappingNode['defaultValue']));
			}
			$mappingOptions = array();
			foreach($mappingNode->attributes() as $key => $value){
				if($key != 'defaultValue'){
					$mappingOptions[$key] = (string) $value;
				}
			}
			//var_dump($mappingOptions);
			$myResponse->setOption('mapping', $mappingOptions);

			$mapping = array();
			foreach($mappingNode->mapEntry as $mapEntry){
				$mapping[(string) $mapEntry['mapKey']] = (string) $mapEntry['mappedValue'];
			}
			$myResponse->setMapping($mapping);

			break;
		}

		//set the areaMapping if defined
		$mappingNodes = $data->xpath("*[name(.) = 'areaMapping']");
		foreach($mappingNodes as $mappingNode){

			if(isset($mappingNode['defaultValue'])){
				$myResponse->setMappingDefaultValue(floatval((string) $mappingNode['defaultValue']));
			}
			$mappingOptions = array();
			foreach($mappingNode->attributes() as $key => $value){
				if($key != 'defaultValue'){
					$mappingOptions[$key] = (string) $value;
				}
			}
			$myResponse->setOption('areaMapping', $mappingOptions);

			$mapping = array();
			foreach($mappingNode->areaMapEntry as $mapEntry){
				$mappingAttributes = array();
				foreach($mapEntry->attributes() as $key => $value){
					$mappingAttributes[(string) $key] = (string) $value;
				}
				$mapping[] = $mappingAttributes;
			}
			$myResponse->setMapping($mapping, 'area');

			break;
		}

		$returnValue = $myResponse;

		// section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002497 end

		return $returnValue;
	}

	/**
	 * Short description of method buildOutcome
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  SimpleXMLElement data
	 * @return taoItems_models_classes_QTI_Outcome
	 */
	public static function buildOutcome( SimpleXMLElement $data)
	{
		$returnValue = null;

		// section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000249A begin

		$options = array();
		foreach($data->attributes() as $key => $value){
			$options[$key] = (string) $value;
		}
		unset($options['identifier']);

		if(!isset($data['identifier'])){
			throw new taoItems_models_classes_QTI_ParsingException("No identifier found for an {$data->getName()}");
		}

		$outCome = new taoItems_models_classes_QTI_Outcome((string) $data['identifier'], $options);
		if(isset($outcome->defaultValue)){
			$outCome->setDefaultValue((string) $outcome->defaultValue->value);
		}

		$returnValue = $outCome;

		// section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000249A end

		return $returnValue;
	}

	/**
	 * Short description of method buildResponseProcessing
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  mixed interactions
	 * @param  SimpleXMLElement data
	 * @return taoItems_models_classes_QTI_response_ResponseProcessing
	 */
	public static function buildResponseProcessing($interactions, SimpleXMLElement $data)
	{
		$returnValue = null;

		// section 127-0-1-1-74726297:12ae6749c02:-8000:0000000000002585 begin

		// RP based on TEMPLATE
		if(isset($data['template']))
		{
			$templateUri = (string) $data['template'];

			// TEMPLATE KNOWN by the system
			if (taoItems_models_classes_QTI_response_TemplatesDriven::isSupportedTemplate($templateUri)){
				// Set the how match attribute of the interactions' response
				foreach ($interactions as $interaction) {
					$interaction->getResponse()->setHowMatch($templateUri);
				}
				$returnValue = new taoItems_models_classes_QTI_response_TemplatesDriven($templateUri);
				//echo ($returnValue->buildQTI($templateUri, Array('responseIdentifier'=>'RESPONSE', 'outcomeIdentifier'=>'SCORE')).'<br/>');
			}

			// TEMPLATE UNKNOWN by the system
			else {
				$returnValue = new taoItems_models_classes_QTI_response_Template($templateUri);
			}

		}

		// RP based on CUSTOM RULE
		else {

			$responseConditionNodes = $data->xpath("*[name(.) = 'responseCondition']");

			// Identify patterns in the custom response processing
			$identifiedPatterns = self::identifyPattern($data);

			// TEMPLATES DRIVEN mode
			if ((count($interactions) * count($identifiedPatterns) * count($responseConditionNodes)) == pow(count($interactions), 3)) {
				// tag each response with the uri of the template used to match it
				foreach ($interactions as $interaction){
					foreach ($identifiedPatterns as $responseIdentifier=>$identifierPattern){
						if ($interaction->getResponse()->getIdentifier() == $responseIdentifier){
							$interaction->getResponse()->setHowMatch($identifierPattern);
						}
					}
				}
				$returnValue = new taoItems_models_classes_QTI_response_TemplatesDriven();
			}

			// CUSTOM CUSTOM
			else {
				$returnValue = self::buildCustomResponseProcessing($data);
			}
		}
		
		common_Logger::d('ResponseProcessing '.get_class($returnValue).' detected', array('TAOITEMS'));
		
		// section 127-0-1-1-74726297:12ae6749c02:-8000:0000000000002585 end

		return $returnValue;
	}

	/**
	 * Short description of method buildCustomResponseProcessing
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  SimpleXMLElement data
	 * @return taoItems_models_classes_QTI_response_ResponseProcessing
	 */
	public static function buildCustomResponseProcessing( SimpleXMLElement $data)
	{
		$returnValue = null;

		// section 127-0-1-1-21b9a9c1:12c0d84cd90:-8000:0000000000002A6D begin
		// Parse to find the different response rules
		$responseRules = array ();

		foreach ($data->children() as $child) {
			$responseRules[] = taoItems_models_classes_QTI_response_ResponseRuleFactory::buildResponseRule($child);
		}
		//@todocheck responseCustom 
		$returnValue = new taoItems_models_classes_QTI_response_Custom($responseRules);
		$returnValue->setData($data->asXml(), false);

		common_Logger::d('Build custom processing with the following rule: '.$returnValue->getRule());
		// section 127-0-1-1-21b9a9c1:12c0d84cd90:-8000:0000000000002A6D end

		return $returnValue;
	}

	/**
	 * Enables you to build the QTI_Resources from a manifest xml data node
	 * Content Packaging 1.1)
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  SimpleXMLElement source
	 * @return array
	 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_intgv2p0.html#section10003
	 */
	public static function getResourcesFromManifest( SimpleXMLElement $source)
	{
		$returnValue = array();

		// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026FB begin

		//check of the root tag
		if($source->getName() != 'manifest'){
			throw new Exception("incorrect manifest root tag");
		}
			
		$resourceNodes = $source->xpath("//*[name(.)='resource']");
		foreach($resourceNodes as $resourceNode){
			$type = (string) $resourceNode['type'];
			if(taoItems_models_classes_QTI_Resource::isAllowed($type)){
					
				$id = (string) $resourceNode['identifier'];
				(isset($resourceNode['href'])) ? $href = (string) $resourceNode['href'] : $href = '';
					
				$auxFiles = array();
				$xmlFiles = array();
				foreach($resourceNode->file as $fileNode){
					$fileHref = (string) $fileNode['href'];
					if(preg_match("/\.xml$/", $fileHref)){
						if(empty($href)){
							$xmlFiles[] = $fileHref;
						}
					}
					else{
						$auxFiles[] = $fileHref;
					}
				}
					
				if(count($xmlFiles) == 1 && empty($href)){
					$href = $xmlFiles[0];
				}
				$resource = new taoItems_models_classes_QTI_Resource($id, $href);
				$resource->setAuxiliaryFiles($auxFiles);
					
				$returnValue[] = $resource;
			}
		}

		// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026FB end

		return (array) $returnValue;
	}

	/**
	 * Short description of method buildConditionalExpression
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  SimpleXMLElement data
	 * @return taoItems_models_classes_QTI_response_ConditionalExpression
	 */
	public static function buildConditionalExpression( SimpleXMLElement $data)
	{
		$returnValue = null;

		// section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B31 begin

		// A conditional expression part consists of an expression which must have an effective baseType of boolean and single cardinality
		// It also contains a set of sub-rules. If the expression is true then the sub-rules are processed, otherwise they are
		// skipped (including if the expression is NULL) and the following responseElseIf or responseElse parts (if any) are considered instead.

		$returnValue = new taoItems_models_classes_QTI_response_ConditionalExpression();
		$actions = array();

		// The first subExpression has to be the condition (single cardinality and boolean type)
		list($conditionNode) = $data->xpath('*[1]');
		$condition = self::buildExpression($conditionNode);
		//echo '<pre>';print_r ($condition);echo '</pre>';
		$returnValue->setCondition($condition);

		// The rest of subExpression have to be computed if the condition is filled
		// These subExpression are responseRule (ResponseCondition, SetOutcomeValue, exitResponse). This code is yet writen, extract the function and avoid doublon
		$dataCount = count($data);
		for ($i=2; $i<=$dataCount; $i++) {
			list($actionNode) = $data->xpath('*['.$i.']');
			$actions[]= self::buildExpression($actionNode);
		}
		$returnValue->setActions($actions);

		// section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B31 end

		return $returnValue;
	}


	public static function buildExpression( SimpleXMLElement $data)
	{
		$returnValue = null;

		// section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B34 begin

		// The factory will create the right expression for us
		$expression = taoItems_models_classes_QTI_expression_ExpressionFactory::create($data);
		
		$returnValue = $expression;
		// section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B34 end

		return $returnValue;
	}

	/**
	 * Short description of method identifyPattern
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  SimpleXMLElement data
	 * @return array
	 */
	public static function identifyPattern( SimpleXMLElement $data)
	{
		$returnValue = array();

		// section 127-0-1-1-703c736:12c63695364:-8000:0000000000002C02 begin

		$data = simplexml_load_string($data->asxml());

		// MATCH CORRECT PATTERN
		$matchPatternMatchCorrectNodes = $data->xpath(
						"responseCondition
						[count(.) = 1]
						[name(./*[1]) = 'responseIf']
						[name(./responseIf/*[1]) = 'match']
						[name(./responseIf/match/*[1]) = 'variable']
						[name(./responseIf/match/*[2]) = 'correct']
						[name(./responseIf/*[2]) = 'setOutcomeValue']
						[name(./responseIf/setOutcomeValue/*[1]) = 'sum']
						[name(./responseIf/setOutcomeValue/sum/*[1]) = 'variable']
						[name(./responseIf/setOutcomeValue/sum/*[2]) = 'baseValue']"
		);

		foreach ($matchPatternMatchCorrectNodes as $node) {
			// Get the response identifier
			$subNode = $node->xpath('responseIf/match/variable');
			$responseIdentifier = (string) $subNode[0]['identifier'];
			$returnValue[$responseIdentifier] = 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/match_correct';
		}

		// MAP RESPONSE PATTERN
		$matchPatternMapResponseNodes = $data->xpath(
						"responseCondition
						[count(.) = 1]
						[name(./responseIf/*[1]) = 'not']
						[name(./responseIf/not/*[1]) = 'isNull']
						[name(./responseIf/not/isNull/*[1]) = 'variable']
						[name(./responseIf/*[2]) = 'setOutcomeValue']
						[name(./responseIf/setOutcomeValue/*[1]) = 'sum']
						[name(./responseIf/setOutcomeValue/sum/*[1]) = 'variable']
						[name(./responseIf/setOutcomeValue/sum/*[2]) = 'mapResponse']"
		);
		foreach ($matchPatternMapResponseNodes as $node) {
			// Get the response identifier
			$subNode = $node->xpath('responseIf/not/isNull/variable');
			$responseIdentifier = (string) $subNode[0]['identifier'];
			$returnValue[$responseIdentifier] = 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response';
		}

		// section 127-0-1-1-703c736:12c63695364:-8000:0000000000002C02 end

		return (array) $returnValue;
	}

} /* end of class taoItems_models_classes_QTI_ParserFactory */

?>
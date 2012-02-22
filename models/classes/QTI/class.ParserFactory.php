<?php

error_reporting(E_ALL);

/**
 * The ParserFactory provides some methods to build the QTI_Data objects from an
 * element.
 * SimpleXML is used as source to build the model.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_models_classes_QTI_response_ResponseRuleParserFactory
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/class.ResponseRuleParserFactory.php');

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
 * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
		if ($itemBodyNodes === false) {
			throw new taoItems_models_classes_QTI_ParsingException('Unable to read itemBody'.(isset($itemId) ? ' for item '.$itemId : ''));
		} 

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
		if (count($rpNodes) == 0) {
			common_Logger::i('No responseProcessing found for QTI Item, setting empty custom', array('QTI', 'TAOITEMS'));
			$customrp = new taoItems_models_classes_QTI_response_Custom(array(), '<responseProcessing/>');
			$myItem->setResponseProcessing($customrp);
		} else {
			$rpNode = array_shift($rpNodes);//the node should be alone
			$rProcessing = self::buildResponseProcessing($rpNode, $myItem);
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * Build a QTI_Choice from a SimpleXMLElement (the root tag of this element
     * an 'choice' node)
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * Short description of method buildTemplateResponseProcessing
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_response_ResponseProcessing
     */
    public static function buildTemplateResponseProcessing( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1-1eeee7a:134f5c3c208:-8000:00000000000035DB begin
   
		if(isset($data['template']) && count($data->children()) == 0) {
			$templateUri = (string) $data['template'];

			$returnValue = new taoItems_models_classes_QTI_response_Template($templateUri);

		} elseif (count($data->children()) == 1) {
			$patternCorrectIMS		= 'responseCondition [count(./*) = 2 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "match" ] [name(./responseIf/match/*[1]) = "variable" ] [name(./responseIf/match/*[2]) = "correct" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue" ] [name(./*[2]) = "responseElse" ] [count(./responseElse/*) = 1 ] [name(./responseElse/*[1]) = "setOutcomeValue" ] [name(./responseElse/setOutcomeValue/*[1]) = "baseValue"]';
			$patternMappingIMS		= 'responseCondition [count(./*) = 2] [name(./*[1]) = "responseIf"] [count(./responseIf/*) = 2] [name(./responseIf/*[1]) = "isNull"] [name(./responseIf/isNull/*[1]) = "variable"] [name(./responseIf/*[2]) = "setOutcomeValue"] [name(./responseIf/setOutcomeValue/*[1]) = "variable"] [name(./*[2]) = "responseElse"] [count(./responseElse/*) = 1] [name(./responseElse/*[1]) = "setOutcomeValue"] [name(./responseElse/setOutcomeValue/*[1]) = "mapResponse"]';
			$patternMappingPointIMS	= 'responseCondition [count(./*) = 2] [name(./*[1]) = "responseIf"] [count(./responseIf/*) = 2] [name(./responseIf/*[1]) = "isNull"] [name(./responseIf/isNull/*[1]) = "variable"] [name(./responseIf/*[2]) = "setOutcomeValue"] [name(./responseIf/setOutcomeValue/*[1]) = "variable"] [name(./*[2]) = "responseElse"] [count(./responseElse/*) = 1] [name(./responseElse/*[1]) = "setOutcomeValue"] [name(./responseElse/setOutcomeValue/*[1]) = "mapResponsePoint"]';
			if (count($data->xpath($patternCorrectIMS)) == 1) {
				$returnValue = new taoItems_models_classes_QTI_response_Template(taoItems_models_classes_QTI_response_Template::MATCH_CORRECT);
			} elseif (count($data->xpath($patternMappingIMS)) == 1) {
				$returnValue = new taoItems_models_classes_QTI_response_Template(taoItems_models_classes_QTI_response_Template::MAP_RESPONSE);
			} elseif (count($data->xpath($patternMappingPointIMS)) == 1) {
				$returnValue = new taoItems_models_classes_QTI_response_Template(taoItems_models_classes_QTI_response_Template::MAP_RESPONSE_POINT);
			} else {
				throw new taoItems_models_classes_QTI_UnexpectedResponseProcessingException('not Template, wrong rule');
			}
		} else {
			throw new taoItems_models_classes_QTI_UnexpectedResponseProcessingException('not Template');
		}
        // section 127-0-1-1-1eeee7a:134f5c3c208:-8000:00000000000035DB end

        return $returnValue;
    }

    /**
     * Short description of method buildResponseProcessing
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleXMLElement data
     * @param  Item item
     * @return taoItems_models_classes_QTI_response_ResponseProcessing
     */
    public static function buildResponseProcessing( SimpleXMLElement $data,  taoItems_models_classes_QTI_Item $item)
    {
        $returnValue = null;

        // section 127-0-1-1-74726297:12ae6749c02:-8000:0000000000002585 begin
        // try template
        try {
        	$returnValue = self::buildTemplateResponseProcessing($data);
	        try {
	    		$returnValue = taoItems_models_classes_QTI_response_TemplatesDriven::takeOverFrom($returnValue, $item);
	       		common_Logger::d('Processing is Template converted to TemplateDriven', array('TAOITEMS', 'QTI'));
	        } catch (taoItems_models_classes_QTI_response_TakeoverFailedException $e) {
	        	common_Logger::d('Processing is Template', array('TAOITEMS', 'QTI'));
	        }
        } catch (taoItems_models_classes_QTI_UnexpectedResponseProcessingException $e) {
		}
        
		//try templatedriven
        if (is_null($returnValue)) {
	        try {
	        	$returnValue = self::buildTemplatedrivenResponse($data, $item->getInteractions());
	        	common_Logger::d('Processing is TemplateDriven', array('TAOITEMS', 'QTI'));
	        } catch (taoItems_models_classes_QTI_UnexpectedResponseProcessingException $e) {
			}
        }
        
        //try composite
        if (is_null($returnValue)) {
	        try {
	        	$returnValue = self::buildCompositeResponseProcessing($data, $item);
	        	common_Logger::d('Processing is Composite', array('TAOITEMS', 'QTI'));
	        } catch (taoItems_models_classes_QTI_UnexpectedResponseProcessingException $e) {
	        	common_Logger::d($e->getMessage());
			}
        	
        }
        /*
        // convert template to composite
        if (!is_null($returnValue)) {
	        try {
	    		$returnValue = taoItems_models_classes_QTI_response_Composite::takeOverFrom($returnValue, $item);
	        } catch (Exception $e) {
	        	common_Logger::w('Could not be converted to Composite', array('TAOITEMS', 'QTI'));
			}
        }
        */
	    // build custom
        if (is_null($returnValue))
	        try {
	        	$returnValue = self::buildCustomResponseProcessing($data);
        		common_Logger::d('ResponseProcessing is custom');
	        } catch (taoItems_models_classes_QTI_UnexpectedResponseProcessingException $e) {
	        	// not a Template
	        	common_Logger::e('custom response processing failed, should never happen', array('TAOITEMS', 'QTI'));
	        }
	        
        if (is_null($returnValue)) {
        	common_Logger::d('failled to determin ResponseProcessing');
        }
	   
        // section 127-0-1-1-74726297:12ae6749c02:-8000:0000000000002585 end

        return $returnValue;
    }

    /**
     * Short description of method buildCompositeResponseProcessing
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleXMLElement data
     * @param  array responses
     * @return taoItems_models_classes_QTI_response_ResponseProcessing
     */
    public static function buildCompositeResponseProcessing( SimpleXMLElement $data, $item)
    {
        $returnValue = null;

        // section 127-0-1-1-1eeee7a:134f5c3c208:-8000:00000000000035D7 begin
        // STRONGLY simplified summation detection
        $patternCorrectTAO	= '/responseCondition [count(./*) = 1 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "match" ] [name(./responseIf/match/*[1]) = "variable" ] [name(./responseIf/match/*[2]) = "correct" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [count(./responseIf/setOutcomeValue/*) = 1 ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue"]';        
        $patternMapTAO		= '/responseCondition [count(./*) = 1 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "not" ] [count(./responseIf/not/*) = 1 ] [name(./responseIf/not/*[1]) = "isNull" ] [count(./responseIf/not/isNull/*) = 1 ] [name(./responseIf/not/isNull/*[1]) = "variable" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [count(./responseIf/setOutcomeValue/*) = 1 ] [name(./responseIf/setOutcomeValue/*[1]) = "mapResponse"]';       
        $patternMapPointTAO	= '/responseCondition [count(./*) = 1 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "not" ] [count(./responseIf/not/*) = 1 ] [name(./responseIf/not/*[1]) = "isNull" ] [count(./responseIf/not/isNull/*) = 1 ] [name(./responseIf/not/isNull/*[1]) = "variable" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [count(./responseIf/setOutcomeValue/*) = 1 ] [name(./responseIf/setOutcomeValue/*[1]) = "mapResponsePoint"]';       
        $patternNoneTAO		= '/responseCondition [count(./*) = 1 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "isNull" ] [count(./responseIf/isNull/*) = 1 ] [name(./responseIf/isNull/*[1]) = "variable" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [count(./responseIf/setOutcomeValue/*) = 1 ] [name(./responseIf/setOutcomeValue/*[1]) = "baseValue"]';       
        $possibleSummation	= '/setOutcomeValue [count(./*) = 1 ] [name(./*[1]) = "sum" ]';
        
		$irps = array();
		$composition = null;
		foreach ($data as $responseRule) {
			if (!is_null($composition))
				throw new taoItems_models_classes_QTI_UnexpectedResponseProcessingException('Not composite, rules after composition');
			
			$subtree = new SimpleXMLElement($responseRule->asXML());

			if (count($subtree->xpath($patternCorrectTAO)) > 0 ) {
				$responseIdentifier = (string) $subtree->responseIf->match->variable[0]['identifier'];
				$irps[$responseIdentifier] = array(
					'class'		=> 'MatchCorrectTemplate',
					'outcome'	=> (string) $subtree->responseIf->setOutcomeValue[0]['identifier']
				);
			} elseif (count($subtree->xpath($patternMapTAO)) > 0 ) {
				$responseIdentifier = (string) $subtree->responseIf->not->isNull->variable[0]['identifier'];
				$irps[$responseIdentifier] = array(
					'class'		=> 'MapResponseTemplate',
					'outcome'	=> (string) $subtree->responseIf->setOutcomeValue[0]['identifier']
				);
			} elseif (count($subtree->xpath($patternMapPointTAO)) > 0 ) {
				$responseIdentifier = (string) $subtree->responseIf->not->isNull->variable[0]['identifier'];
				$irps[$responseIdentifier] = array(
					'class'		=> 'MapResponsePointTemplate',
					'outcome'	=> (string) $subtree->responseIf->setOutcomeValue[0]['identifier']
				);
			} elseif (count($subtree->xpath($patternNoneTAO)) > 0 ) {
				$responseIdentifier = (string) $subtree->responseIf->isNull->variable[0]['identifier'];
				$irps[$responseIdentifier] = array(
					'class'		=> 'None',
					'outcome'	=> (string) $subtree->responseIf->setOutcomeValue[0]['identifier']
				);
			} elseif (count($subtree->xpath($possibleSummation)) > 0 ) {
				$composition = 'Summation';
				$outcomesUsed = array();
				foreach ($subtree->xpath('/setOutcomeValue/sum/variable') as $var) {
					$outcomesUsed[] = (string) $var[0]['identifier'];
				}
			} else {
				throw new taoItems_models_classes_QTI_UnexpectedResponseProcessingException('Not composite, unknown rule');
			}
		}
		
		if (is_null($composition)) {
			throw new taoItems_models_classes_QTI_UnexpectedResponseProcessingException('Not composit, Composition rule missing');
		}
		
		$responses = array();
		foreach ($item->getInteractions() as $interaction) {
			$responses[$interaction->getResponse()->getIdentifier()] = $interaction->getResponse();
		}
		
		if (count(array_diff(array_keys($irps), array_keys($responses))) > 0) {
			throw new taoItems_models_classes_QTI_UnexpectedResponseProcessingException('Not composit, no responses for rules: '.implode(',',array_diff(array_keys($irps), array_keys($responses))));
		}
		if (count(array_diff(array_keys($responses), array_keys($irps))) > 0) {
			common_Logger::w('Some responses have no processing');
			throw new taoItems_models_classes_QTI_UnexpectedResponseProcessingException('Not composit, no support for unmatched variables yet');
		}
		
		//assuming sum is correct
		
		$compositonRP = new taoItems_models_classes_QTI_response_Summation($item);
        foreach ($responses as $id => $response) {
        	$outcome = null;
        	foreach ($item->getOutcomes() as $possibleOutcome) {
        		if ($possibleOutcome->getIdentifier() == $irps[$id]['outcome']) {
        				$outcome = $possibleOutcome;
        				break;
        		}
        	}
        	if (is_null($outcome)) {
        		$compositonRP->destroy();
				throw new taoItems_models_classes_QTI_ParsingException('Undeclared Outcome in ResponseProcessing');
        	}
        	$classname = 'taoItems_models_classes_QTI_response_interactionResponseProcessing_'.$irps[$id]['class'];
        	$compositonRP->add(new $classname($response, $outcome));
        }
		$returnValue = $compositonRP;
        
        // section 127-0-1-1-1eeee7a:134f5c3c208:-8000:00000000000035D7 end

        return $returnValue;
    }

    /**
     * Short description of method identifyPattern
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleXMLElement data
     * @return array
     */
    public static function identifyPattern( SimpleXMLElement $data)
    {
        $returnValue = array();

        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002C02 begin
		common_logger::d('Identifying patterns');
		// foreach rule

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

    /**
     * Short description of method buildCustomResponseProcessing
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
			$responseRules[] = taoItems_models_classes_QTI_response_ResponseRuleParserFactory::buildResponseRule($child);
		}
		//@todocheck responseCustom 
		$returnValue = new taoItems_models_classes_QTI_response_Custom($responseRules, $data->asXml());
        // section 127-0-1-1-21b9a9c1:12c0d84cd90:-8000:0000000000002A6D end

        return $returnValue;
    }

    /**
     * Enables you to build the QTI_Resources from a manifest xml data node
     * Content Packaging 1.1)
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * Short description of method buildExpression
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_response_Rule
     */
    public static function buildExpression( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B34 begin

		// The factory will create the right expression for us
		$expression = taoItems_models_classes_QTI_expression_ExpressionParserFactory::build($data);
		
		$returnValue = $expression;
        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B34 end

        return $returnValue;
    }

    /**
     * Short description of method buildTemplatedrivenResponse
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleXMLElement data
     * @param  array interactions
     * @return taoItems_models_classes_QTI_response_ResponseProcessing
     */
    public static function buildTemplatedrivenResponse( SimpleXMLElement $data, $interactions)
    {
        $returnValue = null;

        // section 127-0-1-1-1eeee7a:134f5c3c208:-8000:00000000000035DF begin
		$patternCorrectTAO = '/responseCondition [count(./*) = 1 ] [name(./*[1]) = "responseIf" ] [count(./responseIf/*) = 2 ] [name(./responseIf/*[1]) = "match" ] [name(./responseIf/match/*[1]) = "variable" ] [name(./responseIf/match/*[2]) = "correct" ] [name(./responseIf/*[2]) = "setOutcomeValue" ] [name(./responseIf/setOutcomeValue/*[1]) = "sum" ] [name(./responseIf/setOutcomeValue/sum/*[1]) = "variable" ] [name(./responseIf/setOutcomeValue/sum/*[2]) = "baseValue"]';
		$patternMappingTAO = '/responseCondition [count(./*) = 1] [name(./*[1]) = "responseIf"] [count(./responseIf/*) = 2] [name(./responseIf/*[1]) = "not"] [name(./responseIf/not/*[1]) = "isNull"] [name(./responseIf/not/isNull/*[1]) = "variable"] [name(./responseIf/*[2]) = "setOutcomeValue"] [name(./responseIf/setOutcomeValue/*[1]) = "sum"] [name(./responseIf/setOutcomeValue/sum/*[1]) = "variable"] [name(./responseIf/setOutcomeValue/sum/*[2]) = "mapResponse"]';
        
		$rules = array();
		foreach ($data as $responseRule) {
			$subtree = new SimpleXMLElement($responseRule->asXML());

			if (count($subtree->xpath($patternCorrectTAO)) > 0 ) {
				$variable = $subtree->responseIf->match->variable;
				$responseIdentifier = (string) $variable[0]['identifier'];
				$rules[$responseIdentifier] = QTI_RESPONSE_TEMPLATE_MATCH_CORRECT;
			} elseif (count($subtree->xpath($patternMappingTAO)) > 0 ) {
				$variable = $subtree->responseIf->not->isNull->variable;
				$responseIdentifier = (string) $variable[0]['identifier'];
				$rules[$responseIdentifier] = QTI_RESPONSE_TEMPLATE_MAP_RESPONSE;
			} else {
				throw new taoItems_models_classes_QTI_UnexpectedResponseProcessingException('Not template driven, unknown rule');
			}
		}
		
		$responseIdentifiers = array();
		foreach ($interactions as $interaction) {
			$responseIdentifiers[] = $interaction->getResponse()->getIdentifier();
		}
		
		if (count(array_diff($responseIdentifiers, array_keys($rules))) > 0
			|| count(array_diff(array_keys($rules), $responseIdentifiers)) > 0) {
			throw new taoItems_models_classes_QTI_UnexpectedResponseProcessingException('Not template driven, responseIdentifiers are '.implode(',',$responseIdentifiers).' while rules are '.implode(',',array_keys($rules)));
		}
		
        $templatesDrivenRP = new taoItems_models_classes_QTI_response_TemplatesDriven();
        foreach ($interactions as $interaction){
			$pattern = $rules[$interaction->getResponse()->getIdentifier()];
			$templatesDrivenRP->setTemplate($interaction->getResponse(), $pattern);
		}
		$returnValue = $templatesDrivenRP;
        // section 127-0-1-1-1eeee7a:134f5c3c208:-8000:00000000000035DF end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_ParserFactory */

?>
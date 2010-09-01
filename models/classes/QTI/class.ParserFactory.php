<?php

error_reporting(E_ALL);

/**
 * The ParserFactory provides some methods to build the QTI_Data objects from an
 * element.
 * SimpleXML is used as source to build the model.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_Item
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10010
     */
    public static function buildItem( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000248E begin
        
        //check on the root tag
	    if($data->getName() != 'assessmentItem'){
	       	throw new taoItems_models_classes_QTI_ParsingException("incorrect item root tag");
	    }
	       
	    //get the item id
	    $itemId = null;
       	if(isset($data['identifier'])){
			$itemId = (string)$data['identifier'];//might be an issue if the identifier given is no good, e.g. twice the same value...
       	}
       
       	//retrieve the item attributes
       	$options = array();
       	foreach($data->attributes() as $key => $value){
       		$options[$key] = (string)$value;
       	}
       	unset($options['identifier']);
       	
       	//create the item instance
       	$myItem = new taoItems_models_classes_QTI_Item($itemId, $options);
       
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
        $itemBodyNodes = $data->xpath("//*[name(.) = 'itemBody']/*");
        
        $itemData = '';
        foreach($itemBodyNodes as $itemBodyNode){
        	$itemData .= $itemBodyNode->asXml();
        }
        if(!empty($itemData)){
	        foreach($myItem->getInteractions() as $interation){
	        	//map the interactions by a identified tag: {interaction-id} 
	        	$tag = $interation->getType().'Interaction';
	        	$pattern = "/<{$tag}\b[^>]*>(.*?)<\/{$tag}>/is";
	        	$itemData = preg_replace($pattern, "{{$interaction->getId()}}", $itemData, 1);
	        }
	        $myItem->setData($itemData);
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
       		$options[$key] = (string)$value;
       	}
       	try{
       		$type = str_replace('Interaction', '', $data->getName());
       		$myInteraction = new taoItems_models_classes_QTI_Interaction($type, null, $options);
       	
       		switch($type){
       			case 'match':
       			case 'graphicassociate':
       			case 'graphicgapmatch':
       			default :
       				$choiceNodes = $data->xpath("//*[ (contains(name(.), 'Choice')) or (name(.) = 'hottext') or (name(.) = 'gapText')]");
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
	        
	        $interactionData = '';
	        foreach($interactionNodes as $interactionNode){
	        	$interactionData .= $interactionNode->asXml();
	        }
	        if(!empty($interactionData)){
				
	        	foreach($myInteraction->getChoices() as $choice){
		        	//map the interactions by a identified tag: {interaction-id}
		        	if($type ==  'gapMatch'){
		        		$tag = 'gap';
		        	}
		        	else{
		        		$tag = $choice->getName();
		        	}
		        	$pattern = "/(<{$tag}\b[^>]*>(.*?)<\/{$tag}>)|(<{$tag}\b[^>]*\/>)/is";
		        	$interactionData = preg_replace($pattern, "{{$choice->getId()}}", $interactionData, 1);
		        }
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
       		$options[$key] = (string)$value;
       	}
       	unset($options['identifier']);
       	
       	if(!isset($data['identifier'])){
			throw new taoItems_models_classes_QTI_ParsingException("No identifier found for the choice {$data->getName()}");
       	}
       	
       	$myChoice = new taoItems_models_classes_QTI_Choice($data['identifier'], $options);
       	$myChoice->setName($data->getName());
       	$myChoice->setData((string)$data);
       	$myChoice->setValue((string)$data['identifier']);
       	
       	$returnValue = $myChoice;
        
        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002494 end

        return $returnValue;
    }

    /**
     * Short description of method buildResponse
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_Response
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10074
     */
    public static function buildResponse( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002497 begin
        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002497 end

        return $returnValue;
    }

    /**
     * Short description of method buildScore
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_Score
     */
    public static function buildScore( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000249A begin
        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000249A end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_ParserFactory */

?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - taoItems/models/classes/QTI/class.Factory.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 04.08.2010, 11:56:47 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
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
 * Short description of class taoItems_models_classes_QTI_Factory
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Factory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getInteraction
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string type
     * @param  array options
     * @return doc_Interaction
     */
    public static function getInteraction($type, $options = array())
    {
        $returnValue = null;

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023EA begin
        
        $baseClassName = str_replace('Factory', 'interaction_', get_class(self));
        $interactionClassName = $baseClassName . $type;
        
        if(!class_exists($interactionClassName, true)){
        	throw new Exception("Unable to found class $interactionClassName");
        }
        
		$returnValue = new $interactionClassName(null, $options);
        
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023EA end

        return $returnValue;
    }

    /**
     * Short description of method buildItem
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  SimpleXMLElement data
     * @param  string namespace
     * @return taoItems_models_classes_QTI_Item
     */
    public static function buildItem( SimpleXMLElement $data, $namespace = '')
    {
        $returnValue = null;

        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000248E begin

    	if(!empty($namespace)){
	       	$queryNamespace = $namespace . ':';
	    }
	    
	    //check on the root tag
	    if($data->getName() != 'assessmentItem'){
	       	throw new taoItems_models_classes_QTI_ParsingException("incorrect item root tag");
	    }
	       
	    $itemId = null;
       	if(isset($data['identifier'])){
			$itemId = (string)$data['identifier'];
       	}
       
       	$myItem = new taoItems_models_classes_QTI_Item($itemId, (array)$data->attributes());
       
     	
        $interactionNodes = $data->xpath("//{$queryNamespace}*[contains(name(.), 'Interaction')]");
        if($interactionNodes instanceof SimpleXMLElement){
	        foreach($interactionNodes as $interactionNode){
	        	$interaction = self::buildInteraction($interactionNode, $name);
	        	if(!is_null($interaction)){
	       			$myItem->addInteraction($interaction);
	        	}
	        }
        }
        
        
        $itemBodyNode = $data->xpath("/{$queryNamespace}itemBody");
        if($itemBodyNode instanceof SimpleXMLElement){
        	$itemBody->asXml();
        }
       
        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000248E end

        return $returnValue;
    }

    /**
     * Short description of method buildInteraction
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  SimpleXMLElement data
     * @param  string namespace
     * @return doc_Interaction
     */
    public static function buildInteraction( SimpleXMLElement $data, $namespace = '')
    {
        $returnValue = null;

        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002491 begin
        
    	if(!empty($namespace)){
	       	$queryNamespace = $namespace . ':';
	    }
       
       	$result = $data->xpath("//{$queryNamespace}*[contains(name(.), 'simpleChoice')]");
        
        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002491 end

        return $returnValue;
    }

    /**
     * Short description of method buildChoice
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  SimpleXMLElement data
     * @param  string namespace
     * @return taoItems_models_classes_QTI_Choice
     */
    public static function buildChoice( SimpleXMLElement $data, $namespace = '')
    {
        $returnValue = null;

        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002494 begin
        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:0000000000002494 end

        return $returnValue;
    }

    /**
     * Short description of method buildResponse
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  SimpleXMLElement data
     * @param  string namespace
     * @return taoItems_models_classes_QTI_Response
     */
    public static function buildResponse( SimpleXMLElement $data, $namespace = '')
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
     * @param  string namespace
     * @return taoItems_models_classes_QTI_Score
     */
    public static function buildScore( SimpleXMLElement $data, $namespace = '')
    {
        $returnValue = null;

        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000249A begin
        // section 127-0-1-1--12a4f8d3:12a37dedffb:-8000:000000000000249A end

        return $returnValue;
    }

    /**
     * Short description of method stripXmlNodes
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  SimpleXMLElement data
     * @param  string pattern
     * @return SimpleXMLElement
     */
    protected static function stripXmlNodes( SimpleXMLElement $data, $pattern)
    {
        $returnValue = null;

        // section 127-0-1-1--656c58c6:12a3c59f354:-8000:0000000000002462 begin
        
        foreach($data->children() as $child){
        	if(preg_macth($pattern, $child->getName())){
        		unset($data->{$child->getName()});
        	}
        	else{
        		$child = self::stripXmlNodes($child, $pattern);
        	}
        }
        
        $returnValue = $data;
        
        // section 127-0-1-1--656c58c6:12a3c59f354:-8000:0000000000002462 end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Factory */

?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.VariableFactory.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 09.11.2010, 13:36:54 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_models_classes_Matching_Variable
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/Matching/class.Variable.php');

/* user defined includes */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028B7-includes begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028B7-includes end

/* user defined constants */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028B7-constants begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028B7-constants end

/**
 * Short description of class taoItems_models_classes_Matching_VariableFactory
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
class taoItems_models_classes_Matching_VariableFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method create
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @param  string type
     * @return taoItems_models_classes_Matching_Variable
     */
    public static function create($data, $type = null)
    {
        $returnValue = null;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028B8 begin
    	
    	$varValue = $data;
		
		// The type has been defined manually
		if ($type == null){	
			$type = gettype ($data);	
		}
		
		// Create the variable according to its type
		switch ($type) {
			//Collection Tuple : our standard define an object as a tuple
			case 'object':
            case 'tuple':
				$returnValue = new taoItems_models_classes_Matching_Tuple ($varValue);
				break;
				
			//Collection List
            case 'array':
            case 'list':
				$returnValue = new taoItems_models_classes_Matching_List ($varValue);
				break;
						
			case 'boolean':
			case 'integer':
			case 'double':
			case 'float':
			case 'string':
			case 'NULL':
				$returnValue = new taoItems_models_classes_Matching_BaseTypeVariable ($varValue);
				break;
				
			default:
				throw new Exception ('taoItems_models_classes_Matching_VariableFactory::create variable type unknown '.$type.' for '.$varValue);
		}
	
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028B8 end

        return $returnValue;
    }

    /**
     * Short description of method createJSONVariableFromQTIData
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @param  string card
     * @param  string baseType
     * @param  values
     */
    public function createJSONVariableFromQTIData($id, $card, $baseType,    $values)
    {
        $returnValue = null;

        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A21 begin
        
    	$returnValue = Array ();
    	$returnValue['identifier'] = $id;
    	$returnValue['value'] = null;
    	
    	// The value container
    	$valueContainer = Array ();
    	
    	if ($values != null){
    		foreach ($values as $value){
	    		$value = taoItems_models_classes_Matching_VariableFactory::createJSONValueFromQTIData  ($value, $baseType);	
		    	array_push ($valueContainer, $value);
	    	} 
	    	
	    	// If the cardinality is multiple or ordered
	        switch ($card){
	        	case 'single':
	        		if (count($valueContainer)){
	        			$returnValue['value'] = $valueContainer[0];
	        		}
	        		else { 
	        			$returnValue = null;
	        		}
	        		break;
	        		
	        	case 'multiple':
	    			$returnValue['value'] = $valueContainer;
	    			break;
	    			
	        	case 'ordered':
	        		$returnValue['value'] = (object) $valueContainer;
	    			break;
	    	}    	
    	}else {
    		$type = "";
    		// @todo not conform to the matching standard
    		// used if the values is not set and we need to define a type as well
    		switch ($baseType){
                case "boolean":
                    $type = "boolean";
                    break;
                case "integer":
                    $type = "integer";
                    break;
    			case "float":
    				$type = "float";
    				break;
    			case "identifier":
    			case "string":
    				$type = "string";
    				break;
    			case "pair":
    				$type = "list";
    				break;
    			case "directedPair":
    				$type = "tuple";
    				break;
    			default:
    				throw new Exception ("taoItems_models_classes_Matching_VariableFactory::createJSONVariableFromQTIData an error occured while parsing : the type ".$baseType." is unknown");
    		}
    		$returnValue['type'] = $type;
    	}

    	$returnValue = (object) $returnValue;
        
        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A21 end

        return $returnValue;
    }

    /**
     * Short description of method createJSONValueFromQTIData
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  value
     * @param  string baseType
     */
    public function createJSONValueFromQTIData(   $value, $baseType)
    {
        $returnValue = null;

        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A23 begin
        
    	switch ($baseType){
            case "boolean":
                $returnValue  = (bool) $value;
                break;
            case "integer":
                $returnValue  = (int) $value;
                break;
    		case "float":
    			$returnValue  = (float) $value;
    			break;
    		case "identifier":
    		case "string":
    			$returnValue  = $value;
    			break;
    			
    		case "pair":
    			$returnValue = explode (" ", $value);
    			break;
    			
    		case "directedPair":
    			$returnValue = (object)explode (" ", $value);
    			break;
    			
    		default :
    			throw new Exception ("taoItems_models_classes_Matching_VariableFactory::createJSONValueFromQTIData an error occured while parsing : the type ".$baseType." is unknown");
    	}
        
        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A23 end

        return $returnValue;
    }

    /**
     * Convert data in numeric BaseTypeVariable.
     * If the data is not a valid base type value return null.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return taoItems_models_classes_Matching_BaseTypeVariable
     */
    public function toNumericBaseType(   $data)
    {
        $returnValue = null;

        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B82 begin
        
        // IF the first expression is not a BaseTypeVariable try to create it
        if (!($data instanceof taoItems_models_classes_Matching_BaseTypeVariable)){
            if (taoItems_models_classes_Matching_BaseTypeVariable::isValidValue ($data)) {
                $matchingVar = new taoItems_models_classes_Matching_BaseTypeVariable ($data);
                if ($matchingVar->isNumerical ()){
                    $returnValue = $matchingVar;
                }
            }
        } else {
            if ($data->isNumerical()){
                $returnValue = $data;
            }
        }
        
        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B82 end

        return $returnValue;
    }

    /**
     * Short description of method toBooleanBaseType
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return taoItems_models_classes_Matching_BaseTypeVariable
     */
    public function toBooleanBaseType(   $data)
    {
        $returnValue = null;

        // section 127-0-1-1-7e272ec4:12c307f74c9:-8000:0000000000002B97 begin
        
        // IF the first expression is not a BaseTypeVariable try to create it
        if (!($data instanceof taoItems_models_classes_Matching_BaseTypeVariable)){
            if (taoItems_models_classes_Matching_BaseTypeVariable::isValidValue ($data)) {
                $matchingVar = new taoItems_models_classes_Matching_BaseTypeVariable ($data);
                if ($matchingVar->isBoolean ()){
                    $returnValue = $matchingVar;
                }
            }
        } else {
            if ($data->isBoolean()){
                $returnValue = $data;
            }
        }
        
        // section 127-0-1-1-7e272ec4:12c307f74c9:-8000:0000000000002B97 end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_Matching_VariableFactory */

?>
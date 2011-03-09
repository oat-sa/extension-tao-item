<?php

error_reporting(E_ALL);

/**
 * The class variable factory provide to developpers a set 
 * of usefull functions arround the variables creation process
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Variable is an abstract class which is the representation 
 * of all the variables managed by the system
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
 * The class variable factory provide to developpers a set 
 * of usefull functions arround the variables creation process
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
     * Create a variable functions of the given data.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data Data of the variable (the value)
     * @param  string type The type is optional, if it is not defined the data will
define the type of the variable
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
            case 'point':
				$returnValue = new taoItems_models_classes_Matching_Tuple ($varValue);
				break;
				
			//Collection List
            case 'array':
            case 'list':
				$returnValue = new taoItems_models_classes_Matching_List ($varValue);
				break;

            // Shape       
            case 'circle':
            case 'ellipse':
                $returnValue = new taoItems_models_classes_Matching_Ellipse ($varValue);
                break;
            case 'rect':
            case 'poly':
                $returnValue = new taoItems_models_classes_Matching_Poly ($varValue);
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
     * Create a jSon Variable from QTI data
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id Identifier of the variable
     * @param  string card Cardinality of the variable [single, multiple, ordered]
     * @param  string baseType
     * @param  values Value of the variable
     */
    public static function createJSONVariableFromQTIData($id, $card, $baseType,    $values)
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
                case "point":
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
     * Create jSon value from QTI data
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  value Data to convert
     * @param  string baseType Type of the QTI data
     */
    public static function createJSONValueFromQTIData(   $value, $baseType)
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
            case "point":
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
     * @param  data Data to convert
     * @return taoItems_models_classes_Matching_BaseTypeVariable
     */
    public static function toNumericBaseType(   $data)
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
     * Convert data in boolean BaseTypeVariable.
     * If the data is not a valid base type value return null.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data Data to convert
     * @return taoItems_models_classes_Matching_BaseTypeVariable
     */
    public static function toBooleanBaseType(   $data)
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

    /**
     * Create a jSon shape from QTI data
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  value
     */
    public static function createJSONShapeFromQTIData(   $value)
    {
        $returnValue = null;

        // section 127-0-1-1-42a22512:12e9b2a4d3c:-8000:0000000000002CFA begin
        
    $returnValue = Array();
        $type = $value["shape"];
        $returnValue["type"] = $type;
        
        switch ($type) {
            case "rect":
                $points = explode(',', $value["coords"]);
                $returnValue["points"] = Array (
                    (object) Array($points[0], $points[1])
                    , (object) Array($points[2], $points[1])
                    , (object) Array($points[2], $points[3])
                    , (object) Array($points[0], $points[3])
                );
                break;
                
            case "poly":
                $coords = explode(',', $value["coords"]);
                $returnValue["points"] = Array();
                while (count ($coords)) {
                    array_push ($returnValue["points"], (object) array_splice($coords, 0, 2));
                }
                break;
                
            case "circle":
                $points = explode(',', $value["coords"]);
                $returnValue["center"] = (object) array_slice ($points, 0, 2);
                $returnValue["hradius"] = $points[2];
                $returnValue["vradius"] = $points[2];
                break;
                
            case "ellipse":
                $points = explode(',', $value["coords"]);
                $returnValue["center"] = (object) array_slice ($points, 0, 2);
                $returnValue["hradius"] = $points[2];
                $returnValue["vradius"] = $points[3];
                break;
        }
        
        // section 127-0-1-1-42a22512:12e9b2a4d3c:-8000:0000000000002CFA end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_Matching_VariableFactory */

?>
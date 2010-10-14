<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.VariableFactory.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 14.10.2010, 23:05:48 with ArgoUML PHP module 
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
			//Collection Tuple : our standart define an object as a tuple
			case 'object':
				$returnValue = new taoItems_models_classes_Matching_Tuple ($varValue);
				break;
				
			//Collection List
			case 'array':
				$returnValue = new taoItems_models_classes_Matching_List ($varValue);
				break;
						
			case 'boolean':
			case 'integer':
			case 'double':
			case 'string':
			case 'NULL':
				$returnValue = new taoItems_models_classes_Matching_BaseTypeVariable ($varValue);
				break;
				
			default:
				throw new Exception ('taoItems_models_classes_Matching_VariableFactory::create variable type unknown '.$variableType.' for '.$varValue);
		}
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028B8 end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_Matching_VariableFactory */

?>
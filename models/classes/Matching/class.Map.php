<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.Map.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.11.2010, 11:59:48 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002947-includes begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002947-includes end

/* user defined constants */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002947-constants begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002947-constants end

/**
 * Short description of class taoItems_models_classes_Matching_Map
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
class taoItems_models_classes_Matching_Map
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute value
     *
     * @access public
     * @var array
     */
    public $value = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  value
     * @return mixed
     */
    public function __construct(   $value)
    {
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A94 begin
        
         $this->setValue ($value);
         
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A94 end
    }

    /**
     * Short description of method map
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return double
     */
    public function map( taoItems_models_classes_Matching_Variable $var)
    {
        $returnValue = (float) 0.0;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002948 begin

        $mapKeyFound = array ();
        
        // for each map element, check if it is represented in the given variable
    	foreach ($this->value as $mapKey=>$mapElt) {
    		
    		// If the given var is a collection
    		if ($var instanceOf taoItems_models_classes_Matching_Collection){
    			if ($var->contain ($mapElt['key'])!=null /* && !isset($mapKeyFound[$mapKey])*/){
    				$returnValue += $mapElt['value'];
    				//$mapKeyFound[$mapKey] = true;
    			}	
    		}
    		else {
    			if ($var->match ($mapElt['key'])){
    				$returnValue += $mapElt['value'];
    			}
    			
    		}
	    }

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002948 end

        return (float) $returnValue;
    }

    /**
     * Short description of method setValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return mixed
     */
    public function setValue(   $data)
    {
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A97 begin
        
    	foreach ($data as $elt){
    		$this->value[] = array("value"=>$elt->value, "key"=>taoItems_models_classes_Matching_VariableFactory::create($elt->key));
    	}  
    	
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A97 end
    }

} /* end of class taoItems_models_classes_Matching_Map */

?>
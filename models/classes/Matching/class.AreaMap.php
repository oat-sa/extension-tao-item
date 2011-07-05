<?php

error_reporting(E_ALL);

/**
 * A special class used to create a mapping from a source set of 
 * point values to a target set of float values. When mapping 
 * containers the result is the sum of the mapped values from
 * the target set
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package taoItems
 * @subpackage models_classes_Matching
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Shape represents the different shapres managed by the tao 
 * matching api.
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('taoItems/models/classes/Matching/class.Shape.php');

/* user defined includes */
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C84-includes begin
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C84-includes end

/* user defined constants */
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C84-constants begin
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C84-constants end

/**
 * A special class used to create a mapping from a source set of 
 * point values to a target set of float values. When mapping 
 * containers the result is the sum of the mapped values from
 * the target set
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
class taoItems_models_classes_Matching_AreaMap
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute value
     *
     * @access public
     * @var array
     */
    public $value = array();

    /**
     * Short description of attribute defaultValue
     *
     * @access private
     * @var double
     */
    private $defaultValue = 0.0;

    /**
     * Short description of attribute upperBound
     *
     * @access private
     * @var double
     */
    private $upperBound = 0.0;

    /**
     * Short description of attribute lowerBound
     *
     * @access private
     * @var double
     */
    private $lowerBound = 0.0;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  data
     * @return mixed
     */
    public function __construct(   $data)
    {
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C88 begin
    
    	if (isset ($data->upperBound)){
    		$this->upperBound = $data->upperBound;
    	}else{
    		$this->upperBound = null;
    	}
    	if (isset ($data->lowerBound)){
    		$this->lowerBound = $data->lowerBound;
    	}else{
    		$this->lowerBound = null;
    	}
    	if (isset ($data->defaultValue)){
    		$this->defaultValue = $data->defaultValue;
    	}   
    	
        $this->setValue ($data->value);
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C88 end
    }

    /**
     * This function looks up the value of a given Variable
     * that must be of type point, and transforms it 
     * using the associated areaMapping. The transformation 
     * is similar to map function of the Map class except that the 
     * points are tested against each area in turn. When mapping 
     * containers each area can be mapped once only.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Variable var
     * @return double
     */
    public function map( taoItems_models_classes_Matching_Variable $var)
    {
        $returnValue = (float) 0.0;

        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C8B begin

        $mapEntriesFound = array ();
        
        // for each map element, check if it is represented in the given variable
        foreach ($this->value as $mapKey=>$mapElt) {
            
            // Collection of points
            if ($var instanceOf taoItems_models_classes_Matching_List){
    			$found = false;
                // For each value contained by the matching var to map
                foreach ($var->getValue() as $key => $value) {
                    // If one is contained by the current map value
                    if ($mapElt['key']->contains($value)) {
                    	$mapEntriesFound[] = $key;
                    	if (!$found) {
                        	$returnValue += $mapElt['value'];
                        	$found = true;
                    	}
                    }
                }
            }
            // Uniq Point
            else if ($var instanceOf taoItems_models_classes_Matching_Tuple) {
                if ($mapElt['key']->contains($var)){
                    $mapEntriesFound[] = $mapElt['key'];
    				$returnValue += $mapElt['value'];
    				break;
                }
                
            }
        }
        
 		// If a defaultValue has been set and it is different from zero
    	if ($this->defaultValue != 0) {		
    		// If the given var is a collection
    		if ($var instanceOf taoItems_models_classes_Matching_Collection){
    			// How many values have not been found * default value
	        	$delta = count($var->getValue()) - count($mapEntriesFound);
	        	$returnValue += $delta * $this->defaultValue;
    		} else if (!$count(mapEntriesFound)) {
    			$returnValue = $this->defaultValue;
    		}
    	}
    	
    	if ($this->lowerBound){
    		if ($returnValue < $this->lowerBound){
    			$returnValue = $this->lowerBound;
    		}
    	}
    	
    	if ($this->upperBound){
    		if ($returnValue > $this->upperBound){
    			$returnValue = $this->upperBound;
    		}
    	}
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C8B end

        return (float) $returnValue;
    }

    /**
     * Set the value of the area map.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  data
     * @return mixed
     */
    public function setValue(   $data)
    {
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C8E begin
        
        foreach ($data as $elt){
            $this->value[] = array("value"=>$elt->value, "key"=>taoItems_models_classes_Matching_VariableFactory::create((object)$elt->key, $elt->key['type']));
        }
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C8E end
    }

} /* end of class taoItems_models_classes_Matching_AreaMap */

?>
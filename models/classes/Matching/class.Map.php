<?php

error_reporting(E_ALL);

/**
 * A special class used to create a mapping from a source set of 
 * any baseType to a single float.
 *
 * @author firstname and lastname of author, <author@example.org>
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
 * A special class used to create a mapping from a source set of 
 * any baseType to a single float.
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
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
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A94 begin
        
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
         
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A94 end
    }

    /**
     * This function looks up the value of a given 
     * Variable and then transforms it using the associated 
     * mapping. The result is a single float. If the given variable 
     * has single cardinality then the value returned is simply the 
     * mapped target value from the map.
     * If the response variable has  multiple cardinality then the 
     * value returned is the sum of the mapped target values.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Variable var
     * @return double
     */
    public function map( taoItems_models_classes_Matching_Variable $var)
    {
        $returnValue = (float) 0.0;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002948 begin

        $mapEntriesFound = array ();
        
        // for each map element, check if it is represented in the given variable
    	foreach ($this->value as $mapKey=>$mapElt) {
    		
    		// If the given var is a collection
    		if ($var instanceOf taoItems_models_classes_Matching_Collection){
    			$found = false;
    		    // For each value contained by the matching var to map
    		    foreach ($var->getValue() as $key => $value) {
                    // If one match the current map value
                    if ($value->match ($mapElt['key'])) {
                    	$mapEntriesFound[] = $key;
                    	if (!$found) {
                        	$returnValue += $mapElt['value'];
                        	$found = true;
                    	}
                    }
    		    }
    		}
    		else {
    			if ($var->match ($mapElt['key'])){
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
    		} else if (!count($mapEntriesFound)) {
    			$returnValue = $this->defaultValue;
    		}
    	}
    	
    	if (!is_null($this->lowerBound)){
    		if ($returnValue < $this->lowerBound){
    			$returnValue = $this->lowerBound;
    		}
    	}
    	
    	if (!is_null($this->upperBound)){
    		if ($returnValue > $this->upperBound){
    			$returnValue = $this->upperBound;
    		}
    	}
	    
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002948 end

        return (float) $returnValue;
    }

    /**
     * Set the value of the map.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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
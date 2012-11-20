<?php

error_reporting(E_ALL);

/**
 * tuple represents the collection tuple as managed by the the
 * tao matching api
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Collection is an abstract class which represents
 * the variables "collection".
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('taoItems/models/classes/Matching/class.Collection.php');

/* user defined includes */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293A-includes begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293A-includes end

/* user defined constants */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293A-constants begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293A-constants end

/**
 * tuple represents the collection tuple as managed by the the
 * tao matching api
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
class taoItems_models_classes_Matching_Tuple
    extends taoItems_models_classes_Matching_Collection
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  data data used to construct the tuple
     * @return mixed
     */
    public function __construct(   $data)
    {
        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002955 begin
        
    	$this->setValue ($data);
    	
        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002955 end
    }

    /**
     * Get the type of the variable
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     * @see Variable
     */
    public function getType()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293D begin
        
        return 'tuple';
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293D end

        return (string) $returnValue;
    }

    /**
     * Get an element by its key. Return null if the element does not exist.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string key
     * @return taoItems_models_classes_Matching_Variable
     */
    public function getElement($key)
    {
        $returnValue = null;

        // section 127-0-1-1-2688e070:12bac090945:-8000:000000000000295D begin
        
        if (isset($this->value[$key])){
        	$returnValue = $this->value[$key];
        }
        
        // section 127-0-1-1-2688e070:12bac090945:-8000:000000000000295D end

        return $returnValue;
    }

    /**
     * Match a tuple with an other
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Variable tuple
     * @return boolean
     */
    public function match( taoItems_models_classes_Matching_Variable $tuple)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002943 begin
        
        // If the cardinality is not the same return false
        if ($this->length() != $tuple->length()){
        	$returnValue = false;
        }
        else {
	        // Test if the both tuples have the same content
	        foreach ($this->value as $key=>$elt){
	        	$compareElt = $tuple->getElement ($key);
	        	
	        	if ($compareElt == null){
	        		$returnValue = false;
	        		break;
	        	} else if ($elt->getType () != $compareElt->getType()){
	        		throw new Exception ('taoItems_models_classes_Matching_Tuple::match an error occured : types of the elements to match are not the same ['.$elt->getType ().'] and ['.$compareElt->getType().']');
	        		$returnValue = false;
	        		break;
	        	} else if (!$elt->match ($compareElt)){
	        		$returnValue = false;
	        		break;
	        	} else {
	        		$returnValue = true;
	        	}
	        }
        }
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002943 end

        return (bool) $returnValue;
    }

    /**
     * Set value of the tuple from an array of data. The array of data could be
     * array of Variables or an array of "base type" variables
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  data
     * @return mixed
     */
    public function setValue(   $data)
    {
        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002952 begin
    	
    	// @todo be carrefull the set function get only base type variable
    	
        $this->value = array();
        
    	if ($data == null){
            return;
        }
    	
    	foreach ($data as $key=>$elt){
    	    if ($elt instanceOf taoItems_models_classes_Matching_Variable) {
    	        $this->value[$key] = $elt;
    	    } else {
    	        $this->value[$key] = taoItems_models_classes_Matching_VariableFactory::create ($elt);
    	    }
    	}
    	
        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002952 end
    }

    /**
     * Export the variable in jSon format.
     * {
     *     "identifier":"myVariableIdentifier",
     *     "value": {
     *         "0" : "myVar1"
     *         , "1" : "myVar2"
     *     }
     * }
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @see Variable
     */
    public function toJSon()
    {
        $returnValue = null;

        // section 127-0-1-1--6df7f690:12c1ba8488c:-8000:0000000000002B37 begin
        
        $toJSON = Array ();
        foreach ($this->getValue() as $value){
            $toJSON[] = $value->toJSon();
        }
        $returnValue = json_encode((object)$toJSON);
        
        // section 127-0-1-1--6df7f690:12c1ba8488c:-8000:0000000000002B37 end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_Matching_Tuple */

?>
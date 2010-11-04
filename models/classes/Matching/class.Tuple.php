<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.Tuple.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 21.10.2010, 10:19:44 with ArgoUML PHP module 
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
 * include taoItems_models_classes_Matching_Collection
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/Matching/class.Collection.php');

/* user defined includes */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293A-includes begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293A-includes end

/* user defined constants */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293A-constants begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293A-constants end

/**
 * Short description of class taoItems_models_classes_Matching_Tuple
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return mixed
     */
    public function __construct(   $data)
    {
        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002955 begin
        
    	$this->setValue ($data);
    	
        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002955 end
    }

    /**
     * Short description of method getType
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
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
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
     * Short description of method match
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Tuple tuple
     * @return boolean
     */
    public function match( taoItems_models_classes_Matching_Tuple $tuple)
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
     * Short description of method setValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return mixed
     */
    public function setValue(   $data)
    {
        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002952 begin
    	
    	// @todo be carrefull the set function get only base type variable
    	
    	$this->value = array();
    	foreach ($data as $key=>$elt){
    	    if ($elt instanceOf taoItems_models_classes_Matching_Variable) {
    	        $this->value[$key] = $elt;
    	    } else {
    	        $this->value[$key] = taoItems_models_classes_Matching_VariableFactory::create ($elt);
    	    }
    	}
    	
        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002952 end
    }

} /* end of class taoItems_models_classes_Matching_Tuple */

?>
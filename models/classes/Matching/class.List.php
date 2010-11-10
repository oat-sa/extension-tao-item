<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.List.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 10.11.2010, 19:53:03 with ArgoUML PHP module 
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
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002939-includes begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002939-includes end

/* user defined constants */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002939-constants begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002939-constants end

/**
 * Short description of class taoItems_models_classes_Matching_List
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
class taoItems_models_classes_Matching_List
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
     * @param  array data
     * @return mixed
     */
    public function __construct($data)
    {
        // section 127-0-1-1-2688e070:12bac090945:-8000:000000000000294F begin
        
    	$this->setValue ($data);
    	
        // section 127-0-1-1-2688e070:12bac090945:-8000:000000000000294F end
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

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293F begin
        
        return 'list';
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000293F end

        return (string) $returnValue;
    }

    /**
     * Get an element by its index. Return null if the element does not exist.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  int key
     * @return taoItems_models_classes_Matching_Variable
     */
    public function getElement($key)
    {
        $returnValue = null;

        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002960 begin
        
        if (isset($this->value[$key])){
        	$returnValue = $this->value[$key];
        }
        
        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002960 end

        return $returnValue;
    }

    /**
     * Short description of method match
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  List list
     * @return boolean
     */
    public function match( taoItems_models_classes_Matching_List $list)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002945 begin

        $returnValue = true;
        
        // If the cardinality is not the same return false
        if ($this->length() != $list->length()){
        	return false;	
        }
        
        // Test if the both lists have the same content
        for ($i=0; $i<$this->length(); $i++) {
	        $tempResult = false;
			
	        for ($j=0; $j<$list->length(); $j++) {
	        	if ($this->getElement($i)->getType () != $list->getElement($j)->getType()){
	        		throw new Exception ('taoItems_models_classes_Matching_List::match an error occured : types of the elements to match are not the same ['. $this->getElement($i)->getType () .'] and ['. $list->getElement($j)->getType() .']');
	        		$returnValue = false;
	        	} 
	        	else if ($this->getElement($i)->match($list->getElement($j))) {
	            	$tempResult = true;
	                break;
	            }
	        }
	        if (!$tempResult){
	        	$returnValue = false;
				break;	
			}
        }     
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002945 end

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
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002959 begin

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

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002959 end
    }

    /**
     * Short description of method toJSon
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    public function toJSon()
    {
        $returnValue = null;

        // section 127-0-1-1--6df7f690:12c1ba8488c:-8000:0000000000002B39 begin
        
        $toJSON = Array ();
        foreach ($this->getValue() as $value){
            $toJSON[] = $value->toJSon();
        }
        $returnValue = json_encode($toJSON);
        
        // section 127-0-1-1--6df7f690:12c1ba8488c:-8000:0000000000002B39 end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_Matching_List */

?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.AreaMap.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 15.12.2010, 13:27:50 with ArgoUML PHP module 
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
 * include taoItems_models_classes_Matching_Shape
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/Matching/class.Shape.php');

/* user defined includes */
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C84-includes begin
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C84-includes end

/* user defined constants */
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C84-constants begin
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C84-constants end

/**
 * Short description of class taoItems_models_classes_Matching_AreaMap
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C88 begin
        
        $this->setValue ($value);
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C88 end
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

        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C8B begin

        $mapKeyFound = array ();
        
        // for each map element, check if it is represented in the given variable
        foreach ($this->value as $mapKey=>$mapElt) {
            
            // Collection of points
            if ($var instanceOf taoItems_models_classes_Matching_List){
                // For each value contained by the matching var to map
                foreach ($var->getValue() as $key => $value) {
                    // If one is contained by the current map value
                    if ($mapElt['key']->contains($value)) {
                        $returnValue += $mapElt['value'];
                        break;
                    }
                }
            }
            // Uniq Point
            else if ($var instanceOf taoItems_models_classes_Matching_Tuple) {
                if ($mapElt['key']->contains($var)){
                    $returnValue += $mapElt['value'];
                }
                
            }
        }
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C8B end

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
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C8E begin
        
        foreach ($data as $elt){
            $this->value[] = array("value"=>$elt->value, "key"=>taoItems_models_classes_Matching_VariableFactory::create((object)$elt->key, $elt->key['type']));
        }
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C8E end
    }

} /* end of class taoItems_models_classes_Matching_AreaMap */

?>
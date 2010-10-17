<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.BaseTypeVariable.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 17.10.2010, 20:12:55 with ArgoUML PHP module 
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
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028C4-includes begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028C4-includes end

/* user defined constants */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028C4-constants begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028C4-constants end

/**
 * Short description of class taoItems_models_classes_Matching_BaseTypeVariable
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
class taoItems_models_classes_Matching_BaseTypeVariable
    extends taoItems_models_classes_Matching_Variable
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

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
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028CA begin
        
        $this->setValue ($value);
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028CA end
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

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028CD begin
        
        return gettype($this->value);
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028CD end

        return (string) $returnValue;
    }

    /**
     * Short description of method isNull
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function isNull()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AB3 begin
        
        $returnValue = $this->value == null;
        
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AB3 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  value
     * @return mixed
     */
    public function setValue(   $value)
    {
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002956 begin
        
    	$this->value = $value;
    	
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002956 end
    }

} /* end of class taoItems_models_classes_Matching_BaseTypeVariable */

?>
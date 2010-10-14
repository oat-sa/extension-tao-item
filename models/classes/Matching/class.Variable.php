<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.Variable.php
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

/* user defined includes */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028BA-includes begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028BA-includes end

/* user defined constants */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028BA-constants begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028BA-constants end

/**
 * Short description of class taoItems_models_classes_Matching_Variable
 *
 * @abstract
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
abstract class taoItems_models_classes_Matching_Variable
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute value
     *
     * @access protected
     * @var object
     */
    protected $value = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getType
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public abstract function getType();

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    public function getValue()
    {
        $returnValue = null;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000292E begin
        
        $returnValue = $this->value;
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000292E end

        return $returnValue;
    }

    /**
     * Short description of method equal
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return boolean
     */
    public function equal( taoItems_models_classes_Matching_Variable $var)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002924 begin
        
        if ($this->getType() != $var->getType()){
        	$returnValue = false;
        } else {
        	$returnValue = $this->getValue() == $var->getValue();	
        }
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002924 end

        return (bool) $returnValue;
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

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028C1 begin
        
        $returnValue = $this->value == null;
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028C1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method match
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return boolean
     */
    public function match( taoItems_models_classes_Matching_Variable $var)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002921 begin
        
        return $this->equal ($var);
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002921 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setValue
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  value
     * @return mixed
     */
    public abstract function setValue(   $value);

    /**
     * Short description of method toJSon
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    public function toJSon()
    {
        $returnValue = null;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002934 begin
        
        $returnValue = $this->getValue();
        
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002934 end

        return $returnValue;
    }

} /* end of abstract class taoItems_models_classes_Matching_Variable */

?>
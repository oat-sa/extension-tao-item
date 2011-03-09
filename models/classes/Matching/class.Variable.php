<?php

error_reporting(E_ALL);

/**
 * Variable is an abstract class which is the representation 
 * of all the variables managed by the system
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
 * Variable is an abstract class which is the representation 
 * of all the variables managed by the system
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
     * Value of the variable
     *
     * @access protected
     * @var object
     */
    protected $value = null;

    // --- OPERATIONS ---

    /**
     * Get the type of the variable
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public abstract function getType();

    /**
     * Get the value of the variable
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
     * Check if the variable is equal to another
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var The variable to compare
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
     * check if the variable is null
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public abstract function isNull();

    /**
     * Match a variable to another. This function does not match a 
     * strict equality. In the case of array the match function will 
     * check it the two arrays have the same value.
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
     * Set the value of the variable
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data The value to set
     * @return mixed
     */
    public abstract function setValue(   $data);

    /**
     * Export the variable in jSon format.
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    public abstract function toJSon();

} /* end of abstract class taoItems_models_classes_Matching_Variable */

?>
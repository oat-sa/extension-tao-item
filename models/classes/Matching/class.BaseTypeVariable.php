<?php

error_reporting(E_ALL);

/**
 * BaseTypeVariable represents the scalar variables
 * managed by the system.
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Variable is an abstract class which is the representation 
 * of all the variables managed by the system
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
 * BaseTypeVariable represents the scalar variables
 * managed by the system.
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

        if (! $this->isValidValue($value)){
            throw new Exception ('taoItems_models_classes_Matching_BaseTypeVariable::_construct an error occured : The value is not a valid value type ['.gettype($value).'], expected [string, boolean, float, integer]');
        }
            
        $this->setValue ($value);
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028CA end
    }

    /**
     * Get the type of the variable
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
     * check if the variable is null
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     * @see Variable
     */
    public function isNull()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AB3 begin
        
        $returnValue = $this->value === null;
        
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AB3 end

        return (bool) $returnValue;
    }

    /**
     * Set the value of the variable
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  value
     * @return mixed
     * @see Variable
     */
    public function setValue(   $value)
    {
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002956 begin
        
    	$this->value = $value;
    	
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002956 end
    }

    /**
     * Export the variable in jSon format.
     * {
     *     "identifier":"myVariableIdentifier",
     *     "value":true
     * }
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @see Variable
     */
    public function toJSon()
    {
        $returnValue = null;

        // section 127-0-1-1--6df7f690:12c1ba8488c:-8000:0000000000002B35 begin
        $returnValue = $this->getValue();
        // section 127-0-1-1--6df7f690:12c1ba8488c:-8000:0000000000002B35 end

        return $returnValue;
    }

    /**
     * Test if a value is compliant with the types managed
     * by the BaseType Variable
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return boolean
     */
    public static function isValidValue(   $data)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-d88aba0:12c2bef8126:-8000:0000000000002B54 begin
        
        // If the data is null
        if ($data == null){
            $returnValue = true;
        } else {
            // If the data has a valid type
            switch (gettype ($data)){
                case 'string':
                case 'integer':
                case 'float':
                case 'double': // only in php
                case 'boolean':
                    $returnValue = true;
            }
        }
        
        // section 127-0-1-1-d88aba0:12c2bef8126:-8000:0000000000002B54 end

        return (bool) $returnValue;
    }

    /**
     * Check if the variable is numerical
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function isNumerical()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-d88aba0:12c2bef8126:-8000:0000000000002B57 begin

        // If the data is null
        if ($this->getValue() == null){
            $returnValue = true;
        } 
        else {
            switch ($this->getType()){
                case 'integer':
                case 'float':
                case 'double': // only in php
                    $returnValue = true;
            }
        }
        
        // section 127-0-1-1-d88aba0:12c2bef8126:-8000:0000000000002B57 end

        return (bool) $returnValue;
    }

    /**
     * check if the variable is boolean
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function isBoolean()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7e272ec4:12c307f74c9:-8000:0000000000002B9A begin
        
        // If the data is null
        if ($this->getValue() == null){
            $returnValue = true;
        } 
        else {
            switch ($this->getType()){
                case 'boolean':
                    $returnValue = true;
            }
        }
        
        // section 127-0-1-1-7e272ec4:12c307f74c9:-8000:0000000000002B9A end

        return (bool) $returnValue;
    }

} /* end of class taoItems_models_classes_Matching_BaseTypeVariable */

?>
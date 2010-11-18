<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.Collection.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 16.11.2010, 14:18:07 with ArgoUML PHP module 
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
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000295D-includes begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000295D-includes end

/* user defined constants */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000295D-constants begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000295D-constants end

/**
 * Short description of class taoItems_models_classes_Matching_Collection
 *
 * @abstract
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
abstract class taoItems_models_classes_Matching_Collection
    extends taoItems_models_classes_Matching_Variable
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method contains
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @param  array options options.needleType {String} Define if the script has to go through the needle or it must treat it as  scalar variable
     * @return boolean
     */
    public function contains( taoItems_models_classes_Matching_Variable $var, $options = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A9A begin
        
        $needleType = 'collection';
        if (isset($options['needleType'])){
            $needleType = $options['needleType'];
        }
        
        // If the needle is a Tuple
        if ($var instanceOf taoItems_models_classes_Matching_Tuple && $needleType == 'collection') {
            foreach ($var->getValue() as $key => $value) {
                // A tuple may contains only other Tuple
                if (! $var->value[$key] instanceOf taoItems_models_classes_Matching_Tuple){
                    $returnValue = false;
                    break;
                }
                if ($this->value[$key]->match($var->value[$key])){
                    $returnValue = true;
                } else {
                    $returnValue = false;
                    break;
                }
            }
        }
        // Else if the needle is a List
        else if ($var instanceOf taoItems_models_classes_Matching_List && $needleType == 'collection') {
            foreach ($var->getValue() as $key => $value) {
                if ($this->contains ($var->value[$key])){
                    $returnValue = true;
                } else {
                    $returnValue = false;
                    break;
                }
            }
        } 
        // Else we check if the value is include is the current collection
        else {
            try {
                foreach ($this->getValue() as $key => $value) {                    
                    // If the needle is not of the same type that an item of the collection (escape)
                    if ($var->getType() != $this->value[$key]->getType()){
                        $returnValue = false;
                        break;
                    } 
                    // Else we check if the needle match the current item
                    else if ($var->match ($this->value[$key])) {
                        $returnValue = true;
                        break;
                    } else {
                        $returnValue = false;
                    }
                }
            } catch (Exception $e)
            {
                var_dump ($e->getMessage()); 
            }
        }
        
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A9A end

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

        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A9D begin
        
        $returnValue = empty ($this->value);
        
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A9D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method length
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_Session_int
     */
    public function length()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002966 begin
        
        $returnValue = count($this->value);
        
        // section 127-0-1-1-2688e070:12bac090945:-8000:0000000000002966 end

        return (int) $returnValue;
    }

} /* end of abstract class taoItems_models_classes_Matching_Collection */

?>
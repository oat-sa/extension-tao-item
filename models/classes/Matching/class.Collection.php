<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.Collection.php
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
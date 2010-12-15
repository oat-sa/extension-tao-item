<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.Shape.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 15.12.2010, 13:43:09 with ArgoUML PHP module 
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
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C9C-includes begin
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C9C-includes end

/* user defined constants */
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C9C-constants begin
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C9C-constants end

/**
 * Short description of class taoItems_models_classes_Matching_Shape
 *
 * @abstract
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
abstract class taoItems_models_classes_Matching_Shape
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute type
     *
     * @access protected
     * @var string
     */
    protected $type = '';

    // --- OPERATIONS ---

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

        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CA0 begin
        
        $returnValue = $this->type;
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CA0 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setType
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string type
     * @return mixed
     */
    public function setType($type)
    {
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CCA begin
        
        $this->type = $type;
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CCA end
    }

} /* end of abstract class taoItems_models_classes_Matching_Shape */

?>
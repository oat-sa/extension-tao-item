<?php

error_reporting(E_ALL);

/**
 * Shape represents the different shapres managed by the tao 
 * matching api.
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
 * Shape represents the different shapres managed by the tao 
 * matching api.
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
     * Type of the shape [poly, rect, ellipse, circle]
     *
     * @access protected
     * @var string
     */
    protected $type = '';

    // --- OPERATIONS ---

    /**
     * Get the type of the shape
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
     * set the type of the shape
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string type The type can be [poly,rect,ellipse,circle]
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
<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.CustomComposite.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 20.01.2012, 19:06:03 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_models_classes_QTI_response_Composite
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/class.Composite.php');

/* user defined includes */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009038-includes begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009038-includes end

/* user defined constants */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009038-constants begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009038-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_CustomComposite
    extends taoItems_models_classes_QTI_response_Composite
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute compositionRules
     *
     * @access public
     * @var array
     */
    public $compositionRules = array();

    // --- OPERATIONS ---

    /**
     * Short description of method getCompositionRules
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getCompositionRules()
    {
        $returnValue = array();

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003608 begin
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003608 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getCompositionQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getCompositionQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003634 begin
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003634 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_response_CustomComposite */

?>
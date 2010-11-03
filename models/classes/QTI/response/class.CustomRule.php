<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.CustomRule.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 03.11.2010, 16:26:23 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The QTI_Data class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistance methods.
 * And give the interface for the rendering.
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/**
 * include taoItems_models_classes_QTI_response_ExpressionFactory
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/class.ExpressionFactory.php');

/**
 * include taoItems_models_classes_QTI_response_ResponseProcessing
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interface.ResponseProcessing.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A2-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A2-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A2-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A2-constants end

/**
 * Short description of class taoItems_models_classes_QTI_response_CustomRule
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_CustomRule
    extends taoItems_models_classes_QTI_Data
        implements taoItems_models_classes_QTI_response_ResponseProcessing
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method process
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Response response
     * @param  Outcome score
     * @return boolean
     */
    public function process( taoItems_models_classes_QTI_Response $response,  taoItems_models_classes_QTI_Outcome $score = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002422 begin
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002422 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getRule()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A1B begin
        
        foreach ($this->responseRules as $responseRule){
            $returnValue .= $responseRule->toJSON();
        }
        
        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A1B end

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array responseRules
     * @return mixed
     */
    public function __construct($responseRules)
    {
        // section 127-0-1-1-21b9a9c1:12c0d84cd90:-8000:0000000000002A6B begin
        
        $this->responseRules = $responseRules;
        
        // section 127-0-1-1-21b9a9c1:12c0d84cd90:-8000:0000000000002A6B end
    }

} /* end of class taoItems_models_classes_QTI_response_CustomRule */

?>
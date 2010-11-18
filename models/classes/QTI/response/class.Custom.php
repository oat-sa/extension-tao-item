<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.Custom.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 18.11.2010, 17:06:31 with ArgoUML PHP module 
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
require_once('taoItems/models/classes/QTI/response/class.ResponseProcessing.php');

/**
 * include taoItems_models_classes_QTI_response_Rule
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interface.Rule.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A2-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A2-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A2-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A2-constants end

/**
 * Short description of class taoItems_models_classes_QTI_response_Custom
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_Custom
    extends taoItems_models_classes_QTI_response_ResponseProcessing
        implements taoItems_models_classes_QTI_response_Rule
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute responseRules
     *
     * @access protected
     * @var array
     */
    protected $responseRules = array();

    // --- OPERATIONS ---

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

        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF begin
        
        foreach ($this->responseRules as $responseRule){
            $returnValue .= $responseRule->getRule();
        }
        
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

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
        
        parent::__construct ();
        $this->responseRules = $responseRules;
        
        // section 127-0-1-1-21b9a9c1:12c0d84cd90:-8000:0000000000002A6B end
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-4f28889c:12c5fba49dc:-8000:0000000000002BE8 begin
        
        return $this->getData();
        
        // section 127-0-1-1-4f28889c:12c5fba49dc:-8000:0000000000002BE8 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_response_Custom */

?>
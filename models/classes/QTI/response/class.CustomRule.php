<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.CustomRule.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.09.2010, 10:41:33 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/**
 * include taoItems_models_classes_QTI_response_ResponseProcessing
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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

} /* end of class taoItems_models_classes_QTI_response_CustomRule */

?>
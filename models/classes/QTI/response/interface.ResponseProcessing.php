<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/interface.ResponseProcessing.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 07.09.2010, 15:54:40 with ArgoUML PHP module 
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
 * include taoItems_models_classes_QTI_Item
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Item.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A0-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A0-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A0-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A0-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
interface taoItems_models_classes_QTI_response_ResponseProcessing
{


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
    public function process( taoItems_models_classes_QTI_Response $response,  taoItems_models_classes_QTI_Outcome $score = null);

} /* end of interface taoItems_models_classes_QTI_response_ResponseProcessing */

?>
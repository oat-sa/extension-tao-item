<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 03.08.2010, 13:08:50 with ArgoUML PHP module 
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
 * include taoItems_models_classes_QTI_response_ResponseProcessing
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interface.ResponseProcessing.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A1-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A1-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A1-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A1-constants end

/**
 * Short description of class taoItems_models_classes_QTI_response_Template
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_Template
        implements taoItems_models_classes_QTI_response_ResponseProcessing
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute uri
     *
     * @access protected
     * @var string
     */
    protected $uri = '';

    // --- OPERATIONS ---

    /**
     * Short description of method process
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array correctResponses
     * @param  Score score
     * @return boolean
     */
    public function process($correctResponses,  taoItems_models_classes_QTI_Score $score)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002422 begin
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002422 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return mixed
     */
    public function __construct($uri)
    {
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002426 begin
        
    	$this->uri = $uri;
    	
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002426 end
    }

} /* end of class taoItems_models_classes_QTI_response_Template */

?>
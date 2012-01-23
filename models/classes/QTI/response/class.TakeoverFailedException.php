<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.TakeoverFailedException.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 23.01.2012, 16:45:40 with ArgoUML PHP module 
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
 * Don't generate this class
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/class.Exception.php');

/* user defined includes */
// section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000361D-includes begin
// section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000361D-includes end

/* user defined constants */
// section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000361D-constants begin
// section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000361D-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_TakeoverFailedException
    extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @return mixed
     */
    public function __construct($message = '')
    {
        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000361F begin
        parent::__construct(empty($message) ? 'Impossible to takeover ResponseProcessing for QTI item' : $message);
        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000361F end
    }

} /* end of class taoItems_models_classes_QTI_response_TakeoverFailedException */

?>
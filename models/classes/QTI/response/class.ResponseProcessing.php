<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.ResponseProcessing.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 23.01.2012, 18:11:20 with ArgoUML PHP module 
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
 * The QTI_Data class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistance methods.
 * And give the interface for the rendering.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/* user defined includes */
// section 127-0-1-1--3f4b312:12c5e672d01:-8000:0000000000002BF1-includes begin
// section 127-0-1-1--3f4b312:12c5e672d01:-8000:0000000000002BF1-includes end

/* user defined constants */
// section 127-0-1-1--3f4b312:12c5e672d01:-8000:0000000000002BF1-constants begin
// section 127-0-1-1--3f4b312:12c5e672d01:-8000:0000000000002BF1-constants end

/**
 * Short description of class
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
abstract class taoItems_models_classes_QTI_response_ResponseProcessing
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method create
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return taoItems_models_classes_QTI_response_ResponseProcessing
     */
    public static function create( taoItems_models_classes_QTI_Item $item)
    {
        $returnValue = null;

        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:0000000000003624 begin
        throw new common_Exception('create not implemented for '.get_called_class());
        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:0000000000003624 end

        return $returnValue;
    }

    /**
     * Short description of method takeoverFrom
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  ResponseProcessing responseProcessing
     * @param  Item item
     * @return taoItems_actions_QTIform_ResponseProcessing
     */
    public static function takeoverFrom( taoItems_models_classes_QTI_response_ResponseProcessing $responseProcessing,  taoItems_models_classes_QTI_Item $item)
    {
        $returnValue = null;

        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:0000000000003621 begin
        throw new taoItems_models_classes_QTI_response_TakeoverFailedException('takeoverFrom not implemented for '.get_called_class());
        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:0000000000003621 end

        return $returnValue;
    }

} /* end of abstract class taoItems_models_classes_QTI_response_ResponseProcessing */

?>
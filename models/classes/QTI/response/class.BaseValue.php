<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.BaseValue.php
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
 * include taoItems_models_classes_QTI_response_Expression
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/class.Expression.php');

/* user defined includes */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A77-includes begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A77-includes end

/* user defined constants */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A77-constants begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A77-constants end

/**
 * Short description of class taoItems_models_classes_QTI_response_BaseValue
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_BaseValue
    extends taoItems_models_classes_QTI_response_Expression
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method toJSON
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function toJSON()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A98 begin
        
        $returnValue = $this->value;
        
        // section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A98 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_response_BaseValue */

?>
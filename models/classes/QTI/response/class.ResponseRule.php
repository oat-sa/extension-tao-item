<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.ResponseRule.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 23.01.2012, 17:25:48 with ArgoUML PHP module 
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
 * include taoItems_models_classes_QTI_response_ConditionalExpression
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/class.ConditionalExpression.php');

/**
 * include taoItems_models_classes_QTI_response_Rule
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interface.Rule.php');

/* user defined includes */
// section 127-0-1-1-dbb9044:134e695b13f:-8000:0000000000006283-includes begin
// section 127-0-1-1-dbb9044:134e695b13f:-8000:0000000000006283-includes end

/* user defined constants */
// section 127-0-1-1-dbb9044:134e695b13f:-8000:0000000000006283-constants begin
// section 127-0-1-1-dbb9044:134e695b13f:-8000:0000000000006283-constants end

/**
 * Short description of class taoItems_models_classes_QTI_response_ResponseRule
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
abstract class taoItems_models_classes_QTI_response_ResponseRule
        implements taoItems_models_classes_QTI_response_Rule
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF begin
        throw new common_Exception('class '.get_class($this).' needs to implement getRule', array('TAOITEMS', 'QTI', 'HARD'));
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

        return (string) $returnValue;
    }

} /* end of abstract class taoItems_models_classes_QTI_response_ResponseRule */

?>
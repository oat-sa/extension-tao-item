<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.ExpressionFactory.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 04.11.2010, 16:05:00 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A71-includes begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A71-includes end

/* user defined constants */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A71-constants begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A71-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_ExpressionFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method create
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  SimpleXMLElement data
     * @return core_kernel_classes_Expression
     */
    public function create( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002ADB begin
        
        $expression = null;
        $expressionName = $data->getName();
        
        //retrieve the expression attributes
        $options = array();
        foreach($data->attributes() as $key => $value){
            $options[$key] = $value;
        }
        
        // Create expression function of its type (If specialization has been done for the expression type)
        $expressionClass = 'taoItems_models_classes_QTI_response_'.ucfirst($expressionName);
        
        if (class_exists($expressionClass)){
            $expression = new $expressionClass ($expressionName, $options);
        }
        else {
            $expression = new taoItems_models_classes_QTI_response_ExpressionOperator ($expressionName, $options);
        }
        
        $returnValue = $expression;
        
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002ADB end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_response_ExpressionFactory */

?>
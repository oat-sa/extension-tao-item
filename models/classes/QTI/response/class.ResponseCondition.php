<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.ResponseCondition.php
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

/**
 * include taoItems_models_classes_QTI_response_ConditionalExpression
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/class.ConditionalExpression.php');

/**
 * include taoItems_models_classes_QTI_response_ExpressionOperator
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/class.ExpressionOperator.php');

/**
 * include taoItems_models_classes_QTI_response_Expression
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interface.Expression.php');

/* user defined includes */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A74-includes begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A74-includes end

/* user defined constants */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A74-constants begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A74-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_ResponseCondition
        implements taoItems_models_classes_QTI_response_Expression
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd : 0    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute responseIf
     *
     * @access protected
     * @var Expression
     */
    protected $responseIf = null;

    /**
     * Short description of attribute responseElseIf
     *
     * @access protected
     * @var array
     */
    protected $responseElseIf = array();

    /**
     * Short description of attribute responseElse
     *
     * @access protected
     * @var array
     */
    protected $responseElse = array();

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
        
        // Get the if condition and the associated actions
        $returnValue .= $this->responseIf->getRule(); 
        
        // Get the else if conditions and the associated actions
        foreach ($this->responseElseIf as $responseElseIf){
            $returnValue .= 'else '.$responseElseIf->getRule();
        }
        
        // Get the else actions
        if (!empty($this->responseElse)){
            $returnValue .= 'else {';
            foreach ($this->responseElse as $actions){
                $returnValue .= $actions->getRule ().';';
            }
            $returnValue .= '}';
        }
        
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

        return (string) $returnValue;
    }

    /**
     * Short description of method setResponseIf
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  ConditionalExpression expression
     * @return mixed
     */
    public function setResponseIf( taoItems_models_classes_QTI_response_ConditionalExpression $expression)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE0 begin
        $this->responseIf = $expression;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE0 end
    }

    /**
     * Short description of method setResponseElseIf
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array expressions
     * @return mixed
     */
    public function setResponseElseIf($expressions)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE2 begin
        $this->responseElseIf = $expressions;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE2 end
    }

    /**
     * Short description of method setResponseElse
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array expressions
     * @return mixed
     */
    public function setResponseElse($expressions)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE6 begin
        $this->responseElse = $expressions;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE6 end
    }

} /* end of class taoItems_models_classes_QTI_response_ResponseCondition */

?>
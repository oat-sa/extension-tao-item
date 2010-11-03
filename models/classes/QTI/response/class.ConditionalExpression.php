<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.ConditionalExpression.php
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
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A76-includes begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A76-includes end

/* user defined constants */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A76-constants begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A76-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_ConditionalExpression
    extends taoItems_models_classes_QTI_response_Expression
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute condition
     *
     * @access protected
     * @var Expression
     */
    protected $condition = null;

    /**
     * Short description of attribute actions
     *
     * @access protected
     * @var array
     */
    protected $actions = array();

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

        // section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A96 begin
        // section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A96 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setCondition
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Expression expression
     * @return mixed
     */
    public function setCondition( taoItems_models_classes_QTI_response_Expression $expression)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE9 begin
        $this->condition = $expression;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE9 end
    }

    /**
     * Short description of method setActions
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array expressions
     * @return mixed
     */
    public function setActions($expressions)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AEC begin
        $this->actions = $expressions;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AEC end
    }

    /**
     * Short description of method getCondition
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return taoItems_models_classes_QTI_response_ExpressionFactory
     */
    public function getCondition()
    {
        $returnValue = null;

        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000004907 begin
        $returnValue = $this->condition;        
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000004907 end

        return $returnValue;
    }

    /**
     * Short description of method getActions
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getActions()
    {
        $returnValue = array();

        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000004909 begin
        $returnValue = $this->actions;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000004909 end

        return (array) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_response_ConditionalExpression */

?>
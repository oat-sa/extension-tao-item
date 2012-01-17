<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.ConditionalExpression.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 16.01.2012, 18:27:56 with ArgoUML PHP module 
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
 * include taoItems_models_classes_QTI_expression_CommonExpression
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/expression/class.CommonExpression.php');

/**
 * include taoItems_models_classes_QTI_expression_Expression
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/expression/class.Expression.php');

/**
 * include taoItems_models_classes_QTI_response_ResponseRule
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/class.ResponseRule.php');

/**
 * include taoItems_models_classes_QTI_response_Rule
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interface.Rule.php');

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
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_ConditionalExpression
        implements taoItems_models_classes_QTI_response_Rule
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd : 

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
        
        $returnValue = 'if('.$this->getCondition()->getRule().') {';
        foreach ($this->getActions() as $actions) {
            $returnValue .= $actions->getRule();
        }
        $returnValue .= '}';
        
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Expression condition
     * @param  array actions
     * @return mixed
     */
    public function __construct( taoItems_models_classes_QTI_expression_Expression $condition, $actions)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE9 begin
        $this->condition	= $condition;
        $this->actions		= $actions;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE9 end
    }

    /**
     * Short description of method getCondition
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return taoItems_models_classes_QTI_expression_Expression
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
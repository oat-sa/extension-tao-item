<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.ResponseCondition.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 16.01.2012, 18:16:29 with ArgoUML PHP module 
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
 * include taoItems_models_classes_QTI_response_ConditionalExpression
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/class.ConditionalExpression.php');

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
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A74-includes begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A74-includes end

/* user defined constants */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A74-constants begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A74-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_ResponseCondition
    extends taoItems_models_classes_QTI_response_ResponseRule
        implements taoItems_models_classes_QTI_response_Rule
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd : 0    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute responseIfs
     *
     * @access protected
     * @var array
     */
    protected $responseIfs = array();

    /**
     * Short description of attribute responseElse
     *
     * @access public
     * @var ResponseRule
     */
    public $responseElse = null;

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
        
        // Get the if / elseif conditions and the associated actions
        foreach ($this->responseIfs as $responseElseIf){
            $returnValue .= (empty($returnValue) ? '' : ' else ').$responseElseIf->getRule();
        }
        
        // Get the else actions
        if (!empty($this->responseElse)){
            $returnValue .= 'else {';
            foreach ($this->responseElse as $actions){
                $returnValue .= $actions->getRule();
            }
            $returnValue .= '}';
        }
        
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

        return (string) $returnValue;
    }

    /**
     * Short description of method addResponseIf
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Expression condition
     * @param  array actions
     * @return mixed
     */
    public function addResponseIf( taoItems_models_classes_QTI_expression_Expression $condition, $actions)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE0 begin
        $this->responseIfs[] = new taoItems_models_classes_QTI_response_ConditionalExpression($condition, $actions);
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE0 end
    }

    /**
     * Short description of method setResponseElse
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array actions
     * @return mixed
     */
    public function setResponseElse($actions)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE6 begin
        $this->responseElse = $actions;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AE6 end
    }

} /* end of class taoItems_models_classes_QTI_response_ResponseCondition */

?>
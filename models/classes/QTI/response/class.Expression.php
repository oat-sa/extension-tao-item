<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.Expression.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 03.11.2010, 16:32:51 with ArgoUML PHP module 
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
 * The QTI_Data class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistance methods.
 * And give the interface for the rendering.
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/* user defined includes */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A70-includes begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A70-includes end

/* user defined constants */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A70-constants begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A70-constants end

/**
 * Short description of class taoItems_models_classes_QTI_response_Expression
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_Expression
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute subExpressions
     *
     * @access protected
     * @var array
     */
    protected $subExpressions = array();

    /**
     * Short description of attribute value
     *
     * @access protected
     */
    protected $value = null;

    /**
     * Short description of attribute name
     *
     * @access public
     * @var string
     */
    public $name = '';

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

        // section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A9A begin
        $subExpressionsJSON = array();
        foreach ($this->subExpressions as $subExpression){
            $subExpressionsJSON[] = $subExpression->toJSON();
        }
         
        $returnValue = $this->name.'('.json_encode($this->options)
            . (count($this->subExpressions) ? ', '.implode(',', $subExpressionsJSON) : '').')';
        
        // section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A9A end

        return (string) $returnValue;
    }

    /**
     * Short description of method setSubExpressions
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array expressions
     * @return mixed
     */
    public function setSubExpressions($expressions)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AC7 begin
        $this->subExpressions = $expressions;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AC7 end
    }

    /**
     * Short description of method setValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  value
     * @return mixed
     */
    public function setValue(   $value)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AD8 begin
        $this->value = $value;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AD8 end
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string expressionName
     * @param  array options
     * @return mixed
     */
    public function __construct($expressionName, $options)
    {
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000004900 begin
        parent::__construct (null, $options);
        $this->name = $expressionName;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000004900 end
    }

} /* end of class taoItems_models_classes_QTI_response_Expression */

?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.Expression.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 18.11.2010, 11:03:29 with ArgoUML PHP module 
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

/**
 * include taoItems_models_classes_QTI_response_Rule
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interface.Rule.php');

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
        implements taoItems_models_classes_QTI_response_Rule
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
        
        // Get subExpressions
        $subExpressionsRules = array();
        foreach ($this->subExpressions as $subExpression){
            $subExpressionsRules[] = $subExpression->getRule();
        }
        $subExpressionsJSON = implode(',', $subExpressionsRules);

        // Format options
        $optionsJSON = count($this->options) ? '"'.addslashes(json_encode($this->options)).'"' : 'null';
        
        // Format rule function of the expression operator
        switch ($this->name) {
            case 'correct':
                $returnValue = 'getCorrect("'.$this->options['identifier'].'")';
                break;
            case 'mapResponse':
                $identifier = $this->options['identifier'];
                $returnValue = 'mapResponse('
                    . $optionsJSON
                    .', getMap("'.$identifier.'"), getResponse("'.$identifier.'"))';
                break;
            // Multiple is a Creation of List from parameters
            case 'multiple':
                $returnValue = 'createVariable("{\"type\":\"list\"}", '.$subExpressionsJSON.')';
                break;
            // Null is a Creation of empty BaseTypeVariable
            case 'null':
                $returnValue = 'createVariable(null, null)';
                break;
            // Ordered is a Creation of Tuple from parameters
            case 'ordered':
                $returnValue = 'createVariable("{\"type\":\"tuple\"}", '.$subExpressionsJSON.')';
                break;
            case 'outcome':
                $returnValue = 'getOutcome("'.$this->options['identifier'].'")';
                break;
            case 'setOutcomeValue':
                $returnValue = 'setOutcomeValue("'.$this->options['identifier'].'", '.$subExpressionsJSON.')';
                break;
            case 'variable':
                $returnValue = 'getVariable("'.$this->options['identifier'].'")';
                break;
            
            default:                 
                $returnValue = 
                    $this->name.'('
                        . $optionsJSON
                        . ($subExpressionsJSON!="" ? ', '.$subExpressionsJSON : '')
                    .')';
        }
        
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

        return (string) $returnValue;
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

    /**
     * Short description of method setAttributes
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array attributes
     * @return mixed
     */
    public function setAttributes($attributes)
    {
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AF5 begin
        // @todo not used
        $this->attributes = $attributes;
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AF5 end
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
        // Set the value of the expression and cast it function of the (defined) base type of the variable
        if ($this->options['baseType']){
            switch ($this->options['baseType']){
                case 'boolean':
                    if (is_string ($value)){
                        $this->value = (bool)($value=='true'||$value=='1'?1:0);
                    }else if (is_bool ($value)){
                        $this->value = $value;
                    }else if ($value == null){
                        $this->value = null;
                    }else{
                        throw new Exception ('taoItems_models_classes_QTI_response_ExpressionOperator::setValue : an error occured, the value ['.$value.'] is not a well formed boolean');
                    }
                    break;
                case 'float':
                    $this->value = (float)$value;
                    break;
                case 'integer':
                    $this->value = (int)$value;
                    break;
                case 'identifier':
                case 'string':
                    $this->value = (string)$value;
                    break;
                case 'pair':
                    $this->value = taoItems_models_classes_Matching_VariableFactory::createJSONValueFromQTIData($value, 'pair');
                    break;
                case 'directedPair':
                    $this->value = taoItems_models_classes_Matching_VariableFactory::createJSONValueFromQTIData($value, 'directedPair');
                    break;
            }   
        }
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AD8 end
    }

} /* end of class taoItems_models_classes_QTI_response_Expression */

?>
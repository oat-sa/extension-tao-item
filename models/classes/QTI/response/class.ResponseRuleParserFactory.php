<?php

error_reporting(E_ALL);

/**
 * TAO -
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 23.01.2012, 17:03:12 with ArgoUML PHP module 
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
 * include taoItems_models_classes_QTI_expression_ExpressionParserFactory
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/expression/class.ExpressionParserFactory.php');

/* user defined includes */
// section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062AA-includes begin
// section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062AA-includes end

/* user defined constants */
// section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062AA-constants begin
// section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062AA-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_ResponseRuleParserFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method buildResponseRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_response_ResponseRule
     */
    public static function buildResponseRule( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062AB begin
	    switch ($data->getName()) {
			case 'exitResponse' : $returnValue = new taoItems_models_classes_QTI_response_ExitResponse();
				break;
			case 'setOutcomeValue' :
				$identifier = (string)$data['identifier'];
				$children = array();
				foreach ($data->children() as $child)
					$children[] = $child;
				$expression = taoItems_models_classes_QTI_expression_ExpressionParserFactory::build(array_shift($children));
				$returnValue = new taoItems_models_classes_QTI_response_SetOutcomeVariable($identifier, $expression);
				break;
			case 'responseCondition' :
				$returnValue = self::buildResponseCondition($data);
				break;
			default :
				throw new taoItems_models_classes_QTI_ParsingException('unknwown element '.$data->getName().' in ResponseProcessing');
		}
        // section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062AB end

        return $returnValue;
    }

    /**
     * Short description of method buildResponseCondition
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoItems_models_classes_QTI_response_ResponseCondition
     */
    private static function buildResponseCondition( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062B7 begin
        $responseCondition = new taoItems_models_classes_QTI_response_ResponseCondition();
		
		foreach ($data->children() as $child) {
			switch ($child->getName()) {
				case 'responseIf' :
				case 'responseElseIf' :
					$subchildren = null;
					foreach ($child->children() as $subchild)
						$subchildren[] = $subchild;
		
					// first node is condition
					$conditionNode = array_shift($subchildren);
					$condition = taoItems_models_classes_QTI_expression_ExpressionParserFactory::build($conditionNode);
					
					// all the other nodes are action
					$responseRules = array();
					foreach ($subchildren as $responseRule)
						$responseRules[] = self::buildResponseRule($responseRule);
						
					$responseCondition->addResponseIf($condition, $responseRules);
							
					break;
					
				case 'responseElse' :
					$responseRules = array();
					foreach ($child->children() as $responseRule) {
						$responseRules[] = self::buildResponseRule($responseRule);
					}
					$responseCondition->setResponseElse($responseRules);
					break;
					
				default:
					throw new taoItems_models_classes_QTI_ParsingException('unknown node in ResponseCondition');
			}
		}

		$returnValue = $responseCondition; 
        // section 127-0-1-1-dbb9044:134e695b13f:-8000:00000000000062B7 end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_response_ResponseRuleParserFactory */

?>
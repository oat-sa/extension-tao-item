<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.TemplatesDriven.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 26.01.2012, 10:57:29 with ArgoUML PHP module 
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
 * include taoItems_models_classes_QTI_response_ResponseProcessing
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/class.ResponseProcessing.php');

/**
 * include taoItems_models_classes_QTI_response_Rule
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interface.Rule.php');

/* user defined includes */
// section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF2-includes begin
// section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF2-includes end

/* user defined constants */
// section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF2-constants begin
// section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF2-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_TemplatesDriven
    extends taoItems_models_classes_QTI_response_ResponseProcessing
        implements taoItems_models_classes_QTI_response_Rule
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute templateMap
     *
     * @access private
     * @var array
     */
    private $templateMap = array();

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
		foreach ($this->templateMap as $identifier => $uri){
			
			$templateName = substr($uri, strrpos($uri, '/')+1);
			$matchingTemplate  = dirname(__FILE__).'/rpTemplate/rule.'.$templateName.'.tpl.php';

			$tplRenderer = new taoItems_models_classes_TemplateRenderer(
				$matchingTemplate,
				Array('responseIdentifier' => $identifier, 'outcomeIdentifier'=>'SCORE')
			);    
			$returnValue .= $tplRenderer->render();
        
		}
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

        return (string) $returnValue;
    }

    /**
     * Short description of method isSupportedTemplate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string uri
     * @return taoItems_models_classes_Matching_bool
     */
    public static function isSupportedTemplate($uri)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BFD begin
        
        $mythoMap = Array (QTI_RESPONSE_TEMPLATE_MATCH_CORRECT
            , QTI_RESPONSE_TEMPLATE_MAP_RESPONSE
            , QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT
        );
        
        $returnValue = in_array($uri, $mythoMap);
        
        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BFD end

        return (bool) $returnValue;
    }

    /**
     * Short description of method create
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return taoItems_models_classes_QTI_response_ResponseProcessing
     */
    public static function create( taoItems_models_classes_QTI_Item $item)
    {
        $returnValue = null;

        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000360F begin
		$returnValue = new taoItems_models_classes_QTI_response_TemplatesDriven();
        if(count($item->getOutcomes()) == 0){
			$item->setOutcomes(array(
				new taoItems_models_classes_QTI_Outcome('SCORE', array('baseType' => 'integer', 'cardinality' => 'single'))
			));
		}
		foreach ($item->getInteractions() as $interaction) {
			$returnValue->setTemplate($interaction->getResponse(), QTI_RESPONSE_TEMPLATE_MATCH_CORRECT);
		}
        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000360F end

        return $returnValue;
    }

    /**
     * Short description of method takeOverFrom
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  ResponseProcessing responseProcessing
     * @param  Item item
     * @return taoItems_models_classes_QTI_response_ResponseProcessing
     */
    public static function takeOverFrom( taoItems_models_classes_QTI_response_ResponseProcessing $responseProcessing,  taoItems_models_classes_QTI_Item $item)
    {
        $returnValue = null;

        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:0000000000003615 begin
        if ($responseProcessing instanceof self)
        	return $responseProcessing;
        	
        if ($responseProcessing instanceof taoItems_models_classes_QTI_response_Template) {
			$returnValue = new taoItems_models_classes_QTI_response_TemplatesDriven();
        	// theoretic only interaction 'RESPONSE' should be considered
			foreach ($item->getInteractions() as $interaction) {
				$returnValue->setTemplate($interaction->getResponse(), $responseProcessing->getUri());
			}
	        	
        } else {
        	throw new taoItems_models_classes_QTI_response_TakeoverFailedException();
        }
        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:0000000000003615 end

        return $returnValue;
    }

    /**
     * Short description of method setTemplate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Response response
     * @param  string template
     * @return boolean
     */
    public function setTemplate( taoItems_models_classes_QTI_Response $response, $template)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003696 begin
        $this->templateMap[$response->getIdentifier()] = $template;
        $returnValue = true;
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003696 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getTemplate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Response response
     * @return string
     */
    public function getTemplate( taoItems_models_classes_QTI_Response $response)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:000000000000360D begin
        if (!isset($this->templateMap[$response->getIdentifier()])) {
        	throw new common_Exception('Identifier '.$response->getIdentifier().' unknown to TemplateDriven ResponseProcessing');
        }
        $returnValue = $this->templateMap[$response->getIdentifier()];
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:000000000000360D end

        return (string) $returnValue;
    }

    /**
     * Short description of method takeNoticeOfAddedInteraction
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Interaction interaction
     * @param  Item item
     * @return mixed
     */
    public function takeNoticeOfAddedInteraction( taoItems_models_classes_QTI_Interaction $interaction,  taoItems_models_classes_QTI_Item $item)
    {
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:000000000000365C begin
		$this->setTemplate($interaction->getResponse(), QTI_RESPONSE_TEMPLATE_MATCH_CORRECT);
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:000000000000365C end
    }

    /**
     * Short description of method takeNoticeOfRemovedInteraction
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Interaction interaction
     * @param  Item item
     * @return mixed
     */
    public function takeNoticeOfRemovedInteraction( taoItems_models_classes_QTI_Interaction $interaction,  taoItems_models_classes_QTI_Item $item)
    {
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:000000000000368F begin
        $this->templateMap[$interaction->getResponse()->getIdentifier()];
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:000000000000368F end
    }

    /**
     * Short description of method getForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Response response
     * @return tao_helpers_form_Form
     */
    public function getForm( taoItems_models_classes_QTI_Response $response)
    {
        $returnValue = null;

        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:000000000000368C begin
		$formContainer = new taoItems_actions_QTIform_TemplatesDrivenResponseOptions($this, $response);
        $returnValue = $formContainer->getForm();
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:000000000000368C end

        return $returnValue;
    }

    /**
     * Short description of method buildQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string uri
     * @param  array options
     * @return string
     */
    public function buildQTI($uri, $options)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF7 begin
        
        if (!isset ($options) || !is_array($options)){
            $options = array();
        }
        
        $templateName = substr($uri, strrpos($uri, '/')+1);
        $matchingTemplate  = dirname(__FILE__).'/rpTemplate/qti.'.$templateName.'.tpl.php';

        $tplRenderer = new taoItems_models_classes_TemplateRenderer($matchingTemplate, $options);
        $returnValue = $tplRenderer->render();
        
        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF7 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002C06 begin
		if (count($this->templateMap) == 1) {
			foreach($this->templateMap as $uri){
				$responseProcessingToRender = new taoItems_models_classes_QTI_response_Template($uri);
				common_Logger::i('Exporting using template '.$uri);
				$returnValue = $responseProcessingToRender->toQTI();
			}
		} else {
	        $returnValue = "<responseProcessing>";
	        foreach ($this->templateMap as $identifier => $templateName) {
	        	$returnValue .= $this->buildQTI($templateName, Array(
	                                    'responseIdentifier'=> $identifier
	                                    , 'outcomeIdentifier'=>'SCORE'
	                                ));
	        }
	        $returnValue .= "</responseProcessing>";
		}
        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002C06 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_response_TemplatesDriven */

?>
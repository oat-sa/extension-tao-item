<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.Composite.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 07.02.2012, 17:46:11 with ArgoUML PHP module 
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
 * include
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interactionResponseProcessing/class.InteractionResponseProcessing.php');

/**
 * include taoItems_models_classes_QTI_response_Rule
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interface.Rule.php');

/* user defined includes */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009010-includes begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009010-includes end

/* user defined constants */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009010-constants begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009010-constants end

/**
 * Short description of class taoItems_models_classes_QTI_response_Composite
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
abstract class taoItems_models_classes_QTI_response_Composite
    extends taoItems_models_classes_QTI_response_ResponseProcessing
        implements taoItems_models_classes_QTI_response_Rule
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute components
     *
     * @access protected
     * @var array
     */
    protected $components = array();

    /**
     * Short description of attribute outcomeIdentifier
     *
     * @access protected
     * @var string
     */
    protected $outcomeIdentifier = '';

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
		foreach ($this->components as $irp) {
        	$returnValue .= $irp->getRule();
        }
		foreach ($this->getCompositionRules() as $rule) {
			$returnValue .= $rule->getRule();
		}
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @param  string outcomeIdentifier
     * @return mixed
     */
    public function __construct( taoItems_models_classes_QTI_Item $item, $outcomeIdentifier = 'SCORE')
    {
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003671 begin
        parent::__construct();
        $this->outcomeIdentifier = $outcomeIdentifier;
		$outcomeExists = false;
        foreach ($item->getOutcomes() as $outcome) {
        	if ($outcome->getIdentifier() == $outcomeIdentifier) {
        		$outcomeExists = true;
        		break;
        	}
        }
        if (!$outcomeExists) {
        	$outcomes = $item->getOutcomes();
        	$outcomes[] = new taoItems_models_classes_QTI_Outcome($outcomeIdentifier, array('baseType' => 'integer', 'cardinality' => 'single'));
        	$item->setOutcomes($outcomes);
        }
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003671 end
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

        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:0000000000003612 begin
        $returnValue = new taoItems_models_classes_QTI_response_Summation($item);
        foreach ($item->getInteractions() as $interaction) {
        	$irp = taoItems_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing::create(
        		taoItems_models_classes_QTI_response_interactionResponseProcessing_None::CLASS_ID
        		, $interaction->getResponse()
        		, $item
        	);
			$returnValue->add($irp, $item);
        }
        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:0000000000003612 end

        return $returnValue;
    }

    /**
     * Short description of method takeOverFrom
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  ResponseProcessing responseProcessing
     * @param  Item item
     * @return taoItems_models_classes_QTI_response_Composite
     */
    public static function takeOverFrom( taoItems_models_classes_QTI_response_ResponseProcessing $responseProcessing,  taoItems_models_classes_QTI_Item $item)
    {
        $returnValue = null;

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:00000000000035DC begin
        if ($responseProcessing instanceof static) {
	        // already good
        	$returnValue = $responseProcessing;
        } elseif ($responseProcessing instanceof taoItems_models_classes_QTI_response_Template) {
	        // IMS Template
        	$rp = new taoItems_models_classes_QTI_response_Summation($item, 'SCORE');
        	foreach ($item->getInteractions() as $interaction) {
        		$response = $interaction->getResponse();
        		try {
	        		$irp = taoItems_models_classes_QTI_response_interactionResponseProcessing_Template::createByTemplate(
	        			$responseProcessing->getUri(), $response, $item);
        		} catch (Exception $e) {
        			$rp->destroy();
        			throw new taoItems_models_classes_QTI_response_TakeoverFailedException();
        		}
        		$rp->add($irp, $item);
        	}
        	$returnValue = $rp;
        } elseif ($responseProcessing instanceof taoItems_models_classes_QTI_response_TemplatesDriven) {
	        // TemplateDriven
        	$rp = new taoItems_models_classes_QTI_response_Summation($item, 'SCORE');
        	foreach ($item->getInteractions() as $interaction) {
        		$response = $interaction->getResponse();
        		try {
	        		$irp = taoItems_models_classes_QTI_response_interactionResponseProcessing_Template::createByTemplate(
	        			$responseProcessing->getTemplate($response)
	        			, $response
	        			, $item
	        		);
        		} catch (Exception $e) {
        			$rp->destroy();
        			throw new taoItems_models_classes_QTI_response_TakeoverFailedException();
        		}
	        	$rp->add($irp, $item);
        	}
        	$returnValue = $rp;
        } else {
        	common_Logger::d('Composite ResponseProcessing can not takeover from '.get_class($responseProcessing).' yet');
        	throw new taoItems_models_classes_QTI_response_TakeoverFailedException();
        }
        
	    common_Logger::i('Converted to Composite', array('TAOITEMS', 'QTI'));
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:00000000000035DC end

        return $returnValue;
    }

    /**
     * Short description of method add
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  InteractionResponseProcessing interactionResponseProcessing
     * @return mixed
     */
    public function add( taoItems_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing $interactionResponseProcessing)
    {
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:00000000000035F6 begin
        $this->components[] = $interactionResponseProcessing;
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:00000000000035F6 end
    }

    /**
     * Short description of method getInteractionResponseProcessing
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Response response
     * @return taoItems_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing
     */
    public function getInteractionResponseProcessing( taoItems_models_classes_QTI_Response $response)
    {
        $returnValue = null;

        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000362E begin
        foreach ($this->components as $irp) {
        	if ($irp->getResponse() == $response) {
        		$returnValue = $irp;
        		break;
        	}
        }
        if (is_null($returnValue))
       		throw new common_Exception('No interactionResponseProcessing defined for '.$response->getIdentifier());
        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:000000000000362E end

        return $returnValue;
    }

    /**
     * Short description of method replace
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  InteractionResponseProcessing newInteractionResponseProcessing
     * @return mixed
     */
    public function replace( taoItems_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing $newInteractionResponseProcessing)
    {
        // section 127-0-1-1--409b13b8:1352f8ed821:-8000:00000000000036A9 begin
        $oldkey = null;
        foreach ($this->components as $key => $component) {
        	if ($component->getResponse() == $newInteractionResponseProcessing->getResponse()) {
        		$oldkey = $key;
        		break;
        	}
        }
        if (!is_null($oldkey)) {
        	$this->components[$oldkey]->destroy();
        	unset($this->components[$oldkey]);
        } else {
        	common_Logger::w('Component to be replaced not found', array('TAOITEMS', 'QTI'));
        }
        $this->add($newInteractionResponseProcessing);
        // section 127-0-1-1--409b13b8:1352f8ed821:-8000:00000000000036A9 end
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

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003626 begin
        $returnValue = "<responseProcessing>";
    	foreach ($this->components as $irp) {
        	$returnValue .= $irp->toQTI();
        }
        $returnValue .= $this->getCompositionQTI();
        $returnValue .= "</responseProcessing>";
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003626 end

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
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003662 begin
        $irp = taoItems_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing::create(
        	taoItems_models_classes_QTI_response_interactionResponseProcessing_MatchCorrectTemplate::CLASS_ID,
        	$interaction->getResponse(),
        	$item
        );
        $this->add($irp);
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003662 end
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
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003668 begin
        $irpExisted = false;
        foreach ($this->components as $key => $irp) {
        	if ($irp->getResponse() === $interaction->getResponse()) {
        		unset($this->components[$key]);
        		$irp->destroy();
        		$irpExisted = true;
        		break;
        	}
        }
        if (!$irpExisted) { 
        	common_Logger::w('InstanceResponseProcessing not found for removed interaction '.$interaction->getIdentifier(), array('TAOITEMS', 'QTI'));
        }
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003668 end
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

        // section 127-0-1-1-7fd95e33:1350eecc263:-8000:0000000000003636 begin
        $formContainer = new taoItems_actions_QTIform_CompositeResponseOptions($this, $response);
        $returnValue = $formContainer->getForm();
        // section 127-0-1-1-7fd95e33:1350eecc263:-8000:0000000000003636 end

        return $returnValue;
    }

    /**
     * Short description of method destroy
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function destroy()
    {
        // section 127-0-1-1-40168e54:135573066b9:-8000:0000000000003743 begin
        foreach ($this->components as $component) {
        	$component->destroy();
        }
        parent::destroy();
        // section 127-0-1-1-40168e54:135573066b9:-8000:0000000000003743 end
    }

    /**
     * Short description of method getCompositionQTI
     *
     * @abstract
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public abstract function getCompositionQTI();

    /**
     * Short description of method getCompositionRules
     *
     * @abstract
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public abstract function getCompositionRules();

} /* end of abstract class taoItems_models_classes_QTI_response_Composite */

?>
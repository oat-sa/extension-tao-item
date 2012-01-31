<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/actions/QTIform/class.ResponseProcessingOptions.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 31.01.2012, 17:07:21 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1-249123f:13519689c9e:-8000:0000000000003689-includes begin
// section 127-0-1-1-249123f:13519689c9e:-8000:0000000000003689-includes end

/* user defined constants */
// section 127-0-1-1-249123f:13519689c9e:-8000:0000000000003689-constants begin
// section 127-0-1-1-249123f:13519689c9e:-8000:0000000000003689-constants end

/**
 * Short description of class taoItems_actions_QTIform_ResponseProcessingOptions
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */
abstract class taoItems_actions_QTIform_ResponseProcessingOptions
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute interaction
     *
     * @access protected
     * @var Interaction
     */
    protected $interaction = null;

    /**
     * Short description of attribute responseProcessing
     *
     * @access protected
     * @var ResponseProcessing
     */
    protected $responseProcessing = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Interaction interaction
     * @param  ResponseProcessing responseProcessing
     * @return mixed
     */
    public function __construct( taoItems_models_classes_QTI_Interaction $interaction,  taoItems_models_classes_QTI_response_ResponseProcessing $responseProcessing)
    {
        // section 127-0-1-1--3304025a:135345a8f39:-8000:00000000000036A7 begin
        if(is_null($interaction) || is_null($responseProcessing)){
			throw new common_exception_Error('interaction and responseProcessing cannot be null');
		}
		$this->interaction = $interaction;
		$this->responseProcessing = $responseProcessing;
		parent::__construct(array(), array('option1' => ''));
		
        // section 127-0-1-1--3304025a:135345a8f39:-8000:00000000000036A7 end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        // section 127-0-1-1-249123f:13519689c9e:-8000:000000000000368E begin
		$this->form = tao_helpers_form_FormFactory::getForm('ResponseCodingOptionsForm');
		
		$this->form->setActions(array(), 'bottom');
        // section 127-0-1-1-249123f:13519689c9e:-8000:000000000000368E end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1--3304025a:135345a8f39:-8000:00000000000036AA begin
        $serialElt = tao_helpers_form_FormFactory::getElement('interactionSerial', 'Hidden');
		$serialElt->setValue($this->interaction->getSerial());
		$this->form->addElement($serialElt);
		
        $serialElt = tao_helpers_form_FormFactory::getElement('responseprocessingSerial', 'Hidden');
		$serialElt->setValue($this->responseProcessing->getSerial());
		$this->form->addElement($serialElt);
		// section 127-0-1-1--3304025a:135345a8f39:-8000:00000000000036AA end
    }

} /* end of abstract class taoItems_actions_QTIform_ResponseProcessingOptions */

?>
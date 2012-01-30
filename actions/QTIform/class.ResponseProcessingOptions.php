<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/actions/QTIform/class.ResponseProcessingOptions.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 26.01.2012, 14:41:46 with ArgoUML PHP module 
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

    // --- OPERATIONS ---

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
		$this->form = tao_helpers_form_FormFactory::getForm('ScoreResponseCodingOptionsForm');
		
		$this->form->setActions(array(), 'bottom');
        // section 127-0-1-1-249123f:13519689c9e:-8000:000000000000368E end
    }

} /* end of abstract class taoItems_actions_QTIform_ResponseProcessingOptions */

?>
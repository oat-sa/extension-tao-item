<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.Choice.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 04.10.2010, 12:52:23 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
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
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/**
 * include taoItems_models_classes_QTI_Interaction
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Interaction.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002345-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002345-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002345-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002345-constants end

/**
 * Short description of class taoItems_models_classes_QTI_Choice
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Choice
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toXHTML()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--752f08b1:12b76dcf1f2:-8000:00000000000025B4 begin
        
        $template = self::getTemplatePath() . 'choices/xhtml.' .strtolower($this->type) . '.tpl.php';
        if(!file_exists($template)){
        	 $template = self::getTemplatePath() . 'xhtml.choice.tpl.php';
        }
        
        //get the variables to used in the template
        $variables = array(
        	'identifier'	=> $this->identifier,
        	'type'			=> $this->type,
        	'data'			=> $this->data,
        	'options'		=> $this->options,
        	'rowOptions'	=> json_encode($this->options)
        );
		
		//parse and render the template
		$tplRenderer = new taoItems_models_classes_QTI_TemplateRenderer($template, $variables);
		$returnValue = $tplRenderer->render();
        
        // section 127-0-1-1--752f08b1:12b76dcf1f2:-8000:00000000000025B4 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E1 begin
        
        //check first if there is a template for the given type
        $template = self::getTemplatePath() . 'choices/qti.' .strtolower($this->type) . '.tpl.php';
        if(!file_exists($template)){
        	 $template = self::getTemplatePath() . 'qti.choice.tpl.php';
        }
        
        //get the variables to used in the template
        $variables = array(
        	'identifier'	=> $this->identifier,
        	'type'			=> $this->type,
        	'data'			=> $this->data,
        	'options'		=> $this->options,
        	'rowOptions'	=> $this->xmlizeOptions()
        );
		
		//parse and render the template
		$tplRenderer = new taoItems_models_classes_QTI_TemplateRenderer($template, $variables);
		$returnValue = $tplRenderer->render();
        
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E1 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toForm
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return tao_helpers_form_Form
     */
    public function toForm()
    {
        $returnValue = null;

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:000000000000249B begin
		
		$choiceType = $this->getType();
		$choiceFormClass = 'taoItems_actions_QTIform_choice_'.ucfirst($choiceType);
		if(!class_exists($choiceFormClass)){
			throw new Exception("the class {$choiceFormClass} does not exist");
		}else{
			$formContainer = new $choiceFormClass($this);
			$myForm = $formContainer->getForm();
			$returnValue = $myForm;
		}
		
        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:000000000000249B end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Choice */

?>
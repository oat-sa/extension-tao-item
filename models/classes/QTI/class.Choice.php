<?php

error_reporting(E_ALL);

/**
 * A choice is a kind of interaction's proposition.
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10254
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
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/**
 * A group is an concept to enable choice logical grouping (ordering). 
 * It use when there is distinct choices groups in an interaction.
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('taoItems/models/classes/QTI/class.Group.php');

/**
 * The QTI's interactions are the way the user interact with the system. The
 * will be rendered into widgets to enable the user to answer to the item.
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
 */
require_once('taoItems/models/classes/QTI/class.Interaction.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002345-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002345-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002345-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002345-constants end

/**
 * A choice is a kind of interaction's proposition.
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10254
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Choice
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
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
        	'class'			=> (isset($this->options['class'])) ? $this->options['class'] : '',
        	'rowOptions'	=> json_encode($this->options)
        );
		
		//parse and render the template
		$tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);
		$returnValue = $tplRenderer->render();
        
        // section 127-0-1-1--752f08b1:12b76dcf1f2:-8000:00000000000025B4 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
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
		$tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);
		$returnValue = $tplRenderer->render();
        
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E1 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toForm
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
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
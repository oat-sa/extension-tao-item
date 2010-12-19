<?php

error_reporting(E_ALL);

/**
 * An outcome is a data build in item output. The SCORE is one of the most
 * outcomes.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10091
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
 * The QTI_Item object represent the assessmentItem.
 * It's the main QTI object, it contains all the other objects and is the main
 * point
 * to render a complete item.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#section10042
 */
require_once('taoItems/models/classes/QTI/class.Item.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002347-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002347-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002347-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002347-constants end

/**
 * An outcome is a data build in item output. The SCORE is one of the most
 * outcomes.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10091
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Outcome
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute defaultValue
     *
     * @access protected
     * @var string
     */
    protected $defaultValue = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string identifier
     * @param  array options
     * @return mixed
     */
    public function __construct($identifier = null, $options = array())
    {
        // section 127-0-1-1--1dc66d76:12ce5106c38:-8000:0000000000002951 begin
        
    	if(is_null($identifier)){
    		parent::__construct($identifier, $options);
    	}
    	else{
    		
    		/*
    		 * @todo check the unity of ids by item
    		 */
    		$this->createSerial();
	    	self::$_instances[] = $this->serial;
			$this->identifier 	= $identifier;    	
	    	$this->options 		= $options;
    	}
    	
        // section 127-0-1-1--1dc66d76:12ce5106c38:-8000:0000000000002951 end
    }

    /**
     * Short description of method getDefaultValue
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getDefaultValue()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002418 begin
        
        $returnValue = $this->defaultValue;
        
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002418 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setDefaultValue
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string value
     * @return mixed
     */
    public function setDefaultValue($value)
    {
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:000000000000241A begin
        
    	$this->defaultValue = $value;
    	
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:000000000000241A end
    }

    /**
     * get the outcome in JSON format
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     */
    public function toJSON()
    {
        $returnValue = null;

        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A1D begin
        
        $outcomeValue = null;
        if ($this->defaultValue != '') {
            $outcomeValue = Array($this->defaultValue);
        } else if ($this->options['baseType'] == 'integer' || $this->options['baseType'] == 'float'){
            $outcomeValue = Array(0);
        } else {
            $outcomeValue = null;
        }
        
        $returnValue = taoItems_models_classes_Matching_VariableFactory::createJSONVariableFromQTIData (
    		$this->getIdentifier()
    		, $this->options['cardinality']
    		, $this->options['baseType']
    		, $outcomeValue
    	);
        
        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A1D end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Outcome */

?>
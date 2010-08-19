<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.Parser.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 19.08.2010, 15:22:55 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-26978f63:12a3830d3d3:-8000:000000000000243C-includes begin
// section 127-0-1-1-26978f63:12a3830d3d3:-8000:000000000000243C-includes end

/* user defined constants */
// section 127-0-1-1-26978f63:12a3830d3d3:-8000:000000000000243C-constants begin
// section 127-0-1-1-26978f63:12a3830d3d3:-8000:000000000000243C-constants end

/**
 * Short description of class taoItems_models_classes_QTI_Parser
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Parser
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute file
     *
     * @access protected
     * @var string
     */
    protected $file = '';

    /**
     * Short description of attribute errors
     *
     * @access protected
     * @var array
     */
    protected $errors = array();

    /**
     * Short description of attribute valid
     *
     * @access protected
     * @var boolean
     */
    protected $valid = false;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string file
     * @return mixed
     */
    public function __construct($file)
    {
        // section 127-0-1-1-26978f63:12a3830d3d3:-8000:000000000000243D begin
        
    	$this->file = $file;
    	
        // section 127-0-1-1-26978f63:12a3830d3d3:-8000:000000000000243D end
    }

    /**
     * Short description of method validate
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return boolean
     */
    public function validate()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-26978f63:12a3830d3d3:-8000:0000000000002441 begin
        
        $this->valid = true;
        
        //check file
   		if(!file_exists($this->file)){
    		$this->addError(new Exception("File {$this->file} not found."));
    	}
    	if(!is_readable($this->file)){
    		$this->addError(new Exception("Unable to read file {$this->file}."));
    	}
   		if(!preg_match("/\.xml$/", basename($this->file))){
    		$this->addError(new Exception("Wrong file extension in {$this->file}, xml extension is expected"));
    	}
    	
    	if($this->valid){
    	
    		$this->valid = false;
	    	try{
	    		
	    		libxml_use_internal_errors(true);
	    		
		    	$dom = new DomDocument();
		    	if($dom->load($this->file)){
		    		$this->valid = $dom->schemaValidate(dirname(__FILE__).'/data/imsqti_v2p0.xsd');
		    	}
		    	
		    	if(!$this->valid){
		    		$this->addErrors(libxml_get_errors());
		    	}
		    	libxml_clear_errors();
	    	}
	    	catch(DOMException $de){
	    		$this->addError($de);
	    	}
    	}
    	
    	$returnValue = $this->valid;
    	
        // section 127-0-1-1-26978f63:12a3830d3d3:-8000:0000000000002441 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method load
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return taoItems_models_classes_QTI_Item
     */
    public function load()
    {
        $returnValue = null;

        // section 127-0-1-1-26978f63:12a3830d3d3:-8000:000000000000244A begin
        
        if(!$this->valid){
        	libxml_use_internal_errors(true);	//retrieve errors if no validation has been done previously
        }
        
        //load it using the SimpleXml library
    	$xml = simplexml_load_file($this->file);
    	if($xml !== false){
    		
    		//build the item from the xml
    		$returnValue = taoItems_models_classes_QTI_ParserFactory::buildItem($xml);
    		
    		if(!$this->valid){
    			$this->valid = true;
    			libxml_clear_errors();
    		}
    	}
    	else if(!$this->valid){
    		$this->addErrors(libxml_get_errors());
    		libxml_clear_errors();
    	}
    	
        // section 127-0-1-1-26978f63:12a3830d3d3:-8000:000000000000244A end

        return $returnValue;
    }

    /**
     * Short description of method isValid
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return boolean
     */
    public function isValid()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024B9 begin
        
        $returnValue = $this->valid;
        
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024B9 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method forceValidation
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function forceValidation()
    {
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024C4 begin
        
    	$this->valid = true;
    	
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024C4 end
    }

    /**
     * Short description of method getErrors
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getErrors()
    {
        $returnValue = array();

        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024B0 begin
        
        $returnValue = $this->errors;
        
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024B0 end

        return (array) $returnValue;
    }

    /**
     * Short description of method addError
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  mixed error
     * @return mixed
     */
    protected function addError($error)
    {
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024C7 begin
        
    	$this->valid = false;
    	
    	if($error instanceof Exception){
    		$this->errors = array(
    			'file' 		=> $error->getFile(),
    			'line' 		=> $error->getLine(),
    			'message'	=> $error->getMessage()
    		);
    	}
    	if($error instanceof LibXMLError){
    		$this->errors = array(
    			'file' 		=> $error->file,
    			'line'		=> $error->line,
    			'message'	=> $error->message
    		);
    	}
    	
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024C7 end
    }

    /**
     * Short description of method addErrors
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array errors
     * @return mixed
     */
    protected function addErrors($errors)
    {
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024CA begin
        
    	foreach($errors as $error){
    		$this->addError($error);
    	}
    	
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024CA end
    }

    /**
     * Short description of method clearErrors
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function clearErrors()
    {
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024CD begin
        
    	$this->errors = array();
    	
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024CD end
    }

} /* end of class taoItems_models_classes_QTI_Parser */

?>
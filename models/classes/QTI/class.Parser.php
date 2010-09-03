<?php

error_reporting(E_ALL);

/**
 * The QTI Parser enables you to parse QTI xml files and build the corresponding
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
 * The QTI Parser enables you to parse QTI xml files and build the corresponding
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
     * the path of the qti file
     *
     * @access protected
     * @var string
     */
    protected $file = '';

    /**
     * the list of errors and exception launched by the file parsing
     *
     * @access protected
     * @var array
     */
    protected $errors = array();

    /**
     * The validation status of the file
     *
     * @access protected
     * @var boolean
     */
    protected $valid = false;

    // --- OPERATIONS ---

    /**
     * The constructor needs the path of the QTI file
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
     * Run the validation process
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
     * load the file content, parse it  and build the QTI_Item
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
    		
    		//clean session's previous item 
    		foreach(Session::getAttributeNames() as $key){
    			if(preg_match("/^".taoItems_models_classes_QTI_Data::PREFIX."/", $key)){
    				Session::removeAttribute($key);
    			}
    		}
    		Session::removeAttribute(taoItems_models_classes_QTI_Data::PREFIX . 'identifiers');
    		
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
     * Check the current validation status
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
     * force the validation status to load it instead errors
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
     * get the validation errors
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
     * add a validation error
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  mixed error Exception|LibXMLError
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
     * add validation errors
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
     * clean the validation errors
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
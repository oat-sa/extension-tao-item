<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/class.Parser.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 10.09.2010, 14:52:35 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025A2-includes begin
// section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025A2-includes end

/* user defined constants */
// section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025A2-constants begin
// section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025A2-constants end

/**
 * Short description of class taoItems_models_classes_Parser
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes
 */
class taoItems_models_classes_Parser
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute uri
     *
     * @access protected
     * @var string
     */
    protected $uri = '';

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
     * @param  string uri
     * @return mixed
     */
    public function __construct($uri)
    {
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025B8 begin
        
    	if(!is_file($uri) && !preg_match("/^http/", $uri)){
    		throw new Exception("Only regular file or HTTP(s) url are allowed");
    	}
    	$this->uri = $uri;
    	
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025B8 end
    }

    /**
     * Short description of method validate
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string schema
     * @return boolean
     */
    public function validate($schema = '')
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025BB begin
    	
        $forced = $this->valid;
        
        $this->valid = true;
        
        try{
        	if(is_file($this->uri)){
        		
		    	//check file
		   		if(!file_exists($this->uri)){
		    		throw new Exception("File {$this->uri} not found.");
		    	}
		    	if(!is_readable($this->uri)){
		    		throw new Exception("Unable to read file {$this->uri}.");
		    	}
		   		if(!preg_match("/\.xml$/", basename($this->uri))){
		    		throw new Exception("Wrong file extension in {$this->uri}, xml extension is expected");
		    	}
		   		if(!tao_helpers_File::securityCheck($this->uri)){
		    		throw new Exception("{$this->uri} seems to contain some security issues");
		    	}
        	}
        	else{
        		
        		//only same domain
        		if(!preg_match("/^".preg_quote(BASE_URL, '/')."/", $this->uri)){
        			throw new Exception("The given uri must be in the domain {$_SERVER['HTTP_HOST']}");
        		}
        		
        	}
        }
        catch(Exception $e){
        	if($forced){
        		throw $e;
        	}
        	else{
        		$this->addError($e);
        	}
        }   
             
        if($this->valid && !$forced){	//valida can be true if forceValidation has been called
        	
        	$this->valid = false;

        	try{
	    		
	    		libxml_use_internal_errors(true);
	    		
		    	$dom = new DomDocument();
		    	$loadResult = false;
		    	if(is_file($this->uri)){
		    		$loadResult = $dom->load($this->uri);
		    	}
		    	else{
		    		$xmlContent = tao_helpers_Request::load($this->uri, true);
		    		$loadResult = $dom->loadXML($xmlContent);
		    	}
		    	if($loadResult){
		    		if(!empty($schema)){
		    			$this->valid = $dom->schemaValidate($schema);
		    		}
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
        
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025BB end

        return (bool) $returnValue;
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

        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025C1 begin
        
        $returnValue = $this->valid;
        
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025C1 end

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
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025C3 begin
        
    	$this->valid = true;
    	
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025C3 end
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

        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025C5 begin
        
        $returnValue = $this->errors;
        
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025C5 end

        return (array) $returnValue;
    }

    /**
     * Short description of method displayErrors
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  boolean htmlOutput
     * @return string
     */
    public function displayErrors($htmlOutput = true)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025C7 begin
        
        
    	foreach($this->errors as $error){
			$returnValue .= "{$error['message']} in file {$error['file']}, line {$error['line']}\n";
		}
		
		if($htmlOutput){
			$returnValue = nl2br($returnValue);
		}
        
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025C7 end

        return (string) $returnValue;
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
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025CF begin
        
    	$this->valid = false;
    	
    	if($error instanceof Exception){
    		$this->errors[] = array(
    			'file' 		=> $error->getFile(),
    			'line' 		=> $error->getLine(),
    			'message'	=> "[".get_class($error)."] ".$error->getMessage()
    		);
    	}
    	if($error instanceof LibXMLError){
    		$this->errors[] = array(
    			'file' 		=> $error->file,
    			'line'		=> $error->line,
    			'message'	=> "[".get_class($error)."] ".$error->message
    		);
    	}
    	
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025CF end
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
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025D4 begin
        
   		foreach($errors as $error){
    		$this->addError($error);
    	}
    	
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025D4 end
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
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025D2 begin
        
    	$this->errors = array();
    	
        // section 127-0-1-1-64df0e4a:12af6a1640c:-8000:00000000000025D2 end
    }

} /* end of class taoItems_models_classes_Parser */

?>
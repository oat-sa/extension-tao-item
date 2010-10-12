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

/**
 * include taoItems_models_classes_Parser
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/class.Parser.php');

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
    extends taoItems_models_classes_Parser
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

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
       
    	$returnValue = parent::validate(dirname(__FILE__).'/data/imsqti_v2p0.xsd');
    	
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
        $xml = false;
    	switch($this->sourceType){
    		case self::SOURCE_FILE:
    			$xml = simplexml_load_file($this->source);
    			break;
    		case self::SOURCE_URL:
    			$xmlContent = tao_helpers_Request::load($this->source, true);
    			$xml = simplexml_load_string($xmlContent);
    			break;
    		case self::SOURCE_STRING:
    			$xml = simplexml_load_string($this->source);
    			break;
    	}
    	
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

} /* end of class taoItems_models_classes_QTI_Parser */

?>
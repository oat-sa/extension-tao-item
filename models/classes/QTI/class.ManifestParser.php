<?php

error_reporting(E_ALL);

/**
 * Enables you to parse and validate an imsmanifest.xml file. 
 * You can load a list  QTI_Resources from the parsed file.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_intgv2p0.html#section10003
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Parser enables you to load, parse and validate xml content from an xml
 * Usually used for to load and validate the itemContent  property.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.Parser.php');

/* user defined includes */
// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026E9-includes begin
// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026E9-includes end

/* user defined constants */
// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026E9-constants begin
// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026E9-constants end

/**
 * Enables you to parse and validate an imsmanifest.xml file. 
 * You can load a list  QTI_Resources from the parsed file.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_intgv2p0.html#section10003
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_ManifestParser
    extends tao_models_classes_Parser
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

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

        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026ED begin
        
        $returnValue = parent::validate(dirname(__FILE__).'/data/imscp_v1p1.xsd');
        
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026ED end

        return (bool) $returnValue;
    }

    /**
     * Extract the resources informations about the items 
     * and build a list a QTI_Resource
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function load()
    {
        $returnValue = array();

        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026F1 begin
        
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
    		
    		//get the QTI Item's resources from the imsmanifest.xml
    		$returnValue = taoItems_models_classes_QTI_ParserFactory::getResourcesFromManifest($xml);
    		
    		if(!$this->valid){
    			$this->valid = true;
    			libxml_clear_errors();
    		}
    	}
    	else if(!$this->valid){
    		$this->addErrors(libxml_get_errors());
    		libxml_clear_errors();
    	}
        
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026F1 end

        return (array) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_ManifestParser */

?>
<?php

error_reporting(E_ALL);

/**
 * A resource respresent a QTI item from the point of view of the imsmanifest
 * v1.1 : Content Packaging).
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002700-includes begin
// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002700-includes end

/* user defined constants */
// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002700-constants begin
// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002700-constants end

/**
 * A resource respresent a QTI item from the point of view of the imsmanifest
 * v1.1 : Content Packaging).
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute allowedTypes
     *
     * @access protected
     * @var array
     */
    protected static $allowedTypes = array('imsqti_item_xmlv2p0');

    /**
     * Short description of attribute identifier
     *
     * @access protected
     * @var string
     */
    protected $identifier = '';

    /**
     * Short description of attribute itemFile
     *
     * @access protected
     * @var string
     */
    protected $itemFile = '';

    /**
     * Short description of attribute auxiliaryFiles
     *
     * @access protected
     * @var array
     */
    protected $auxiliaryFiles = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string id
     * @param  string file
     * @return mixed
     */
    public function __construct($id, $file)
    {
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002720 begin
        
    	$this->identifier = $id;
    	$this->itemFile = $file;
    	
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002720 end
    }

    /**
     * Check if the given type is allowed as a QTI Resource
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string type
     * @return boolean
     */
    public static function isAllowed($type)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002724 begin
        
         $returnValue = (!empty($type) && in_array($type, self::$allowedTypes));
        
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002724 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getIdentifier
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getIdentifier()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002708 begin
        
         $returnValue = $this->identifier;
        
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002708 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getItemFile
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getItemFile()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002729 begin
        
         $returnValue = $this->itemFile;
        
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002729 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setAuxiliaryFiles
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array files
     * @return mixed
     */
    public function setAuxiliaryFiles($files)
    {
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:000000000000272B begin
        
    	if(is_array($files)){
    		$this->auxiliaryFiles = $files;
    	}
    	
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:000000000000272B end
    }

    /**
     * Short description of method addAuxiliaryFile
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string file
     * @return mixed
     */
    public function addAuxiliaryFile($file)
    {
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:000000000000272E begin
        
    	$this->auxiliaryFiles[] = $file;
    	 
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:000000000000272E end
    }

    /**
     * Short description of method getAuxiliaryFiles
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getAuxiliaryFiles()
    {
        $returnValue = array();

        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002731 begin
        
       	$returnValue = $this->auxiliaryFiles;
        
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002731 end

        return (array) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Resource */

?>
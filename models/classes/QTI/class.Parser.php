<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - taoItems/models/classes/QTI/class.Parser.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 04.08.2010, 11:11:59 with ArgoUML PHP module 
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

    // --- OPERATIONS ---

    /**
     * Short description of method validate
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string file
     * @return boolean
     */
    protected function validate($file)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-26978f63:12a3830d3d3:-8000:0000000000002441 begin
        
   		if(!file_exists($file)){
    		throw new Exception("Unable to load file $file");
    	}
        
    	$returnValue = true;
    	
        // section 127-0-1-1-26978f63:12a3830d3d3:-8000:0000000000002441 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method load
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string file
     * @return boolean
     */
    public function load($file)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-26978f63:12a3830d3d3:-8000:000000000000244A begin
        
        //validate the file
        if(!$this->validate($file)){
    		throw new Exception("$file is not a valid QTI item file.");
        }
        
        //load it using the SimpleXml library
    	$xml = simplexml_load_file($file);
    	
    	//register the default namespace
    	$namespaces = $xml->getDocNamespaces();
    	$defaultNamespace = 'qti';
    	$xml->registerXPathNamespace($defaultNamespace, $namespaces['']); 
    	
    	//build the item from the xml
    	$item = taoItems_models_classes_QTI_Factory::buildItem($xml, $defaultNamespace);
       
        
        // section 127-0-1-1-26978f63:12a3830d3d3:-8000:000000000000244A end

        return (bool) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Parser */

?>
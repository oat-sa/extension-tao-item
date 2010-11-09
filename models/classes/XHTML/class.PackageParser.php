<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/XHTML/class.PackageParser.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.11.2010, 17:43:14 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_XHTML
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
// section 127-0-1-1-2d0bb0b3:12c2c41fb7c:-8000:0000000000002869-includes begin
// section 127-0-1-1-2d0bb0b3:12c2c41fb7c:-8000:0000000000002869-includes end

/* user defined constants */
// section 127-0-1-1-2d0bb0b3:12c2c41fb7c:-8000:0000000000002869-constants begin
// section 127-0-1-1-2d0bb0b3:12c2c41fb7c:-8000:0000000000002869-constants end

/**
 * Short description of class taoItems_models_classes_XHTML_PackageParser
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_XHTML
 */
class taoItems_models_classes_XHTML_PackageParser
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

        // section 127-0-1-1-2d0bb0b3:12c2c41fb7c:-8000:000000000000286A begin
                
        $forced = $this->valid;
        $this->valid = true;
        
        try{
        	switch($this->sourceType){
        		case self::SOURCE_FILE:
	        		//check file
			   		if(!file_exists($this->source)){
			    		throw new Exception("File {$this->source} not found.");
			    	}
			    	if(!is_readable($this->source)){
			    		throw new Exception("Unable to read file {$this->source}.");
			    	}
			   		if(!preg_match("/\.zip$/", basename($this->source))){
			    		throw new Exception("Wrong file extension in {$this->source}, zip extension is expected");
			    	}
			   		if(!tao_helpers_File::securityCheck($this->source)){
			    		throw new Exception("{$this->source} seems to contain some security issues");
			    	}
			    	break;
        		default:
	        		throw new Exception("Only regular files are allowed as package source");
	        		break;
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
	    		$zip = new ZipArchive();
	    		//check the archive opening and the consistency
				if($zip->open($this->source, ZIPARCHIVE::CHECKCONS) !== true){
					throw new Exception($zip->getStatusString());
				}
				else{
					//check if the manifest is there
					if($zip->locateName("index.html") === false){
						throw new Exception("A QTI package must contains a imsmanifest.xml file");
					}
					
					$this->valid = true;
				}
				$zip->close();
			}
			catch(Exception $e){
				$this->addError($e);
				$zip->close();
			}
        }
    	$returnValue = $this->valid;
        
        // section 127-0-1-1-2d0bb0b3:12c2c41fb7c:-8000:000000000000286A end

        return (bool) $returnValue;
    }

    /**
     * Short description of method extract
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function extract()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-2d0bb0b3:12c2c41fb7c:-8000:000000000000286C begin
        
    	if(!is_file($this->source)){	//ultimate verification
        	throw new Exception("Wrong source mode");
        }
        
        $sourceFile = basename($this->source);
        $folder = dirname($this->source) . '/' . substr($sourceFile, 0, strrpos($sourceFile, '.'));
        
        if(!is_dir($folder)){
        	mkdir($folder);
        }
        
	    $zip = new ZipArchive();
		if ($zip->open($this->source) === true) {
		    if($zip->extractTo($folder)){
		    	$returnValue = $folder;
		    }
		    $zip->close();
		}
        
        // section 127-0-1-1-2d0bb0b3:12c2c41fb7c:-8000:000000000000286C end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_XHTML_PackageParser */

?>
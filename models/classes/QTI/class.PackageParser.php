<?php

error_reporting(E_ALL);

/**
 * Enables you to parse and validate a QTI Package.
 * The Package is formated as a zip archive containing the manifest and the
 * (item files and media files)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('tao/models/classes/class.Parser.php');

/* user defined includes */
// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026EB-includes begin
// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026EB-includes end

/* user defined constants */
// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026EB-constants begin
// section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026EB-constants end

/**
 * Enables you to parse and validate a QTI Package.
 * The Package is formated as a zip archive containing the manifest and the
 * (item files and media files)
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_intgv2p0.html#section10003
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_PackageParser
    extends tao_models_classes_Parser
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method validate
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string schema
     * @return boolean
     */
    public function validate($schema = '')
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026EF begin
        
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
					if($zip->locateName("imsmanifest.xml") === false){
						throw new Exception("A QTI package must contains a imsmanifest.xml file  at the root of the archive");
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
        
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026EF end

        return (bool) $returnValue;
    }

    /**
     * Short description of method extract
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function extract()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026F3 begin
        
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
        
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:00000000000026F3 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_PackageParser */

?>
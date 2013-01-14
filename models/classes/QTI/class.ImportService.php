<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.ImportService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 09.01.2013, 11:31:43 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-30-1--78--1c871595:13c064de577:-8000:0000000000003C9E-includes begin
// section 10-30-1--78--1c871595:13c064de577:-8000:0000000000003C9E-includes end

/* user defined constants */
// section 10-30-1--78--1c871595:13c064de577:-8000:0000000000003C9E-constants begin
// section 10-30-1--78--1c871595:13c064de577:-8000:0000000000003C9E-constants end

/**
 * Short description of class taoItems_models_classes_QTI_ImportService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_ImportService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method importQTIFile
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string qtiFile
     * @param  Class itemClass
     * @param  boolean validate
     * @param  Repository repository
     * @return core_kernel_classes_Resource
     */
    public function importQTIFile($qtiFile,  core_kernel_classes_Class $itemClass, $validate = true,  core_kernel_versioning_Repository $repository = null)
    {
        $returnValue = null;

        // section 10-30-1--78--1c871595:13c064de577:-8000:0000000000003CAC begin
    	//repository
		$repository = is_null($repository)
			? tao_models_classes_FileSourceService::singleton()->getDefaultFileSource()
			: $repository;
			
        //get the services instances we will need
		$itemService	= taoItems_models_classes_ItemsService::singleton();
		$qtiService 	= taoItems_models_classes_QTI_Service::singleton();
	
		if(!$itemService->isItemClass($itemClass)){
			throw new common_exception_Error('provided non Itemclass for '.__FUNCTION__);
		}
				
		//validate the file to import
		$qtiParser = new taoItems_models_classes_QTI_Parser($qtiFile);
		
		$qtiParser->validate();
		if(!$qtiParser->isValid() && !$validate){
			throw new taoItems_models_classes_QTI_ParsingException(implode(',', $qtiParser->getErrors()));
		}

		//load the QTI item from the file
		$qtiItem = $qtiParser->load();
		if(is_null($qtiItem)){
			throw new common_Exception('QTI item could not be loaded');
		}
		
		//create the instance
		// @todo add type and repository
		$rdfItem = $itemService->createInstance($itemClass);
		
		if(is_null($rdfItem)){
			throw new common_exception_Error('Unable to create instance of '.$itemClass->getUri());
		}
		
		//set the QTI type
		$rdfItem->setPropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_QTI);
		
		//set the label
		$rdfItem->setLabel($qtiItem->getOption('title'));
		
		//save itemcontent
		if ($qtiService->saveDataItemToRdfItem($qtiItem, $rdfItem)) {
			$returnValue = $rdfItem;
		}
        // section 10-30-1--78--1c871595:13c064de577:-8000:0000000000003CAC end

        return $returnValue;
    }

    /**
     * imports a qti package and
     * returns the number of items imported
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string file
     * @param  Class itemClass
     * @param  boolean validate
     * @param  Repository repository if none provided uses default repository
     * @return int
     */
    public function importQTIPACKFile($file,  core_kernel_classes_Class $itemClass, $validate = true,  core_kernel_versioning_Repository $repository = null)
    {
        $returnValue = (int) 0;

        // section 10-30-1--78--1c871595:13c064de577:-8000:0000000000003C9F begin
			
		//get the services instances we will need
		$itemService	= taoItems_models_classes_ItemsService::singleton();
		$qtiService 	= taoItems_models_classes_QTI_Service::singleton();
		
		//repository
		$repository = is_null($repository)
			? tao_models_classes_FileSourceService::singleton()->getDefaultFileSource()
			: $repository;
		
		//load and validate the package
		$qtiPackageParser = new taoItems_models_classes_QTI_PackageParser($file);
		$qtiPackageParser->validate();

		if(!$qtiPackageParser->isValid() && !$validate){
			throw new taoItems_models_classes_QTI_ParsingException();
		}
		
		//extract the package
		$folder = $qtiPackageParser->extract();
		if(!is_dir($folder)){
			throw new taoItems_models_classes_QTI_exception_ExtractException();
		}
		
			
		//load and validate the manifest
		$qtiManifestParser = new taoItems_models_classes_QTI_ManifestParser($folder .'/imsmanifest.xml');
		$qtiManifestParser->validate();
		
		if(!$qtiManifestParser->isValid() && !$validate){
			tao_helpers_File::delTree($folder);
			throw new taoItems_models_classes_QTI_ParsingException();
		}
			
		$itemModelProperty = new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY);
		
		//load the information about resources in the manifest 
		$resources = $qtiManifestParser->load();
		$importedItems = 0;
		foreach($resources as $qtiResource){
			try {
				$qtiFile = $folder .DIRECTORY_SEPARATOR. $qtiResource->getItemFile();
				$rdfItem = $this->importQTIFile($qtiFile, $itemClass, $validate, $repository);
				$itemPath = taoItems_models_classes_ItemsService::singleton()->getItemFolder($rdfItem);
				
				foreach($qtiResource->getAuxiliaryFiles() as $auxResource){
					// $auxResource is a relativ URL, so we need to replace the slashes with directory separators
					$auxPath = $folder .DIRECTORY_SEPARATOR. str_replace('/', DIRECTORY_SEPARATOR, $auxResource);
					$relPath = helpers_File::getRelPath($qtiFile, $auxPath);
					$destPath = $itemPath.$relPath;
					tao_helpers_File::copy($auxPath, $destPath, true);
				}
				$importedItems++;
				
			} catch (Exception $e) {
				// an error occured during a specific item
				// skip to next
			}
		}
		// cleanup
		tao_helpers_File::delTree($folder);
		
		$returnValue = $importedItems;
        // section 10-30-1--78--1c871595:13c064de577:-8000:0000000000003C9F end

        return (int) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_ImportService */

?>
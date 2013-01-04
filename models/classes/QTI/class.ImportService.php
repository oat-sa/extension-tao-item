<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.ImportService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 04.01.2013, 18:09:35 with ArgoUML PHP module 
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
     * @return core_kernel_classes_Resource
     */
    public function importQTIFile($qtiFile,  core_kernel_classes_Class $itemClass, $validate = true)
    {
        $returnValue = null;

        // section 10-30-1--78--1c871595:13c064de577:-8000:0000000000003CAC begin
    	
        //get the services instances we will need
		$itemService	= taoItems_models_classes_ItemsService::singleton();
		$qtiService 	= taoItems_models_classes_QTI_Service::singleton();
	
		//validate the file to import
		$qtiParser = new taoItems_models_classes_QTI_Parser($qtiFile);
		
		$qtiParser->validate();
		if(!$qtiParser->isValid() && !$validate){
			throw new taoItems_models_classes_QTI_ParsingException(implode(',', $qtiParser->getErrors()));
		}

		if(!$itemService->isItemClass($itemClass)){
			throw new common_exception_Error('provided non Itemclass for '.__FUNCTION__);
		}
				
		//load the QTI item from the file
		$qtiItem = $qtiParser->load();
		if(is_null($qtiItem)){
			throw new common_Exception('QTI item could not be loaded');
		}
		
		//create the instance
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
     * @return int
     */
    public function importQTIPACKFile($file,  core_kernel_classes_Class $itemClass, $validate = true)
    {
        $returnValue = (int) 0;

        // section 10-30-1--78--1c871595:13c064de577:-8000:0000000000003C9F begin
			
		//get the services instances we will need
		$itemService	= taoItems_models_classes_ItemsService::singleton();
		$qtiService 	= taoItems_models_classes_QTI_Service::singleton();
		
		//test versioning
		$versioning = false;

		
		//load and validate the package
		$qtiPackageParser = new taoItems_models_classes_QTI_PackageParser($file);
		$qtiPackageParser->validate();

		if(!$qtiPackageParser->isValid() && !$validate){
			$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
			$this->setData('importErrors', $qtiPackageParser->getErrors());
			return false;
		}
		
		//extract the package
		$folder = $qtiPackageParser->extract();
		if(!is_dir($folder)){
			$this->setData('importErrorTitle', __('An error occured during the import'));
			$this->setData('importErrors', array(array('message' => __('unable to extract archive content, please check your tmp dir'))));
			return false;
		}
		
			
		//load and validate the manifest
		$qtiManifestParser = new taoItems_models_classes_QTI_ManifestParser($folder .'/imsmanifest.xml');
		$qtiManifestParser->validate();
		
		if(!$qtiManifestParser->isValid() && !$validate){
			$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
			$this->setData('importErrors', $qtiManifestParser->getErrors());
			return false;
		}
			
		$itemModelProperty = new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY);
		
		//load the information about resources in the manifest 
		$resources = $qtiManifestParser->load();
		$importedItems = 0;
		foreach($resources as $resource){
			if($resource instanceof taoItems_models_classes_QTI_Resource){
			
				//create a new item in the model
				$rdfItem = $itemService->createInstance($itemClass, $resource->getIdentifier());
				
				$qtiItem = null;
				try{//load the QTI_Item from the item file
					$qtiItem = $qtiService->loadItemFromFile($folder . '/'. $resource->getItemFile());
				}
				catch(Exception $e){
					
					$this->setData('importErrorTitle', __('An error occured during the import'));
					
					// The QTI File at $folder/$resource->itemFile cannot be loaded.
					// Is this because 
					// - the file referenced by the manifest does not exists in the archive ?
					// - the file exists but cannot be parsed ?
					if(file_exists($folder . '/' . $resource->getItemFile())){
						$this->setData('importErrors', array(array('message' => $e->getMessage())));
					}
					else{
						$this->setData('importErrors', array(array('message' => sprintf(__("Unable to load QTI resource with href = '%s'"), $resource->getItemFile()))));
					}
					
					// An error occured. We should rollback the knowledge base for this item.
					$rdfItem->delete();
					break;
				}
				
				if(is_null($qtiItem) || is_null($rdfItem)){
					$this->setData('importErrorTitle', __('An error occured during the import'));
					$this->setData('importErrors', array(array('message' => __('Unable to create the item for the content '.$resource->getIdentifier().' , from file '.$resource->getItemFile()))));
					
					// An error occured. We should rollback the knowledge base.
					$rdfItem->delete();
					if(!$validate){
						break;
					}
				}
				else{
					//set the QTI type
					$rdfItem->setPropertyValue($itemModelProperty, TAO_ITEM_MODEL_QTI);
					
					//set the file in the itemContent
					if($qtiService->saveDataItemToRdfItem($qtiItem, $rdfItem, 'HOLD_COMMIT')){
						
						$subpath = preg_quote(dirname($resource->getItemFile()), '/');
						
						//and copy the others resources in the runtime path
						$itemPath = $itemService->getItemFolder($rdfItem);
						
						foreach($resource->getAuxiliaryFiles() as $auxResource){
							$auxPath = $auxResource;
							if(preg_match("/^i[0-9]*/", $subpath)){
								$auxPath = preg_replace("/^$subpath\//", '', $auxResource);
							}
							tao_helpers_File::copy($folder . '/'. $auxResource, $itemPath.'/'.$auxPath, true);
						}
						
						if ($versioning) {
							// add to repo
							$itemContent = $rdfItem->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
							$versionedContend = $repository->add($itemContent);
							if (!is_null($versionedContend)) {
								if ($versionedContend->getUri() != $itemContent->getUri()) {
									$rdfItem->editPropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY), $versionedContend);
								}
								$importedItems++;
							}
						}else{
							$importedItems++;	//item is considered as imported there 
						}
					}
				}
			}
		}
		
		$returnValue = count($resources);
        // section 10-30-1--78--1c871595:13c064de577:-8000:0000000000003C9F end

        return (int) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_ImportService */

?>
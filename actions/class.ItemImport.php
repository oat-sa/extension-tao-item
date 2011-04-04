<?php
/**
 * This controller provide the actions to import items 
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @subpackage action
 *
 */
class taoItems_actions_ItemImport extends tao_actions_Import {

	/**
	 * Constructor used to override the formContainer
	 */
	public function __construct(){
		parent::__construct();
		$this->formContainer = new taoItems_actions_form_Import();
	}
	
	/**
	 * action to perform on a posted QTI file
	 * @param array $formValues the posted data
	 * @return boolean
	 */
	protected function importQTIFile($formValues){
		if(isset($formValues['source']) && $this->hasSessionAttribute('classUri')){
			
			//get the item parent class
			$clazz = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getSessionAttribute('classUri')));
		
			//get the services instances we will need
			$itemService	= tao_models_classes_ServiceFactory::get('items');
			$qtiService 	= tao_models_classes_ServiceFactory::get('taoItems_models_classes_QTI_Service');
		
			$uploadedFile = $formValues['source']['uploaded_file'];
			
			$forceValid = false;
			if(isset($formValues['disable_validation'])){
				if(is_array($formValues['disable_validation'])){
					$forceValid = true;
				}
			} 
			
			//validate the file to import
			$qtiParser = new taoItems_models_classes_QTI_Parser($uploadedFile);
			
			$qtiParser->validate();
			if(!$qtiParser->isValid() && !$forceValid){
				$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
				$this->setData('importErrors', $qtiParser->getErrors());
			}
			else{
				//create a new item instance of the clazz
				if($itemService->isItemClass($clazz)){
					
					//load the QTI item from the file
					$qtiItem = $qtiParser->load();
					if(!is_null($qtiItem)){
					
						//create the instance
						$rdfItem = $itemService->createInstance($clazz);
						
						if(!is_null($rdfItem)){
							//set the QTI type
							$rdfItem->setPropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_QTI);
							
							if($qtiService->saveDataItemToRdfItem($qtiItem, $rdfItem)){
								
								$this->removeSessionAttribute('classUri');
								$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($rdfItem->uriResource));
								$this->setData('message', __('Item imported successfully') . ' : ' .$rdfItem->getLabel());
								$this->setData('reload', true);
								
								@unlink($uploadedFile);
								
								return true;
							}
						}
					}
				}
				$this->setData('message', __('An error occurs during the import'));
			}
			return false;
		}
	}
	
	/**
	 * action to perform on a posted QTI CP file
	 * @param array $formValues the posted data
	 */
	protected function importQTIPACKFile($formValues){
		if(isset($formValues['source']) && $this->hasSessionAttribute('classUri')){
			
			set_time_limit(200);	//the zip extraction is a long process that can exced the 30s timeout
			
			//get the item parent class
			$clazz = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getSessionAttribute('classUri')));
			
			//get the services instances we will need
			$itemService	= tao_models_classes_ServiceFactory::get('items');
			$qtiService 	= tao_models_classes_ServiceFactory::get('taoItems_models_classes_QTI_Service');
			
			$uploadedFile = $formValues['source']['uploaded_file'];
			
			$forceValid = false;
			if(isset($formValues['disable_validation'])){
				if(is_array($formValues['disable_validation'])){
					$forceValid = true;
				}
			} 
			//load and validate the package
			$qtiPackageParser = new taoItems_models_classes_QTI_PackageParser($uploadedFile);
			$qtiPackageParser->validate();

			if(!$qtiPackageParser->isValid() && !$forceValid){
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
			
			if(!$qtiManifestParser->isValid() && !$forceValid){
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
					$rdfItem = $itemService->createInstance($clazz, $resource->getIdentifier());
					
					$qtiItem = null;
					try{//load the QTI_Item from the item file
						$qtiItem = $qtiService->loadItemFromFile($folder . '/'. $resource->getItemFile());
					}
					catch(taoItems_models_classes_QTI_ParsingException $pe){
						if(!$forceValid){
							$this->setData('importErrors', array(array('message' => $pe->getMessage())));
						}
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
						if(!$forceValid){
							break;
						}
					}
					else{
						//set the QTI type
						$rdfItem->setPropertyValue($itemModelProperty, TAO_ITEM_MODEL_QTI);
						
						//set the file in the itemContent
						if($qtiService->saveDataItemToRdfItem($qtiItem, $rdfItem)){
							
							$folderName = substr($rdfItem->uriResource, strpos($rdfItem->uriResource, '#') + 1);
							
							$importedItems++;	//item is considered as imported there 
							
							//and copy the others resources in the runtime path
							foreach($resource->getAuxiliaryFiles() as $auxResource){
								tao_helpers_File::copy($folder . '/'. $auxResource, BASE_PATH. "/data/$folderName/$auxResource", true);
							}
						}
					}
				}
			}
			
			$totalItems = count($resources);
			
			if($totalItems == $importedItems && $totalItems > 0){
				
				$this->removeSessionAttribute('classUri');
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($rdfItem->uriResource));
				$this->setData('message', $importedItems . ' ' . __('items imported successfully'));
				$this->setData('reload', true);
				
				tao_helpers_File::remove($uploadedFile);
				tao_helpers_File::remove(str_replace('.zip', '', $uploadedFile), true);
				
				return true;
			}
			else{
				// Display how many items were imported.
				$this->setData('message', sprintf(__('%d items of %d imported successfuly'), $importedItems, $totalItems));
			}
		}
		return false;
	}

	/**
	 * 
	 * @param array $formValues
	 * @return boolean
	 */
	protected function importXHTMLFile($formValues){
		if(isset($formValues['source']) && $this->hasSessionAttribute('classUri')){
			
			set_time_limit(200);	//the zip extraction is a long process that can exced the 30s timeout
			
			//get the item parent class
			$clazz = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getSessionAttribute('classUri')));
			
			//get the services instances we will need
			$itemService	= tao_models_classes_ServiceFactory::get('items');
			
			$uploadedFile = $formValues['source']['uploaded_file'];
			
			$forceValid = false;
			if(isset($formValues['disable_validation'])){
				if(is_array($formValues['disable_validation'])){
					$forceValid = true;
				}
			}
			
			//load and validate the package
			$packageParser = new taoItems_models_classes_XHTML_PackageParser($uploadedFile);
			$packageParser->validate();

			if(!$packageParser->isValid()){
				$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
				$this->setData('importErrors', $packageParser->getErrors());
				return false;
			}
			
		
			//extract the package
			$folder = $packageParser->extract();
			if(!is_dir($folder)){
				$this->setData('importErrorTitle', __('An error occured during the import'));
				$this->setData('importErrors', array(array('message' => __('unable to extract archive content, please check your tmp dir'))));
				return false;
			}
				
			//load and validate the manifest
			$fileParser = new tao_models_classes_Parser($folder .'/index.html', array('extension' => 'html'));
			$fileParser->validate(BASE_PATH.'/models/classes/data/xhtml/xhtml.xsd');
			
			if(!$fileParser->isValid() && !$forceValid){
				$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
				$this->setData('importErrors', $fileParser->getErrors());
				return false;
			}
				
			//create a new item in the model
			$rdfItem = $itemService->createInstance($clazz);
			//set the QTI type
			$rdfItem->setPropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_XHTML);
			
			$itemContent = file_get_contents($folder .'/index.html');
			
			$folderName = substr($rdfItem->uriResource, strpos($rdfItem->uriResource, '#') + 1);
        	$itemPath = BASE_PATH."/data/{$folderName}";
        	if(!tao_helpers_File::move($folder, $itemPath)){
        		$this->setData('importErrorTitle', __('Unable to copy the resources'));
				$this->setData('importErrors', array(array('message' => __('Unable to move')." $folder to $itemPath")));
				return false;
        	}
        	
        	$itemService->setItemContent($rdfItem, $itemContent);
						
			$this->removeSessionAttribute('classUri');
			$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($rdfItem->uriResource));
			$this->setData('message',__('item imported successfully'));
			$this->setData('reload', true);
			
			//remove the temp files
			tao_helpers_File::remove($uploadedFile);
			tao_helpers_File::remove(str_replace('.zip', '', $uploadedFile), true);
			
			return true;
		}
		return false;
	}
}
?>
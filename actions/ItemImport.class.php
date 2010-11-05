<?php
require_once('tao/actions/Import.class.php');

/**
 * This controller provide the actions to import items 
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @subpackage action
 *
 */
class ItemImport extends Import {

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
			
			//validate the file to import
			$qtiParser = new taoItems_models_classes_QTI_Parser($uploadedFile);
			$qtiParser->validate();

			if(!$qtiParser->isValid()){
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
			
			//load and validate the package
			$qtiPackageParser = new taoItems_models_classes_QTI_PackageParser($uploadedFile);
			$qtiPackageParser->validate();

			if(!$qtiPackageParser->isValid()){
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
			
			if(!$qtiManifestParser->isValid()){
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
					
					try{//load the QTI_Item from the item file
						$qtiItem = $qtiService->loadItemFromFile($folder . '/'. $resource->getItemFile());
					}
					catch(Exception $e){
						$this->setData('importErrorTitle', __('An error occured during the import'));
						$this->setData('importErrors', array(array('message' => $e->getMessage())));
						break;
					}
					
					if(is_null($qtiItem) || is_null($rdfItem)){
						$this->setData('importErrorTitle', __('An error occured during the import'));
						$this->setData('importErrors', array(array('message' => __('unable to create for imported content'))));
						break;
					}
					//set the QTI type
					$rdfItem->setPropertyValue($itemModelProperty, TAO_ITEM_MODEL_QTI);
					
					//set the file in the itemContent
					if($qtiService->saveDataItemToRdfItem($qtiItem, $rdfItem)){
						
						$deployParams = array(
							'delivery_server_mode'	=> false
						);
						
						$folderName = substr($rdfItem->uriResource, strpos($rdfItem->uriResource, '#') + 1);
        				$itemPath = BASE_PATH."/views/runtime/{$folderName}/index.html";
						if(!is_dir(dirname($itemPath))){
		        			mkdir(dirname($itemPath));
		        		}
		        		$itemUrl = BASE_WWW . "runtime/{$folderName}/index.html";
						
						//we deploy it
						if(!$itemService->deployItem($rdfItem, $itemPath, $itemUrl, $deployParams)){
							$this->setData('importErrorTitle', __('An error occured during the import'));
							$this->setData('importErrors', array(array('message' => __('unable to deploy item'))));
							break;
						}
						
						$importedItems++;	//item is considered as imported there 
						
						//and copy the others resources in the runtime path
						foreach($resource->getAuxiliaryFiles() as $auxResource){
							tao_helpers_File::copy($folder . '/'. $auxResource, dirname($itemPath) . '/'. $auxResource, true);
						}
						
					}
				}
			}
			if(count($resources) == $importedItems){
				
				$this->removeSessionAttribute('classUri');
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($rdfItem->uriResource));
				$this->setData('message', $importedItems . ' ' . __('items imported successfully'));
				$this->setData('reload', true);
				
				tao_helpers_File::remove($uploadedFile);
				tao_helpers_File::remove(str_replace('.zip', '', $uploadedFile), true);
				
				return true;
			}
		}
		return false;
	}
}
?>
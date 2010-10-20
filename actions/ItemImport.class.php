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
			
			//get the item parent class
			$clazz = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getSessionAttribute('classUri')));
			
			//get the services instances we will need
			$testService	= tao_models_classes_ServiceFactory::get('tests');
			$itemService	= tao_models_classes_ServiceFactory::get('items');
			$qtiService 	= tao_models_classes_ServiceFactory::get('taoItems_models_classes_QTI_Service');
			
			$uploadedFile = $formValues['source']['uploaded_file'];
			
			//load and validate the package
			$qtiPackageParser = new taoTests_models_classes_QTI_PackageParser($uploadedFile);
			$qtiPackageParser->validate();

			if(!$qtiPackageParser->isValid()){
				$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
				$this->setData('importErrors', $qtiPackageParser->getErrors());
			}
			else{
				
				//extract the package
				$folder = $qtiPackageParser->extract();
				if(is_dir($folder)){
					
					//load and validate the manifest
					$qtiManifestParser = new taoTests_models_classes_QTI_ManifestParser($folder .'/imsmanifest.xml');
					$qtiManifestParser->validate();
					
					if(!$qtiManifestParser->isValid()){
						$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
						$this->setData('importErrors', $qtiManifestParser->getErrors());
					}
					else{
						
						//load the information about resources in the manifest 
						$resources = $qtiManifestParser->load();
						if(count($resources) > 0){
							
							//create a new test
							$testInstance = $testService->createInstance($clazz);
							
							//create an item class specificaly
							$itemClazz = $itemService->createSubClass( $itemService->getItemClass() );
							foreach($resources as $resource){
								
								if($resource instanceof taoTests_models_classes_QTI_Resource){
								
									//create a new item in the model
									$itemInstance = $itemService->createInstance($itemClazz, $resource->getIdentifier());
									
									try{
										//load the QTI_Item from the item file
										$qtiItem = $qtiService->loadItemFromFile($folder . $resource->getItemFile());
									}
									catch(Exception $e){
										$this->setData('importErrorTitle', __('An error occured during the iport'));
										$this->setData('importErrors', $e->getMessage());
										break;
									}
									
									if(!is_null($qtiItem) && !is_null($itemInstance)){
										//set the file in the itemContent
										if($qtiService->saveDataItemToRdfItem($qtiItem, $itemInstance)){
											
											//we deploy it
											$mainFile = $itemService->deployItem($itemInstance);
											if(file_exists($mainFile)){
												//and copy the others resources
												$deployFolder = dirname($mainFile);
												foreach($resource->getAuxiliaryFiles() as $auxResource){
													rename($folder . $auxResource, $deployFolder . $auxResource);
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
?>

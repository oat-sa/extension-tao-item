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
	
	protected $excludedProperties = array(TAO_ITEM_CONTENT_PROPERTY);
	
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
		
			$uploadedFile = $formValues['source']['uploaded_file'];
			
			$forceValid = false;
			if(isset($formValues['disable_validation'])){
				if(is_array($formValues['disable_validation'])){
					$forceValid = true;
				}
			}
			
			try {
				$importService = taoItems_models_classes_QTI_ImportService::singleton();
				$item = $importService->importQTIFile($uploadedFile, $clazz, $forceValid);
			} catch (taoItems_models_classes_QTI_ParsingException $e) {
				$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
				$this->setData('importErrors', $qtiParser->getErrors());
			} catch (common_Exception $e) {
				$this->setData('message', __('An error occurs during the import'));
			}
			
			@unlink($uploadedFile);
			if (!is_null($item)) {
				$this->removeSessionAttribute('classUri');
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->getUri()));
				$this->setData('message', __('Item imported successfully') . ' : ' .$item->getLabel());
				$this->setData('reload', true);
			}
			return true;
		}
		return false;
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
			$itemService	= taoItems_models_classes_ItemsService::singleton();
			$qtiService 	= taoItems_models_classes_QTI_Service::singleton();
			
			//test versioning
			$repository = null;
			if (isset($formValues['repository']) && common_Utils::isUri($formValues['repository'])) {
				$repository = new core_kernel_versioning_Repository($formValues['repository']);
				if (!$repository->exists()) {
					$repository = null;
				}
			}
			
			
			$uploadedFile = $formValues['source']['uploaded_file'];
			
			$forceValid = false;
			if(isset($formValues['disable_validation'])){
				if(is_array($formValues['disable_validation'])){
					$forceValid = true;
				}
			} 
			
			try {
				$importService = taoItems_models_classes_QTI_ImportService::singleton();
				$importedItems = $importService->importQTIPACKFile($uploadedFile, $clazz, $forceValid, $repository);
			} catch (taoItems_models_classes_QTI_exception_ExtractException $e) {
				$this->setData('importErrorTitle', __('An error occured during the import'));
				$this->setData('importErrors', array(array('message' => __('unable to extract archive content, please check your tmp dir'))));
			} catch (taoItems_models_classes_QTI_ParsingException $e) {
				$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
				$this->setData('importErrors', $qtiParser->getErrors());
				return false;
				
			} catch (common_Exception $e) {
				$this->setData('message', __('An error occurs during the import'));
			}
			
			if($importedItems > 0){
				
				$this->removeSessionAttribute('classUri');
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->getUri()));
				$this->setData('message', $importedItems . ' ' . __('items imported successfully'));
				$this->setData('reload', true);
				
				return true;
			}
			else{
				// Display how many items were imported.
				$this->setData('message', __('No items of could be imported successfuly'));
			}
			tao_helpers_File::remove($uploadedFile);
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
			$itemService	= taoItems_models_classes_ItemsService::singleton();
			
			//test versioning
			$versioning = false;
			if (isset($formValues['repository']) && common_Utils::isUri($formValues['repository'])) {
				$repository = new core_kernel_versioning_Repository($formValues['repository']);
				if ($repository->exists()) {
					$versioning = true;
				}
			}
			
			$uploadedFile = $formValues['source']['uploaded_file'];
			$uploadedFileBaseName = basename($uploadedFile);
			// uploaded file name contains an extra prefix that we have to remove.
			$uploadedFileBaseName = preg_replace('/^([0-9a-z])+_/', '', $uploadedFileBaseName, 1);
			$uploadedFileBaseName = preg_replace('/.zip|.ZIP$/', '', $uploadedFileBaseName);
			
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
			$rdfItem->editPropertyValues(new core_kernel_classes_Property(RDFS_LABEL), $uploadedFileBaseName);
			//set the QTI type
			$rdfItem->setPropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_XHTML);
			
			$itemContent = file_get_contents($folder .'/index.html');
			
			$folderName = substr($rdfItem->uriResource, strpos($rdfItem->uriResource, '#') + 1);
			
			$itemPath = $itemService->getItemFolder($rdfItem);
        	if(!tao_helpers_File::move($folder, $itemPath)){
        		$this->setData('importErrorTitle', __('Unable to copy the resources'));
				$this->setData('importErrors', array(array('message' => __('Unable to move')." $folder to $itemPath")));
				return false;
        	}
        	
        	$itemService->setItemContent($rdfItem, $itemContent, null, 'HOLD_COMMIT');
			if($versioning){
				// add to repo
				$itemContent = $rdfItem->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
				$versionedContend = $repository->add($itemContent);
				if (!is_null($versionedContend)) {
					if ($versionedContend->getUri() != $itemContent->getUri()) {
						$rdfItem->editPropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY), $versionedContend);
					}
					$importedItems++;
				}
			}
			
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
	
	protected function importPaperFile($formValues){
		if(!isset($formValues['source'])) {
			common_Logger::w('Missing file source during paper-based item import', 'TAOITEMS');
			return false;
		}
		if(!$this->hasSessionAttribute('classUri')) {
			common_Logger::w('Missing classUri during paper-based item import', 'TAOITEMS');
			return false;
		}
		//get the item parent class
		//get the item parent class
		$clazz = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getSessionAttribute('classUri')));
	
		//get the services instances we will need
		$itemService		= taoItems_models_classes_ItemsService::singleton();
		$uploadedFile		= $formValues['source']['uploaded_file'];
		$originalFileName	= $formValues['source']['name'];
		
		//create a new item instance of the clazz
		if($itemService->isItemClass($clazz)){
					
			//create the instance
			$rdfItem = $itemService->createInstance($clazz);
						
			if(!is_null($rdfItem)){
				//set the QTI type
				$rdfItem->setPropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_PAPERBASED);
				
				$itemService->setItemContent($rdfItem, file_get_contents($uploadedFile));
				$rdfItem->editPropertyValues(new core_kernel_classes_Property(TAO_ITEM_SOURCENAME_PROPERTY), $originalFileName);
				$rdfItem->setLabel($originalFileName);

				$this->removeSessionAttribute('classUri');
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($rdfItem->uriResource));
				$this->setData('message', __('Item imported successfully') . ' : ' .$rdfItem->getLabel());
				$this->setData('reload', true);
								
				@unlink($uploadedFile);
				return true;
			} else {
				common_Logger::w('could not create instance of class \''.$clazz->getLabel().'\'', array('TAOITEMS'));
			}
		} else {
			common_Logger::w('expected ItemClass, got class \''.$clazz->getLabel().'\'', array('TAOITEMS'));
		}
		$this->setData('message', __('An error occurs during the import'));
			
		return true;
	}
}
?>
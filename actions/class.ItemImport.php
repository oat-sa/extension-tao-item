<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
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
				$importService = taoQTI_models_classes_QTI_ImportService::singleton();
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
			
			$importedItems = array();
			set_time_limit(200);	//the zip extraction is a long process that can exced the 30s timeout
			
			//get the item parent class
			$clazz = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getSessionAttribute('classUri')));
			
			//get the services instances we will need
			$itemService	= taoItems_models_classes_ItemsService::singleton();
			$qtiService 	= taoQTI_models_classes_QTI_Service::singleton();
			
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
				$importService = taoQTI_models_classes_QTI_ImportService::singleton();
				$importedItems = $importService->importQTIPACKFile($uploadedFile, $clazz, $forceValid, $repository);
			} catch (taoItems_models_classes_QTI_exception_ExtractException $e) {
				$this->setData('importErrorTitle', __('An error occured during the import'));
				$this->setData('importErrors', array(array('message' => __('unable to extract archive content, please check your tmp dir'))));
			} catch (taoItems_models_classes_QTI_ParsingException $e) {
				$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
				$this->setData('importErrors', array());
				return false;
				
			} catch (common_Exception $e) {
				$this->setData('message', __('An error occured during the import'));
			}
			
			if(count($importedItems) > 0) {
				
				$this->removeSessionAttribute('classUri');
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->getUri()));
				$this->setData('message', count($importedItems) . ' ' . __('items imported successfully'));
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
			
			$uploadedFile = $formValues['source']['uploaded_file'];
			$uploadedFileBaseName = basename($uploadedFile);
			// uploaded file name contains an extra prefix that we have to remove.
			$uploadedFileBaseName = preg_replace('/^([0-9a-z])+_/', '', $uploadedFileBaseName, 1);
			$uploadedFileBaseName = preg_replace('/.zip|.ZIP$/', '', $uploadedFileBaseName);
			
			$validate = true;
			if(isset($formValues['disable_validation'])){
				if(is_array($formValues['disable_validation'])){
					$validate = false;
				}
			}
			
			$importService = taoItems_models_classes_XHTML_ImportService::singleton();
			try {
				$rdfItem = $importService->importXhtmlFile($uploadedFile, $clazz, $validate);
			} catch (taoItems_models_classes_Import_ExtractException $e) {
				$this->setData('importErrorTitle', __('An error occured during the import'));
				$this->setData('importErrors', array(array('message' => __('unable to extract archive content, please check your tmp dir'))));
				return false;
			} catch (taoItems_models_classes_Import_ParsingException $e) {
				$this->setData('importErrorTitle', __('Validation failed'));
				$this->setData('importErrors', array(array('message' => __('Validation of the imported file has failed'))));
				return false;
				
			} catch (common_Exception $e) {
				$this->setData('message', __('An error occured during the import'));
				return false;
			}
			
			tao_helpers_File::remove($uploadedFile);
			$this->removeSessionAttribute('classUri');
			$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($rdfItem->uriResource));
			$this->setData('message',__('item imported successfully'));
			$this->setData('reload', true);
			
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
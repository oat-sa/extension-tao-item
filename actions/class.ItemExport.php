<?php
/**
 * This controller provide the actions to export items 
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @subpackage action
 *
 */
class taoItems_actions_ItemExport extends tao_actions_Export {

	/**
	 * constructor used to override the formContainer
	 */
	public function __construct(){
		parent::__construct();
		
		$data = array();
		if($this->hasRequestParameter('classUri')){
			if(trim($this->getRequestParameter('classUri')) != ''){
				$data['class'] = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));
			}
		}
		if($this->hasRequestParameter('uri') && $this->hasRequestParameter('classUri')){
			if(trim($this->getRequestParameter('uri')) != ''){
				$data['item'] = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
			}
		}
		$this->formContainer = new taoItems_actions_form_Export($data);
	}
	
	/**
	 * action to perform to export items in XML
	 * @param array $formValues the posted data
	 */
	protected function exportXMLData($formValues){
		if($this->hasRequestParameter('filename')){
			$instances = $formValues['instances'];
			if(count($instances) > 0){
				
				$itemService = tao_models_classes_ServiceFactory::get('Items');
				
				$folder = $this->getExportPath();
				$fileName = $formValues['filename'].'_'.time().'.zip';
				$path = tao_helpers_File::concat(array($folder, $fileName));
				if(!tao_helpers_File::securityCheck($path, true)){
					throw new Exception('Unauthorized file name');
				}
				
				$zipAchive = new ZipArchive();
				if($zipAchive->open($path, ZipArchive::CREATE) !== true){
					throw new Exception('Unable to create archive at '.$path);
				}
				foreach($instances as $instance){
					$item = $itemService->getItem($instance);
					$folder = $itemService->getItemFolder($item);
					
					$this->addFolder($zipAchive, $folder, basename($folder));
				}
				
				$zipAchive->close();
			}
		}
	}
	
	/**
	 * Utility function to add recursively a folder to an archive, 
	 * 
	 * @see ItemExport::exportXMlData
	 * @param ZipArchive $zipAchive
	 * @param string $folder
	 * @param string $relPath
	 * @return boolean
	 */
	private function addFolder(ZipArchive $zipAchive, $folder, $relPath){
		
		$done = 0;
		
		$content = scandir($folder);
		foreach($content as $file){
			if(!preg_match("/^\./", $file)){
				$filePath = tao_helpers_File::concat(array($folder, $file));
				$fileRelPath = tao_helpers_File::concat(array($relPath, $file));
				if(is_dir($filePath)){
					if($zipAchive->addEmptyDir($filePath, $fileRelPath)){
						$done++;
					}
					$this->addFolder($zipAchive, $filePath, $fileRelPath);
				}
				else{
					if($zipAchive->addFile($filePath, $fileRelPath)){
						$done++;
					}
				}
			}
		}
		return (count($content == $done));
	}
}
?>
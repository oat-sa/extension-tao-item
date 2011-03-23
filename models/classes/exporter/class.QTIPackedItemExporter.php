<?php
class taoItems_models_classes_exporter_QTIPackedItemExporter extends taoItems_models_classes_exporter_QTIItemExporter {

	/**
	 * Store the manifest of teh previous items exported
	 * @var string
	 */
	protected static $manifest = '';
	
	/**
	 * Reset the manifest
	 */
	public function resetManifest(){
		self::$manifest = '';
	}
	
	/**
	 * @see taoItems_models_classes_exporter_QTIItemExporter::export()
	 * Export the manifest in addition
	 */
	public function export(){
		parent::export();
		$this->exportManifest();
	}
	
	/**
	 * Build, merge and export the IMS Manifest
	 */
	public function exportManifest(){

		$base = basename($this->getItemLocation());
		$zipArchive = $this->getZip();
		if(!is_null($zipArchive)){
			
			$qtiFile = '';
			$qtiResources = array();
			for($i = 0; $i < $zipArchive->numFiles; $i++){  
          		$fileName = $zipArchive->getNameIndex($i);
          		if(preg_match("/^$base/", $fileName)){
          			if(basename($fileName) == 'qti.xml'){
          				$qtiFile = $fileName;
          			}
          			else{
          				$qtiResources[] = $fileName;
          			}
          		}
     		}
     		if(!empty($qtiFile)){
     			$qtiItemService = tao_models_classes_ServiceFactory::get('taoItems_models_classes_QTI_Service');
     			$qtiItem = $qtiItemService->getDataItemByRdfItem($this->getItem());
     			if(!is_null($qtiItem)){
	     			$templateRenderer = new taoItems_models_classes_TemplateRenderer(BASE_PATH.'/models/classes/QTI/templates/imsmanifest.tpl.php', array(
						'qtiItem' 				=> $qtiItem,
						'qtiFilePath'			=> $qtiFile,
						'medias'				=> $qtiResources,
						'manifestIdentifier'	=> 'QTI-MANIFEST-'.tao_helpers_Display::textCleaner($qtiItem->getIdentifier(), '-')
		        	));
		        	$renderedManifest = $templateRenderer->render();
		        	if(self::$manifest == ''){
		        		self::$manifest = $renderedManifest;
		        	}
		        	else{
		        		$dom1 = new DOMDocument();
		        		$dom1->loadXML(self::$manifest);
		        		
		        		$dom2 = new DOMDocument();
		        		$dom2->loadXML($renderedManifest);
		        		$resourceNodes = $dom2->getElementsByTagName('resource');
		        		
		        		$resourcesNodes = $dom1->getElementsByTagName('resources');
		        		foreach($resourcesNodes as $resourcesNode){
		        			foreach($resourceNodes as $resourceNode){
		        				$newResourceNode = $dom1->importNode($resourceNode, true);
		        				$resourcesNode->appendChild($newResourceNode);
		        			}
		        		}
		        		self::$manifest = $dom1->saveXML();
		        		
		        	}
		        	
		        	$zipArchive->addFromString('imsmanifest.xml', self::$manifest);
     			}
     		}
			
		}
	}
	
}
?>
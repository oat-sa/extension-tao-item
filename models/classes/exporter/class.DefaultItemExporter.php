<?php
//TODO : to be generated !
class taoItems_models_classes_exporter_DefaultItemExporter extends taoItems_models_classes_ItemExporter {

	public function export($options = array()) {
		
		$zipToRoot = isset($options['zipToRoot'])?(bool)$options['zipToRoot']:false;
		
		$location = $this->getItemLocation();
		if(is_dir(realpath($location))){
			if($zipToRoot){
				$this->addFile($location, '');
			}else{
				$this->addFile($location, basename($location));
			}
		}
	}
	
}
?>
<?php
class taoItems_models_classes_exporter_DefaultItemExporter extends taoItems_models_classes_ItemExporter {

	public function export() {
		$location = $this->getItemLocation();
		if(is_dir(realpath($location))){
			$this->addFile($location, basename($location));
		}
	}
	
}
?>
<?php
class taoItems_models_classes_exporter_DefaultItemExporter extends taoItems_models_classes_ItemExporter {

	public function export() {
		$location = $this->getItemLocation();
		$this->addFile($location, basename($location));
	}
	
}
?>
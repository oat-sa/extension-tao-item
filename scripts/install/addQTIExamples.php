<?php
require_once dirname(__FILE__) . '/../../includes/raw_start.php';

$itemClass	= taoItems_models_classes_ItemsService::singleton()->getItemClass();
$file		= dirname(__FILE__).DIRECTORY_SEPARATOR.'qtiExamples.zip';

$service = taoItems_models_classes_QTI_ImportService::singleton();
$service->importQTIPACKFile($file, $itemClass, false);
<?php
$extension = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
$dataPath = $extension ->getConstant('BASE_PATH').'data/';

$service = tao_models_classes_FileSourceService::singleton();
$source = $service->addLocalSource('itemDirectory', $dataPath);
$service->setDefaultFileSource($source);
<?php
$extension = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
$dataPath = $extension ->getConstant('BASE_PATH').'data/';

$source = tao_models_classes_FileSourceService::singleton()->addLocalSource('itemDirectory', $dataPath);
taoItems_models_classes_ItemsService::singleton()->setDefaultFileSource($source);
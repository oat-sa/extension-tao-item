<?php
$todefine = array(
	'TAO_ITEM_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
	'TAO_ITEM_MODEL_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels',
	'TAO_ITEM_MODEL_PROPERTY' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel', 
	'TAO_ITEM_CONTENT_PROPERTY' 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent', 
	'TAO_ITEM_MODEL_RUNTIME_PROPERTY' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#SWFFile', 
	'TAO_ITEM_MODEL_AUTHORING_PROPERTY' => 'http://www.tao.lu/Ontologies/TAOItem.rdf#12580164649880',
	'TAO_ITEM_AUTHORING_BASE_URI' 		=> BASE_PATH.'/data'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>
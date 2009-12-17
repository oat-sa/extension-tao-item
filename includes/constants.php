<?php

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */

$todefine = array(
	'TAO_ITEM_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
	'TAO_ITEM_MODEL_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels',
	'TAO_ITEM_MODEL_PROPERTY' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel', 
	'TAO_ITEM_CONTENT_PROPERTY' 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent', 
	'TAO_ITEM_MODEL_RUNTIME_PROPERTY' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#SWFFile', 
	'TAO_ITEM_MODEL_AUTHORING_PROPERTY' => 'http://www.tao.lu/Ontologies/TAOItem.rdf#12580164649880',
	'TAO_ITEM_MODEL_QCM'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#QCM',
	'TAO_ITEM_MODEL_KHOS'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#kohs',
	'TAO_ITEM_MODEL_WATERPHENIX'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#125933161031263',
	'TAO_ITEM_AUTHORING_BASE_URI' 		=> BASE_PATH.'/data',
	'TAO_ITEM_AUTHORING_TPL_FILE' 		=> BASE_PATH.'/data/black_ref.xml',
	'TAO_OBJECT_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject',
	'TAO_GROUP_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group',
	'TAO_RESULT_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#Result',
	'TAO_SUBJECT_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject',
	'TAO_TEST_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test',
	'RDFS_LABEL'						=> 'http://www.w3.org/2000/01/rdf-schema#label',
	'GENERIS_BOOLEAN'					=> 'http://www.tao.lu/Ontologies/generis.rdf#Boolean'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>
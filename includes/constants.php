<?php

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
include_once ROOT_PATH . '/tao/includes/constants.php';

$todefine = array(
	'TAO_ITEM_MODEL_PROPERTY' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel', 
	'TAO_ITEM_CONTENT_PROPERTY' 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent', 
	'TAO_ITEM_MODEL_RUNTIME_PROPERTY' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#SWFFile', 
	'TAO_ITEM_MODEL_AUTHORING_PROPERTY' => 'http://www.tao.lu/Ontologies/TAOItem.rdf#i12580164649880',
	'TAO_ITEM_MODEL_DATAFILE_PROPERTY'	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#DataFileName',
	'TAO_ITEM_MODEL_QCM'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#QCM',
	'TAO_ITEM_MODEL_KHOS'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#kohs',
	'TAO_ITEM_MODEL_KOHS'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#kohs',
	'TAO_ITEM_MODEL_CTEST'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Ctest',
	'TAO_ITEM_MODEL_QTI'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#QTI',
	'TAO_ITEM_MODEL_XHTML'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#XHTML',
	'TAO_ITEM_MODEL_WATERPHENIX'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#i125933161031263',
	'TAO_ITEM_MODEL_CAMPUS'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#campus',
	'TAO_ITEM_AUTHORING_BASE_URI' 		=> BASE_PATH.'/data',
	'TAO_ITEM_HAWAI_TPL_FILE'	 		=> BASE_PATH.'/data/black_ref.xml',
	'TAO_ITEM_CAMPUS_TPL_FILE'	 		=> BASE_PATH.'/data/campus_ref.xml',
	'QTI_RESPONSE_TEMPLATE_MATCH_CORRECT' => 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/match_correct',
	'QTI_RESPONSE_TEMPLATE_MAP_RESPONSE' => 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response',
	'QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT' => 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response_point'
);
foreach($todefine as $constName => $constValue){
	if(!defined($constName)){
		define($constName, $constValue);
	}
}
unset($todefine);
?>
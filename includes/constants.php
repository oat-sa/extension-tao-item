<?php

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
$todefine = array(
	'TAO_ITEM_MODEL_PROPERTY' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel', 
	'TAO_ITEM_CONTENT_PROPERTY' 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent',
	'TAO_ITEM_VERSIONED_CONTENT_PROPERTY'=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#VersionedItemContentFolder',
	'TAO_ITEM_MODEL_RUNTIME_PROPERTY' 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemRuntime', 
	'TAO_ITEM_MODEL_AUTHORING_PROPERTY' => 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemAuthoring',
	'TAO_ITEM_MODEL_DATAFILE_PROPERTY'	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#DataFileName',
	'TAO_ITEM_MODEL_QCM'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#QCM',
	'TAO_ITEM_MODEL_KOHS'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#kohs',
	'TAO_ITEM_MODEL_CTEST'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Ctest',
	'TAO_ITEM_MODEL_QTI'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#QTI',
	'TAO_ITEM_MODEL_XHTML'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#XHTML',
	'TAO_ITEM_MODEL_HAWAI'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Hawai',
	'TAO_ITEM_MODEL_CAMPUS'				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#campus',
	
	'TAO_ITEM_AUTHORING_BASE_URI' 		=> BASE_PATH.'/data',
	'TAO_ITEM_HAWAI_TPL_FILE'	 		=> BASE_PATH.'/models/ext/itemAuthoring/waterphenix/xt/xhtml/data/units/xhtml.skeleton.xhtml',
	'TAO_ITEM_CAMPUS_TPL_FILE'	 		=> BASE_PATH.'/data/campus_ref.xml',
	
	'QTI_RESPONSE_TEMPLATE_MATCH_CORRECT' 		=> 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/match_correct',
	'QTI_RESPONSE_TEMPLATE_MAP_RESPONSE' 		=> 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response',
	'QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT' 	=> 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response_point',

	'TAO_ITEM_MODEL_STATUS_PROPERTY'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModelStatus',
	'TAO_ITEM_MODEL_STATUS_STABLE' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusStable',
	'TAO_ITEM_MODEL_STATUS_DEPRECATED'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusDeprecated',
	'TAO_ITEM_MODEL_STATUS_DEV' 			=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusDevelopment',
	'TAO_ITEM_MODEL_STATUS_EXPERIMENTAL'	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#StatusExperimental',
	'TAO_ITEM_EXPORTERS_DIR'				=> BASE_PATH.'/models/ext/itemExport',
	
	'TAO_ITEM_MEASURMENT_PROPERTY' 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#hasMeasurement',
	'TAO_ITEM_MEASURMENT' 				=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Measurement',
	'TAO_ITEM_IDENTIFIER_PROPERTY'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#MeasurementIdentifier',
	'TAO_ITEM_MEASURMENT_HUMAN_ASSISTED'=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#isHumanAssisted', 
	'TAO_ITEM_SCALE_PROPERTY'	 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#hasScale',
	'TAO_ITEM_DESCRIPTION_PROPERTY'	 	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#MeasurementDescription',
	'TAO_ITEM_SCALE'			 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Scale',
	'TAO_ITEM_NUMERICAL_SCALE'	 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#NumericalScale',
	'TAO_ITEM_DISCRETE_SCALE'	 		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#DiscreteScale',
	'TAO_ITEM_ENUMERATION_SCALE'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Enumeration',
	'TAO_ITEM_LOWER_BOUND_PROPERTY'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ScaleLowerBound',
	'TAO_ITEM_UPPER_BOUND_PROPERTY'		=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#ScaleUpperBound',
	'TAO_ITEM_DISCRETE_SCALE_DISTANCE_PROPERTY'	=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#DiscreteScaleDistance',
	'TAO_ITEM_MODEL_SURVEY'                => 
'http://www.tao.lu/Ontologies/TAOItem.rdf#SurveyItem'

);
?>

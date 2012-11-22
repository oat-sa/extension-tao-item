<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
	'name' => 'taoItems',
	'description' => 'the TAO Items extension provides the item creation, authoring and managment',
	'version' => '2.4',
	'author' => 'CRP Henri Tudor',
	'dependencies' => array('tao'),
	'models' => array(
		'http://www.tao.lu/Ontologies/TAOItem.rdf',
		'http://www.tao.lu/Ontologies/taoFuncACL.rdf'),
	'install' => array(
		'rdf' => array(
				array('ns' => 'http://www.tao.lu/Ontologies/TAOItem.rdf', 'file' => dirname(__FILE__). '/models/ontology/taoitem.rdf'),
		),
		'checks' => array(
			array('type' => 'CheckPHPExtension', 'value' => array('id' => 'taoItems_extension_tidy', 'name' => 'tidy')),
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_taoItems_data', 'location' => 'taoItems/data', 'rights' => 'rw')),
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_taoItems_includes', 'location' => 'taoItems/includes', 'rights' => 'r')),
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_taoItems_views_runtime', 'location' => 'taoItems/views/runtime', 'rights' => 'rw'))
		)
	),
	'classLoaderPackages' => array(
		dirname(__FILE__).'/actions/',
		dirname(__FILE__).'/helpers/'
	),
	 'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# models directory
		"DIR_MODELS"			=> $extpath."models".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# helpers directory
		"DIR_HELPERS"			=> $extpath."helpers".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Items',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL	.'taoItems/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL	.'taoItems/views/',
	
		#BASE DATA the path where items are stored
		'BASE_DATA'				=> $extpath.'data'.DIRECTORY_SEPARATOR,
	
		#BASE PREVIEW the path where items are compiled for preview
		'BASE_PREVIEW'			=> $extpath.'views'.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR,

		#BASE PREVIEW URL the url pointing at where items can be previewed
		'BASE_PREVIEW_URL'		=> ROOT_URL.'taoItems/views/runtime/',
	 
	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL	.'tao/views/',
		'TAOVIEW_PATH'			=> $taopath	.'views'.DIRECTORY_SEPARATOR,
		'TAO_TPL_PATH'			=> $taopath	.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR,
	
		'USE_CACHED_XSL'		=> false
	)
);
?>
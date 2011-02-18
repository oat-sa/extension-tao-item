<?php
require_once dirname(__FILE__) . '/../includes/raw_start.php';

new taoItems_scripts_MigrateLegacyItems(array(
	'min'		=> 2,
	'required'	=> array(
		array('input', 'output'),
		array('input', 'uri'),
		array('input', 'addResource')
	),
	'parameters' => array(
		array(
			'name' 			=> 'input',
			'type' 			=> 'file',
			'shortcut'		=> 'i',
			'required'		=> true,
			'description'	=> 'the intput file containing the legacy item'
		),
		array(
			'name' 			=> 'output',
			'type' 			=> 'path',
			'shortcut'		=> 'o',
			'description'	=> 'the output file to save the new item'
		),
		array(
			'name' 			=> 'uri',
			'type' 			=> 'string',
			'shortcut'		=> 'u',
			'description' 	=> 'the uri of an existing resource to bind the item content'
		),
		array(
			'name' 			=> 'addResource',
			'type' 			=> 'boolean',
			'shortcut'		=> 'a',
			'description' 	=> 'create a new resource to bind the item content'
		),
		array(
			'name' 			=> 'class',
			'type' 			=> 'string',
			'shortcut'		=> 'c',
			'description' 	=> 'the RDFS class where to add the resource'
		)
	)
));
?>
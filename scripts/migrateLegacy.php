<?php
require_once dirname(__FILE__) . '/../includes/raw_start.php';

new taoItems_scripts_MigrateLegacyItems(array(
	'min'		=> 1,
	'required'	=> array(
		array('input'),
		array('input', 'uri'),
		array('input', 'addResource'),
		array('input', 'output', 'pack')
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
			'type' 			=> 'dir',
			'shortcut'		=> 'o',
			'description'	=> 'the output directory to save the new item, by default the same than the input'
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
		),
		array(
			'name' 			=> 'pack',
			'type' 			=> 'boolean',
			'shortcut'		=> 'p',
			'description' 	=> 'Create a package'
		)
	)
));
?>
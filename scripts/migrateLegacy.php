<?php
require_once dirname(__FILE__) . '/../includes/raw_start.php';

new taoItems_scripts_MigrateLegacyItems(array(
	'min'		=> 2,
	'required'	=> array('input', 'output'),
	'types'		=> array(
		'input'		=> 'file',
		'output' 	=> 'path'
	),
	'shortcuts'	=> array(
		'input'		=> 'i',
		'output' 	=> 'o'
	)
));
?>
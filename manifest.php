<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
return array(
	'name' => 'taoItems',
	'description' => 'the TAO Items extension provides the item creation, authoring and managment',
	'additional' => array(
		'version' => '2.0',
		'author' => 'CRP Henri Tudor',
		'dependances' => array('tao'),
		'models' => 'http://www.tao.lu/Ontologies/TAOItem.rdf',
		'install' => array( 
			'php' => dirname(__FILE__). '/install/install.php',
			'rdf' => dirname(__FILE__). '/models/ontology/taoitem.rdf'
		),
		'classLoaderPackages' => array( 
			dirname(__FILE__).'/actions/',
			dirname(__FILE__).'/helpers/'
		 )
	)
);
?>
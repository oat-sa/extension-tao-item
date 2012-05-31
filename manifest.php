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
		'extends' => 'tao',
		'dependances' => array('tao'),
		'models' => array('http://www.tao.lu/Ontologies/TAOItem.rdf',
			'http://www.tao.lu/Ontologies/taoFuncACL.rdf'),
		'install' => array(
			'rdf' => array(
					array('ns' => 'http://www.tao.lu/Ontologies/TAOItem.rdf', 'file' => dirname(__FILE__). '/models/ontology/taoitem.rdf'),
					array('ns' => 'http://www.tao.lu/Ontologies/taoFuncACL.rdf', 'file' => dirname(__FILE__). '/models/ontology/funcacl.rdf')
			)
		),
		'classLoaderPackages' => array(
			dirname(__FILE__).'/actions/',
			dirname(__FILE__).'/helpers/'
		 )
	)
);
?>
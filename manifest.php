<?php
	return array(
		'name' => 'TAO Items',
		'description' => 'TAO Items extensions http://www.tao.lu',
		'additional' => array(
			'version' => '1.0',
			'author' => 'CRP Henry Tudor',
			'dependances' => array(),
			'install' => array( 
				'sql' => dirname(__FILE__). '/model/ontology/TAOItem.sql',
				'php' => dirname(__FILE__). '/install/install.php'
			),


			'classLoaderPackages' => array( 
				dirname(__FILE__).'/actions/',
				dirname(__FILE__).'/helpers/'
			 )

				
			
		)
	);
?>
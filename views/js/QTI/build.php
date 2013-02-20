<?php 
include_once("../../../../tao/lib/jstools/minify.php");

//minimify QTI Javascript sources using JSMin
$jsFiles = array (
	'./src/Widget.js',
	'./src/widgets/associate.js',
	'./src/widgets/button.js',
	'./src/widgets/choice.js',
	'./src/widgets/match.js',
	'./src/widgets/order.js',
	'./src/widgets/spot.js',
	'./src/widgets/text.js',
	'./src/ResultCollector.js',
	'./src/init.js',
	'./src/initTaoApis.js'			//remove this line to use the QTI API without TAO
);
minifyJSFiles($jsFiles, "qti.min.js");

//minimify QTI CSS sources using JSMin
$cssFiles = array (
	'./css/reset.css',
	'./css/qti.css'
);
minifyCSSFiles($cssFiles, dirname(__FILE__)."/css/qti.min.css");

exit(0);
?>

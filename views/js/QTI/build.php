<?php 
include_once("../../../../tao/lib/jstools/minify.php");

$jsFiles = array ();
$jsFiles[] = "./src/Widget.js";
$jsFiles[] = "./src/ResultCollector.js";
$jsFiles[] = "./src/init.js";

minifyJSFiles ($jsFiles, "qti.min.js");

$cssFiles = array ();
$cssFiles[] = "./css/reset.css";
$cssFiles[] = "./css/qti.css";

minifyCSSFiles ($cssFiles, dirname(__FILE__)."/css/qti.min.css");

exit(0);
?>

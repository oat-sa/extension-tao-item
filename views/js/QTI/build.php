<?php 
include_once("../../../../tao/lib/jstools/jsmin.php");

$files = array ();
$files[] = "./src/Widget.js";
$files[] = "./src/ResultCollector.js";
$files[] = "./src/init.js";

minify_files ($files, "qti.min.js");

$files = array ();
$files[] = "./css/qti.css";
$files[] = "./css/reset.css";

minify_files ($files, "qti.min.css");

exit(0);
?>

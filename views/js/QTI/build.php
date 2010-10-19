<?php 
include_once("../../../../tao/lib/jstools/jsmin.php");

$files = array ();
$files[] = "./src/Widget.js";
$files[] = "./src/ResultCollector.js";
$files[] = "./src/init.js";

minify_files ($files, "qti.min.js");

exit(0);
?>

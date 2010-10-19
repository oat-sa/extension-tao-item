<?php 
include_once("../../../../tao/lib/jstools/jsmin.php");

$files = array ();
$files[] = "./src/constants.js";
$files[] = "./src/core.js";
$files[] = "./src/api.js";

minify_files ($files, "taoApi.min.js");

exit(0);
?>

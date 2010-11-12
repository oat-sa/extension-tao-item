<?php 
include_once("../../../../tao/lib/jstools/minify.php");

$files = array ();
$files[] = "./src/constants.js";
$files[] = "./src/core.js";
$files[] = "./src/events.js";
$files[] = "./src/api.js";

minifyJSFiles ($files, "taoApi.min.js");

exit(0);
?>

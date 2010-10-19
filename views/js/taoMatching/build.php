<?php 
include_once("../../../../tao/lib/jstools/jsmin.php");

$files = array ();
$files[] = "./src/class.Matching.js";
$files[] = "./src/class.VariableFactory.js";
$files[] = "./src/class.Variable.js";
$files[] = "./src/class.BaseTypeVariable.js";
$files[] = "./src/class.Collection.js";
$files[] = "./src/class.List.js";
$files[] = "./src/class.Tuple.js";
$files[] = "./src/class.Map.js";
$files[] = "./src/matching_api.js";

minify_files ($files, "taoMatching.min.js");

exit(0);
?>

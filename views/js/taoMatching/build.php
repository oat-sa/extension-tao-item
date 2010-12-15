<?php 
include_once("../../../../tao/lib/jstools/minify.php");

$files = array ();
$files[] = "./src/class.Matching.js";
$files[] = "./src/class.MatchingRemote.js";
$files[] = "./src/class.VariableFactory.js";
$files[] = "./src/class.Variable.js";
$files[] = "./src/class.BaseTypeVariable.js";
$files[] = "./src/class.Shape.js";
$files[] = "./src/class.Ellipse.js";
$files[] = "./src/class.Poly.js";
$files[] = "./src/class.Collection.js";
$files[] = "./src/class.List.js";
$files[] = "./src/class.Tuple.js";
$files[] = "./src/class.Map.js";
$files[] = "./src/class.AreaMap.js";
$files[] = "./src/matching_constant.js";
$files[] = "./src/matching_api.js";

minifyJSFiles ($files, "taoMatching.min.js");

exit(0);
?>

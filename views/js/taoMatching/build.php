<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
?>
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

minifyJSFiles($files, "taoMatching.min.js");

exit(0);
?>

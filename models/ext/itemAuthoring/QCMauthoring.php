<?php
require('../../../../generis/common/inc.extension.php');
require('../../../includes/common.php');

if (isset($_POST["AddInquiry_x"])) {$_SESSION["AddInquiry_x"] = $_POST["AddInquiry_x"];}
if (isset($_POST["AddInquiry"])) {$_SESSION["AddInquiry"] = $_POST["AddInquiry"];}
if (isset($_POST["AddProp"])) {$_SESSION["AddProp"] = $_POST["AddProp"];}
if (isset($_POST["removeInquiry"])) {$_SESSION["removeInquiry"] = $_POST["removeInquiry"];}
if (isset($_POST["removeProposition"])) {$_SESSION["removeProposition"] = $_POST["removeProposition"]; }

require('TAOAuthoringGUI.php');
$TAOAuthoringGUI = new TAOAuthoringGUI($_GET['xml'], $_GET['instance']);
echo $TAOAuthoringGUI->getOutput();
?>
<?php
session_start();

/* BRIDGE WITH THE LEGACY FORMS */
if(isset($_POST['itemcontent'])){
	require('../../../../generis/common/inc.extension.php');
	require('../../../includes/common.php');
	require_once('TAOsaveContent.php');
	$TAOsaveContent = new TAOsaveContent();
	$_SESSION['xml'] = $TAOsaveContent->getOutput($_POST['itemcontent']);
}
else{
	$_SESSION['xml'] = $_POST['xml'];
}
$_SESSION['instance'] = $_POST['instance'];

header("Location: /taoItems/Items/saveItemContent");
?>

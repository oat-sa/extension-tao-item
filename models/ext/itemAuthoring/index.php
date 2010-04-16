<?php

/**
 * @todo REMOVE THIS AND REDIRECT DIRECTLY TO A VALID ACTION
 */

/* BRIDGE WITH THE LEGACY FORMS */
if(isset($_POST['itemcontent'])){
	require('../../../../generis/common/inc.extension.php');
	require('../../../includes/common.php');
	require_once('TAOsaveContent.php');
	$TAOsaveContent = new TAOsaveContent();
	$_SESSION['xml'] = $TAOsaveContent->getOutput($_POST['itemcontent']);
}
else{
	session_start();
	$_SESSION['xml'] = $_POST['xml'];
}
$_SESSION['instance'] = $_POST['instance'];

if($_SERVER['REQUEST_URI'] == '/taoItems/SaSItems/index.php'){
	header("Location: /taoItems/SaSItems/saveItemContent");
}
else{
	header("Location: /taoItems/Items/saveItemContent");
}
?>

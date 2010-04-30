<?php

/**
 * @todo REMOVE THIS AND REDIRECT DIRECTLY TO A VALID ACTION
 */

require_once('../../../../generis/common/inc.extension.php');
require_once('../../../includes/common.php');
require_once('../../../../tao/helpers/class.Uri.php');

/* BRIDGE WITH THE LEGACY FORMS */
if(isset($_POST['itemcontent'])){
	require_once('TAOsaveContent.php');
	$TAOsaveContent = new TAOsaveContent();
	$_SESSION['xml'] = $TAOsaveContent->getOutput($_POST['itemcontent']);
}
else{
	$_SESSION['xml'] = $_POST['xml'];
}
$_SESSION['instance'] = $_POST['instance'];

if(preg_match("/SaSItems/", $_SERVER['REQUEST_URI'])){
	header("Location: "._url('saveItemContent', 'SaSItems', 'taoItems'));
}
else{
	header("Location: "._url('saveItemContent', 'Items', 'taoItems'));
}
?>

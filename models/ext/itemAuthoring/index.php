<?php

/**
 * @todo REMOVE THIS AND REDIRECT DIRECTLY TO A VALID ACTION
 */

require_once(dirname(__FILE__).'/../../../../generis/common/inc.extension.php');
require_once(dirname(__FILE__).'/../../../includes/common.php');
require_once(ROOT_PATH.'/tao/helpers/class.Uri.php');

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
	header("Location: ".tao_helpers_Uri::url('saveItemContent', 'SaSItems', 'taoItems'));
}
else{
	header("Location: ".tao_helpers_Uri::url('saveItemContent', 'Items', 'taoItems'));
}
?>

<?php
require('../../../../generis/common/inc.extension.php');
require('../../../includes/common.php');

if (isset($_POST["AddInquiry_x"])) {
	$_SESSION["AddInquiry_x"] = $_POST["AddInquiry_x"];
}
else{
	unset($_SESSION["AddInquiry_x"]);
}

if (isset($_POST["AddInquiry"])) {
	$_SESSION["AddInquiry"] = $_POST["AddInquiry"];
}
else{
	unset($_SESSION["AddInquiry"]);
}

if (isset($_POST["AddProp"])) {
	$_SESSION["AddProp"] = $_POST["AddProp"];
}
else{
	unset($_SESSION["AddProp"]);
}

if (isset($_POST["removeInquiry"])) {
	$_SESSION["removeInquiry"] = $_POST["removeInquiry"];
}
else{
	unset($_SESSION["removeInquiry"]);
}

if (isset($_POST["removeProposition"])) {
	$_SESSION["removeProposition"] = $_POST["removeProposition"]; 
}
else{
	unset($_SESSION["removeProposition"]);
}


if(isset($_POST['itemcontent']) && isset($_POST['instance'])){

	require_once('TAOsaveContent.php');
	$TAOsaveContent = new TAOsaveContent();
	$xml = $TAOsaveContent->getOutput($_POST['itemcontent']);

	error_reporting(E_ALL);

	$service = tao_models_classes_ServiceFactory::get('Items');
	$item = $service->getItem($_POST['instance']);
	if(!is_null($item)){
		$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
		if($itemModel instanceof core_kernel_classes_Resource){
			$service->bindProperties($item, array(TAO_ITEM_CONTENT_PROPERTY => $xml));
		}
	}
}

require('TAOAuthoringGUI.php');
$TAOAuthoringGUI = new TAOAuthoringGUI($_GET['xml'], $_GET['instance']);
echo $TAOAuthoringGUI->getOutput();
?>
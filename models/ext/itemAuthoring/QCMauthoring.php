<?php
require('../../../../generis/common/inc.extension.php');
require('../../../includes/common.php');


$checkParams = array(
	'AddInquiry_x',
	'AddInquiry',
	'AddProp',
	'removeInquiry',
	'removeProposition',
	'nbinq',
	'nbprop'
);
foreach($checkParams as $checkParam){
	if (isset($_POST[$checkParam])) {
		$_SESSION[$checkParam] = $_POST[$checkParam];
	}
	elseif (isset($_SESSION[$checkParam])) {
		unset($_SESSION[$checkParam]);
	}
}

if(isset($_POST['itemcontent']) && isset($_POST['instance'])){

	require_once('TAOsaveContent.php');
	$TAOsaveContent = new TAOsaveContent();
	$xml = $TAOsaveContent->getOutput($_POST['itemcontent']);

	error_reporting(E_ALL);

	$service = tao_models_classes_ServiceFactory::get('Items');
	$item = $service->getItem($_POST['instance']);
	$service->setItemContent($item, $xml);
	
}

require('TAOAuthoringGUI.php');
$TAOAuthoringGUI = new TAOAuthoringGUI($_GET['xml'], $_GET['instance']);
echo $TAOAuthoringGUI->getOutput();
?>
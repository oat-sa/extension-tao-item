<?php
// Load the matching template functions of the context
// PREVIEW MODE
if (isset($ctx_preview_mode) && $ctx_preview_mode) $templateName = "preview"; 
// SERVER MODE
else if (isset($ctx_delivery_server_mode) && $ctx_delivery_server_mode) $templateName = "server"; 
// CLIENT MODE
else $templateName = "client"; 

$matchingTemplate  = dirname(__FILE__).'/matching/js.'.$templateName.'.tpl.php';
$matchingTplRenderer = new taoItems_models_classes_QTI_TemplateRenderer($matchingTemplate, $matching);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>QTI Item <?=$identifier?></title>

	<!-- CSS -->
    <link rel="stylesheet" type="text/css" href="<?=$ctx_base_www?>js/QTI/css/qti.min.css" media="screen" />

	<!-- user CSS -->
	<?foreach($stylesheets as $stylesheet):?>
		<link rel="stylesheet" type="text/css" href="<?=$stylesheet['href']?>" media="<?=$stylesheet['media']?>" />
	<?endforeach?>
	
	<!-- LIB -->
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/jquery-ui-1.8.4.custom.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/json2.js"></script>
	
	<!-- JS REQUIRED -->
	
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoApi/taoApi.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_root_url?>/wfEngine/views/js/wfApi/wfApi.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/taoMatching.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/QTI/qti.min.js"></script>
	<script type="text/javascript">
		var qti_initParam  = new Object();
		var matchingParam = new Object();
	
		$(document).ready(function(){
		    <?= $matchingTplRenderer->render() ?>
			qti_init(qti_initParam);
			matchingInit(matchingParam);
		});
	</script>
</head>
<body>
	<div class="qti_item">
		<h1><?=$options['title']?></h1>
	
		<?=$data?>
		
		<!-- validation button -->
		<a href="#" id="qti_validate">Validate</a>
	</div>
</body>
</html>

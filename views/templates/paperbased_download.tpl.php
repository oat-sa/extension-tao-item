<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?=$label?></title>

	<!-- LIB -->
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/jquery-ui-1.8.21.custom.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/json.min.js"></script>
	<script type="text/javascript">
		var root_url = "<?=$ctx_root_url?>";
	</script>

	<!-- JS REQUIRED -->
	<?if(!$ctx_raw_preview):?>
	<script type="text/javascript" src="<?=$ctx_root_url?>/wfEngine/views/js/wfApi/wfApi.min.js"></script>
	<?endif?>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoApi/taoApi.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/taoMatching.min.js"></script>
</head>
<body>
<div class="main-container">
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<a target="_blank" class="blink" href="<?=$downloadurl?>">
			<img class="icon" alt="xml" src="<?=$ctx_base_www?>img/text-xml-file.png"><?=__('Download item content')?>
		</a>
	</div>
</div>
</body>
</html>

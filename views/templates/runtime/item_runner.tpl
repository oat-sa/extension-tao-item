<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<script type='text/javascript' src="<?=TAOBASE_WWW?>js/require-jquery.js"></script>
		<script type='text/javascript' src="<?=TAOBASE_WWW?>js/util.js"></script>
		<script type='text/javascript' src='<?=BASE_WWW?>js/runtime/ItemServiceImpl.js'></script>
		<script type='text/javascript' src='<?=BASE_WWW?>js/runtime/ItemVariableStorage.js'></script>
		<script type='text/javascript' src='<?=BASE_WWW?>js/runtime/item_runner.js'></script>
		<script type='text/javascript'>
			var storageData = <?=tao_helpers_Javascript::buildObject(get_data('storageData'));?>;
			var itemId = <?=json_encode(get_data('itemId'));?>;
		</script>
	</head>
	<body style="margin: 0px;">
		<iframe id='item-container' src="<?=get_data('itemPath')?>" class="toolframe" frameborder="0" style="position:absolute;left:0%;top:0%;width:100%;" height="800"></iframe>
	</body>
</html>
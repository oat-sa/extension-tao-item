<?include(TAO_TPL_PATH . 'header.tpl')?>

<div id="preview-container" class="ui-corner-all">
	<?if(get_data('preview')):?>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="800" height="600" id="tao_item" align="middle">
		<param name="allowScriptAccess" value="sameDomain" />
		<param name="movie" value="<?=get_data('swf')?>?localXmlFile=<?=get_data('dataPreview')?>&instance=<?=get_data('instanceUri')?>" />
		<param name="wmode" value="opaque" />
		<param name="quality" value="high" />
		<param name="bgcolor" value="#ffffff" />
		<embed src="<?=get_data('swf')?>?localXmlFile=<?=get_data('dataPreview')?>&instance=<?=get_data('instanceUri')?>" quality="high" bgcolor="#ffffff" wmode="opaque" width="800" height="600" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
	<?else:?>
		<?=__('PREVIEW BOX')?><br /><br />
		<?=get_data('previewMsg')?>
	<?endif?>
</div>
<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>

<?include('footer.tpl');?>

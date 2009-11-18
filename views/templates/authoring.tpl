<?include('header.tpl')?>

<div class="main-container">
	
<?if(get_data('error')):?>
	
	<div class="ui-state-error ui-corner-all" style="padding:5px;">
		<?=__('Please select an item before!')?>
	</div>
	<br />
	<span class="ui-widget ui-state-default ui-corner-all" style="padding:5px;">
		<a href="#" onclick="selectTabByName('manage_items');"><?=__('Back')?></a>
	</span>

<?else:?>
	<?switch(get_data('type')){
		case 'swf':?>
		<div>
		<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="800" height="600" id="tao_item" align="middle">
			<param name="allowScriptAccess" value="sameDomain" />
			<param name="movie" value="<?=get_data('authoringFile')?>?localXmlFile=<?=get_data('dataPreview')?>&instance=<?=get_data('uri')?>" />
			<param name="quality" value="high" />
			<param name="wmode" value="opaque" />
			<param name="bgcolor" value="#ffffff" />
			<embed src="<?=get_data('authoringFile')?>?localXmlFile=<?=get_data('dataPreview')?>&instance=<?=get_data('uri')?>" quality="high" bgcolor="#ffffff" width="800" height="600" align="middle" wmode="opaque" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
		</object>
		</div>
		<?break;
		
		case 'php':?>
		
		<?break;
		
	}?>
	
<?endif?>
</div>

<?include('footer.tpl')?>
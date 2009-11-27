<?include('header.tpl')?>


<?if(get_data('error')):?>
	<div class="main-container">
		<div class="ui-state-error ui-corner-all" style="padding:5px;">
			<?=__('Please select an item before!')?>
		</div>
		<br />
		<span class="ui-widget ui-state-default ui-corner-all" style="padding:5px;">
			<a href="#" onclick="selectTabByName('manage_items');"><?=__('Back')?></a>
		</span>
	</div>
<?else:?>

<div class="main-container" style="display:none;"></div>
<div id="authoring-container" class="ui-helper-reset"  >
	<?switch(get_data('type')){
		case 'swf':?>
		
		<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="800" height="600" id="tao_item" align="middle">
			<param name="allowScriptAccess" value="sameDomain" />
			<param name="movie" value="<?=get_data('authoringFile')?>?xml=<?=get_data('dataPreview')?>&instance=<?=get_data('instanceUri')?>" />
			<param name="quality" value="high" />
			<param name="wmode" value="opaque" />
			<param name="bgcolor" value="#ffffff" />
			<embed src="<?=get_data('authoringFile')?>?xml=<?=get_data('dataPreview')?>&instance=<?=get_data('instanceUri')?>" quality="high" bgcolor="#ffffff" width="800" height="600" align="middle" wmode="opaque" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
		</object>
		<?break;
		
		case 'php':?>
		<iframe  src="<?=get_data('authoringFile')?>?xml=<?=get_data('dataPreview')?>&instance=<?=get_data('instanceUri')?>" style="border-width:0px;width:100%; height:100%;overflow:scroll;" />
		<?break;
		
	}?>
</div>
<br />
<span class="ui-widget ui-state-default ui-corner-all" style="padding:5px;">
	<a href="#" onclick="selectTabByName('manage_items');"><?=__('Back')?></a>
</span>
	
<?endif?>


<?include('footer.tpl')?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>TAO</title>
	</head>
	<body>
		<?if(get_data('preview')):?>
		<div>
		<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="800" height="600" id="tao_item" align="middle">
			<param name="allowScriptAccess" value="sameDomain" />
			<param name="movie" value="<?=get_data('swf')?>?localXmlFile=<?=get_data('contentUrl')?>&instance=<?=get_data('instanceUri')?>" />
			<param name="wmode" value="opaque" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#ffffff" />
			<embed src="<?=get_data('swf')?>?localXmlFile=<?=get_data('contentUrl')?>&instance=<?=get_data('instanceUri')?>" quality="high" bgcolor="#ffffff" width="800" height="600" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
		</object>
		</div>
		<?else:?>
			<?=__('PREVIEW BOX')?><br /><br />
			<?=get_data('previewMsg')?>
		<?endif?>
		
	</body>
</html>

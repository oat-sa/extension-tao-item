<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?=PRODUCT_NAME?> <?=TAO_VERSION?></title>
       
    <link rel="stylesheet" type="text/css" href="<?=ROOT_URL?>tao/views/css/tao-main-style.css" />
    <link rel="stylesheet" type="text/css" href="<?=ROOT_URL?>taoQtiItem/views/css/qti.css" />
    <link rel="stylesheet" type="text/css" href="<?=ROOT_URL?>taoItems/views/css/quick-preview.css" />

        <?if(has_data('previewUrl')):?>
         <script type="text/javascript" src="<?=TAOBASE_WWW?>js/lib/require.js"></script>
            <script type="text/javascript">
            (function(){
                var clientConfigUrl = '<?=get_data('client_config_url')?>';
                require([clientConfigUrl], function(){
                    require(['taoItems/controller/preview/itemRunner'], function(itemRunner){
                        itemRunner.start({
                             <?if(has_data('resultServer')):?>
                            resultServer : <?=json_encode(get_data('resultServer'))?>,
                            <?endif?>
                            previewUrl : <?=json_encode(get_data('previewUrl'))?>,
                            clientConfigUrl : clientConfigUrl
                        });
                    });
                });
            }());
        </script>
        <?endif?>

</head>
<body class='quick-preview'>
	
	<?if(has_data('previewUrl')):?>
		
            <iframe id='preview-container' name="preview-container"></iframe>
       <?else:?>
            <h3><?=__('PREVIEW BOX')?></h3>
            <p><?=__("Not yet available")?></p>
	<?endif?>

</body>
</html>

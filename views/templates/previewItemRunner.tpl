<?php
use oat\tao\helpers\Template;
?><link rel="stylesheet" type="text/css" <?= Template::css('tao-main-style.css', 'tao') ?> />
<link rel="stylesheet" type="text/css" <?= Template::css('qti.css', 'taoQtiItem') ?> />
<link rel="stylesheet" type="text/css" <?= Template::css('preview.css', 'taoItems') ?> />

<?php if(has_data('previewUrl')):?>
<script>
requirejs.config({
   config: {
       'taoItems/controller/preview/itemRunner' : {
           <?php if(has_data('resultServer')):?>
           resultServer : <?=json_encode(get_data('resultServer'))?>,
           <?php endif?>
           previewUrl : <?=json_encode(get_data('previewUrl'))?>,
           userInfoServiceRequestUrl : <?=json_encode(_url('getUserPropertyValues', 'ServiceModule', 'tao'))?>,
           clientConfigUrl : '<?=get_data('client_config_url')?>',
           timeout : <?=get_data('client_timeout')?>
       }
   } 
});


$(document).ready(function() {
	if ("<?= get_data('previewUrl')?>".indexOf('Qti') != -1) {
		$('#submit > button').css('display', 'inline').click(function() {
			$('#preview-container')[0].contentWindow.qtiRunner.validate();
		});
	}
});
</script>
<?php endif?>

<div class="main-container">

	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?= __('Preview')?>
	</div>
	<div class="ui-widget ui-widget-content">
	
	<?php if(has_data('previewUrl')):?>
		
		<iframe id='preview-container' name="preview-container"></iframe>
		<!-- to emulate wf navigaton: <button id='finishButton' ><?=__('Finish')?></button> -->
		<div id="submit" class="tao-scope">
			<button class="btn-info"><?= __("Submit"); ?></button>
		</div>
		<div id='preview-console'>
			<div class="console-control">
				<span class="ui-icon ui-icon-circle-close" title="<?=__('close')?>"></span>
				<span class="ui-icon ui-icon-circle-plus toggler" title="<?=__('show/hide')?>"></span>
				<span class="ui-icon ui-icon-trash" title="<?=__('clean up')?>"></span>
				<?=__('Preview Console')?> 
			</div>
			<div class="console-content"><ul></ul></div>
		</div>

	<?php else:?>
			<h3><?=__('PREVIEW BOX')?></h3>
			<p><?=__("Not yet available")?></p>
	<?php endif?>
	</div>
</div>

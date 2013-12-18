<link rel="stylesheet" type="text/css" href="<?=ROOT_URL?>taoItems/views/css/preview.css" />

<?if(has_data('previewUrl')):?>
<script type='text/javascript'>
requirejs.config({
   config: {
       'taoItems/controller/preview/itemRunner' : {
           <?if(has_data('resultServer')):?>
           resultServer : <?=json_encode(get_data('resultServer'))?>,
           <?endif?>
           previewUrl : <?=json_encode(get_data('previewUrl'))?>
       }
   } 
});
</script>
<?endif?>

<div class="main-container">

	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?= __('Preview')?>
	</div>
	<div class="ui-widget ui-widget-content">
	
	<?if(has_data('previewUrl')):?>
		
		<iframe id='preview-container' name="preview-container"></iframe>
		<!-- to emulate wf navigaton: <button id='finishButton' ><?=__('Finish')?></button> -->
		
		<div id='preview-console'>
			<div class="console-control">
				<span class="ui-icon ui-icon-circle-close" title="<?=__('close')?>"></span>
				<span class="ui-icon ui-icon-circle-plus toggler" title="<?=__('show/hide')?>"></span>
				<span class="ui-icon ui-icon-trash" title="<?=__('clean up')?>"></span>
				<?=__('Preview Console')?> 
			</div>
			<div class="console-content"><ul></ul></div>
		</div>

	<?else:?>
			<h3><?=__('PREVIEW BOX')?></h3>
			<p><?=__("Not yet available")?></p>
	<?endif?>
	</div>
</div>
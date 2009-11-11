<script type="text/javascript">
var ctx_extension 	= "<?=get_data('extension')?>";
var ctx_module 		= "<?=get_data('module')?>";
var ctx_action 		= "<?=get_data('action')?>";
$(function(){
	
	<?if(get_data('uri') && get_data('classUri')):?>
		$("#comment-form-container").dialog('destroy');
		getMetaData("<?=get_data('uri')?>", "<?=get_data('classUri')?>");
	<?else:?>
		$("#section-meta").empty();
	<?endif?>
	
	<?if(get_data('reload') === true):?>	
		
	loadControls();
	
	<?else:?>
	
	initActions();
	
	<?endif?>
});
</script>
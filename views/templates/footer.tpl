<script type="text/javascript">
var ctx_extension 	= "<?=get_data('extension')?>";
var ctx_module 		= "<?=get_data('module')?>";
var ctx_action 		= "<?=get_data('action')?>";
$(function(){
	
	<?if(get_data('uri') && get_data('classUri')):?>
		$("#comment-form-container").dialog('destroy');
		getMetaData("<?=get_data('uri')?>", "<?=get_data('classUri')?>");
		<?if(get_data('action') != 'authoring'):?>
		index = getTabIndexByName('item_authoring');
		tabs.tabs('url', index, "/taoItems/Items/authoring?uri=<?=get_data('uri')?>&classUri=<?=get_data('classUri')?>");
		tabs.tabs('enable', index);
		<?endif?>
	<?else:?>
		$("#section-meta").empty();
		<?if(get_data('action') != 'authoring'):?>
			tabs.tabs('disable', getTabIndexByName('item_authoring'));
		<?endif?>
	<?endif?>
	
	<?if(get_data('reload') === true):?>	
		
	loadControls();
	
	<?else:?>
	
		<?if(get_data('action') != 'authoring'):?>
		initActions();
		<?endif?>
	<?endif?>
	
});
</script>
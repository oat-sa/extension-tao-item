<script type="text/javascript">
var ctx_extension 	= "<?=get_data('extension')?>";
var ctx_module 		= "<?=get_data('module')?>";
var ctx_action 		= "<?=get_data('action')?>";
$(function(){
	
	authoringIndex = getTabIndexByName('items_authoring');
	previewIndex = getTabIndexByName('items_preview');
	
	<?if(get_data('uri') && get_data('classUri')):?>
		
		if(ctx_action != 'authoring'){
			UiBootstrap.tabs.tabs('url', authoringIndex, "<?=_url('authoring', 'Items', 'taoItems', array('uri' => get_data('uri'), 'classUri' => get_data('classUri')))?>");
			UiBootstrap.tabs.tabs('enable', authoringIndex);
		}
		if(ctx_action != 'preview'){
			UiBootstrap.tabs.tabs('url', previewIndex, "<?=_url('preview', 'Items', 'taoItems', array('uri' => get_data('uri'), 'classUri' => get_data('classUri')))?>");
			UiBootstrap.tabs.tabs('enable', previewIndex);
		}
	<?else:?>
	
		if(ctx_action != 'authoring'){
			UiBootstrap.tabs.tabs('disable', authoringIndex);
		}
		if(ctx_action != 'preview'){
			UiBootstrap.tabs.tabs('disable', previewIndex);
		}
		
	<?endif?>
	
	<?if(get_data('reload')):?>
		uiBootstrap.initTrees();
	<?endif?>
	
});
</script>

<script type="text/javascript">
var ctx_extension 	= "<?=get_data('extension')?>";
var ctx_module 		= "<?=get_data('module')?>";
var ctx_action 		= "<?=get_data('action')?>";
$(function(){
	
	index = getTabIndexByName('items_authoring');
	
	<?if(get_data('uri') && get_data('classUri')):?>
		
		<?if(get_data('action') != 'authoring' && get_data('modelDefined') != false):?>
		
			UiBootstrap.tabs.tabs('url', index, "/taoItems/Items/authoring?uri=<?=get_data('uri')?>&classUri=<?=get_data('classUri')?>");
			UiBootstrap.tabs.tabs('enable', index);
		
		<?endif?>
	<?else:?>
	
		if(ctx_action != 'authoring'){
			UiBootstrap.tabs.tabs('disable', index);
		}
		
	<?endif?>
	
	<?if(get_data('reload')):?>
		uiBootstrap.initTrees();
	<?endif?>
	
});
</script>

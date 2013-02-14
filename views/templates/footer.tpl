<script type="text/javascript">
	var ctx_extension = "<?=get_data('extension')?>";
	var ctx_module = "<?=get_data('module')?>";
	var ctx_action = "<?=get_data('action')?>";

	$(function(){
		require(['require', 'jquery'], function (req, $) {
			authoringIndex = helpers.getTabIndexByName('items_authoring');
			previewIndex = helpers.getTabIndexByName('items_preview');

<?if(get_data('uri') && get_data('classUri')):?>
<?	if(get_data('isAuthoringEnabled')):?>
			if(ctx_action != 'authoring'){
				uiBootstrap.tabs.tabs('url', authoringIndex, "<?=_url('authoring', 'Items', 'taoItems', array('uri' => get_data('uri'), 'classUri' => get_data('classUri')))?>");
				uiBootstrap.tabs.tabs('enable', authoringIndex);
			}
<?	endif;?>
			if(ctx_action != 'preview'){
				uiBootstrap.tabs.tabs('url', previewIndex, "<?=_url('preview', 'Items', 'taoItems', array('uri' => get_data('uri'), 'classUri' => get_data('classUri')))?>");
				uiBootstrap.tabs.tabs('enable', previewIndex);
			}
<?else:?>
			if(ctx_action != 'authoring'){
				uiBootstrap.tabs.tabs('disable', authoringIndex);
			}
			if(ctx_action != 'preview'){
				uiBootstrap.tabs.tabs('disable', previewIndex);
			}
<?endif?>

<?if(get_data('reload')):?>
			uiBootstrap.initTrees();
<?endif?>
		});
	});
</script>

<?include(TAO_TPL_PATH . 'header.tpl')?>

<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		var deprecated = <?=get_data('deprecatedOptions')?>;
	<?if(get_data('isDeprecated') === true):?>
		UiBootstrap.tabs.tabs('disable', getTabIndexByName('items_authoring'));
		UiBootstrap.tabs.tabs('disable', getTabIndexByName('items_preview'));
		$(':radio').each(function(){
			$(this).attr('disabled', 'true');
			$("label[for='"+$(this).attr('id')+"']").css('color', '#A0A0A0');
		});
	<?else:?>
		$(':radio').each(function(){
			if($.inArray($(this).val(), deprecated) > -1){
				$(this).attr('disabled', 'true');
				$("label[for='"+$(this).attr('id')+"']").css('color', '#A0A0A0');
			}
		});

		var $authoringButton = $('input[name="<?=tao_helpers_Uri::encode(TAO_ITEM_CONTENT_PROPERTY)?>"]');
		$authoringButton.after('<input id="content-button" type="button" value="Content"/>');
		$('#content-button').click(function(){
			var url = '';
			if(ctx_extension){
				url = root_url + '/' + ctx_extension + '/' + ctx_module + '/';
			}
			url += 'itemContentIO';
			var data = {
				'uri':$("#uri").val()
			};

			if(UiBootstrap.tabs.size() == 0){
				if($('div.main-container').length){
					$('div.main-container').load(url, data);
				}
			}
			$(getMainContainerSelector(UiBootstrap.tabs)).load(url, data);
			return false;
		});
		<?if(!get_data('isAuthoringEnabled')):?>
			$authoringButton.hide();
			UiBootstrap.tabs.tabs('disable', getTabIndexByName('items_authoring'));
		<?endif;?>

		<?if(GENERIS_VERSIONING_ENABLED):?>
		//append versioned item management manually :
		if($authoringButton.length){
			$authoringButton.after('<input id="versioned-item-content" type="button" value="Versioned Item content"/>');
			$('#versioned-item-content').click(function(){
				var url = '';
				if(ctx_extension){
					url = root_url + '/' + ctx_extension + '/' + ctx_module + '/';
				}
				url += 'itemVersionedContentIO';
				var data = {
					'uri':$("#uri").val()
				};

				if(UiBootstrap.tabs.size() == 0){
					if($('div.main-container').length){
						$('div.main-container').load(url, data);
					}
				}
				$(getMainContainerSelector(UiBootstrap.tabs)).load(url, data);
				return false;
			});
		}
		<?endif;?>
	<?endif?>
	});
</script>
<?if(!get_data('isDeprecated')):?>
	<?include('footer.tpl');?>
<?endif?>
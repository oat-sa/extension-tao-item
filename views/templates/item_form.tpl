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
	<?endif?>
	});
</script>
<?if(!get_data('isDeprecated')):?>
	<?include('footer.tpl');?>
<?endif?>
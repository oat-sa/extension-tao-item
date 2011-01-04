<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/preview.css" />

<div class="main-container">

	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=get_data('previewTitle')?>
		<?if(get_data('preview')):?>
		<span id="preview-options-opener">
			<a href="#" ><img src="<?=BASE_WWW?>/img/options.png" class="icon" alt="options" /><?=__('Options')?><span class="ui-icon ui-icon-carat-1-e"></span></a>
		</span>
		<?endif?>
	</div>
	<div class="ui-widget ui-widget-content">
	
	<?if(get_data('preview')):?>
		
		<div id='preview-options'>
			<?=get_data('optionsForm')?>
		</div>
	
		<iframe id='preview-container' name="preview-container" src="<?=get_data('previewUrl')?>" />
	
		<div id='preview-console'>
			<div class="console-control">
				<span class="ui-icon ui-icon-circle-close" title="<?=__('close')?>"></span>
				<span class="ui-icon ui-icon-circle-plus toggler" title="<?=__('show/hide')?>"></span>
				<span class="ui-icon ui-icon-trash" title="<?=__('clean up')?>"></span>
				<?=__('Preview Console')?> 
			</div>
			<div class="console-content"><ul></ul></div>
		</div>

	<script type="text/javascript">
	$(document).ready(function(){

		//preview form toggling
		$('#preview-options-opener').click(function(){
			var fromClass 	= 'ui-icon-carat-1-s';
			var toClass 	= 'ui-icon-carat-1-e';
			if($('#preview-options').css('display') == 'none'){
				fromClass 	= 'ui-icon-carat-1-e';
				toClass 	= 'ui-icon-carat-1-s';
			}
			$(this).find('span.ui-icon').switchClass(fromClass,toClass);
			$('#preview-options').toggle();
		});

		//prevent wrong iframe loading from chrome
		if($.browser.webkit){
			$("#preview-container").attr('src', $("#preview-container").attr('src'));
		}
	});
	</script>

	<?else:?>
			<h3><?=__('PREVIEW BOX')?></h3>
			<p><?=get_data('previewMsg')?></p>
	<?endif?>
	</div>
</div>
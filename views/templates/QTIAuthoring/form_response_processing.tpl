<div id="qtiAuthoring_processingEditor_formContainer" class="ext-home-container ui-state-highlight">
	<p><?=get_data('warningMessage')?></p>
	<?=get_data('form')?>
</div>

<script type="text/javascript">
	
	// myItem.setResponseMappingMode(<?=get_data('responseMappingMode')?>);
	
	var warningMessage = "<?=get_data('warningMessage')?>"
	if(warningMessage.length){
		alert(warningMessage);
	}
	
	$(document).ready(function(){
		$('#qtiAuthoring_processingEditor_formContainer').find('.form-submiter').click(function(){
			var $form = $('#qtiAuthoring_processingEditor_formContainer').find('form');
			if($form.length){
				myItem.saveResponseProcessing($form);
			}
		});
	
	});
</script>
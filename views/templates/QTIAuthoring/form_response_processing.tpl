<div id="qtiAuthoring_processingEditor_formContainer" class="ext-home-container ui-state-highlight">
	<p><?=get_data('warningMessage')?></p>
	<?=get_data('form')?>
</div>

<script type="text/javascript">
	// myItem.setResponseMappingMode(<?=get_data('responseMappingMode')?>);
	var warningMessage = "<?=get_data('warningMessage')?>"
	if (warningMessage.length) {
		alert(warningMessage);
	}

	$(function(){
setTimeout(function(){
		$('#qtiAuthoring_processingEditor_formContainer .form-submiter').off('click').on('click', function(e){
			e.preventDefault();
			myItem.saveItemResponseProcessing($('#qtiAuthoring_processingEditor_formContainer form'));
		});}, 2000);
	});
</script>
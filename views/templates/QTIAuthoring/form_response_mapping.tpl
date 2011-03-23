<div id="qtiAuthoring_mappingEditor" class="qti-authoring-form-container">
	
		<?=get_data('form')?>
	
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#qtiAuthoring_mappingEditor').find('.form-submiter').click(function(){
		var $form = $('#qtiAuthoring_mappingEditor').find('form');
		if($form.length){
			myInteraction.saveResponseMappingOptions($form);
		}
	});

});
</script>
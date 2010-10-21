<div id="qtiAuthoring_mapping_title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=__('Mapping options:')?>
</div>
<div id="qtiAuthoring_mappingEditor" class="ui-widget-content ui-corner-bottom qti-authoring-form-container">
	
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
<div id="qtiAuthoring_scoring_title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=__('Mapping options:')?>
</div>
<div id="qtiAuthoring_scoringEditor" class="ui-widget-content ui-corner-bottom">
	<div class="ext-home-container ui-state-highlight">
		<?get_data('form')?>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#qtiAuthoring_scoringEditor').find('.form-submiter').click(function(){
			var $form = $('#qtiAuthoring_scoringEditor').find('form');
			if($form.length){
				interactionEdit.saveResponseMappingOptions($form);
			}
		});
	
	});
</script>
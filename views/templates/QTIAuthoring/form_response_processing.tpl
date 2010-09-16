<div id="qtiAuthoring_processingEditor_formContainer" class="ext-home-container ui-state-highlight">
	<?=get_data('form')?>
</div>

<script type="text/javascript">
	<?if(get_data('responseMappingMode')):?>
	//set the reponse mapping to true:
	if(qtiEdit.responseMappingMode){
		//do nothing:
	}else{
		//display the scoring form: //TODO: load it only when necessary:
		qtiEdit.responseMappingMode = true;
		$(qtiEdit.responseMappingOptionsFormContainer).show();
	}
	<?else:?>
	qtiEdit.responseMappingMode = false;
	$(qtiEdit.responseMappingOptionsFormContainer).hide();
	<?endif;?>
	
	
	$(document).ready(function(){
		$('#qtiAuthoring_processingEditor_formContainer').find('.form-submiter').click(function(){
			var $form = $('#qtiAuthoring_processingEditor_formContainer').find('form');
			if($form.length){
				qtiEdit.saveResponseProcessing($form);
			}
		});
	
	});
</script>
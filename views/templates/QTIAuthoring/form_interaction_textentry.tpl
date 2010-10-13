
<div id="formInteraction_title_<?=get_data('interactionSerial')?>" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
		<?=__('Interaction editor:')?>
</div>
<div id="formInteraction_content_<?=get_data('interactionSerial')?>" class="ui-widget-content ui-corner-bottom">
	<div class="ext-home-container ui-state-highlight">
		<?=get_data('formInteraction')?>
	</div>
</div>

<script type="text/javascript">
/*
$(document).ready(function(){
	interactionEdit.interactionSerial = '<?=get_data('interactionSerial')?>';
	interactionEdit.initInteractionFormSubmitter();
	
	//add the listener to the form changing 
	interactionEdit.setFormChangeListener();//all form
	
	//always load the mappingForm (show and hide it according to the value of the qtiEdit.responseMappingMode)
	interactionEdit.loadResponseMappingForm();
	
	interactionEdit.modifiedChoices = [];//no choice for such an interaction
});
*/
var myInteraction = null;
$(document).ready(function(){
	try{
		myInteraction = new interactionClass('<?=get_data('interactionSerial')?>', myItem.itemSerial);
	}catch(err){
		CL('error creating interaction', err);
	}
	
});
</script>


<div id="formInteraction_title_<?=get_data('interactionSerial')?>" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
		<?=__('Interaction editor:')?>
</div>
<div id="formInteraction_content_<?=get_data('interactionSerial')?>" class="ui-widget-content ui-corner-bottom">
	<div class="ext-home-container ui-state-highlight">
		<?=get_data('formInteraction')?>
	</div>
	
	<div id="formChoices_container" class="ext-home-container">
	</div>
	
</div>

<script type="text/javascript">
matchInteractionEdit = new Object();
matchInteractionEdit.setOrderedChoicesButtons = function(doubleList){
	
	// var length = doubleList.length;
	for(var groupSerial in doubleList){
		// interactionEdit.setOrderedChoicesButtons(doubleList[j]);
		var list = doubleList[groupSerial];
		var total = list.length;
		for(var i=0; i<total; i++){
			if(!list[i]){
				throw 'broken order in array';
				break;
			}
			
			$upElt = $('<span id="up_'+list[i]+'" title="'+__('Move Up')+'" class="form-group-control ui-icon ui-icon-circle-triangle-n"></span>');
			
			//get the corresponding group id:
			$("#a_choicePropOptions_"+list[i]).after($upElt);
			$upElt.bind('click', {'groupSerial':groupSerial}, function(e){
				var choiceSerial = $(this).attr('id').substr(3);
				interactionEdit.orderedChoices[e.data.groupSerial] = interactionEdit.switchOrder(interactionEdit.orderedChoices[e.data.groupSerial], choiceSerial, 'up');
			});
			
			$downElt = $('<span id="down_'+list[i]+'" title="'+__('Move Down')+'" class="form-group-control ui-icon ui-icon-circle-triangle-s"></span>');
			$upElt.after($downElt);
			$downElt.bind('click', {'groupSerial':groupSerial}, function(e){
				var choiceSerial = $(this).attr('id').substr(5);
				interactionEdit.orderedChoices[e.data.groupSerial] = interactionEdit.switchOrder(interactionEdit.orderedChoices[e.data.groupSerial], choiceSerial, 'down');
			});
		}
	}
}


$(document).ready(function(){
	interactionEdit.interactionSerial = '<?=get_data('interactionSerial')?>';
	interactionEdit.initInteractionFormSubmitter();
	
	//always load the mappingForm (show and hide it according to the value of the qtiEdit.responseMappingMode)
	interactionEdit.loadResponseMappingForm();
	
	interactionEdit.choicesFormContainer = '#formChoices_container';
	interactionEdit.loadChoicesForm(interactionEdit.choicesFormContainer);
});
</script>

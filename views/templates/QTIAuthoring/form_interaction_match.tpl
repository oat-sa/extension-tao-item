
<div id="formInteraction_title_<?=get_data('interactionSerial')?>" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
		<?=__('Interaction editor:')?>
</div>
<div id="formInteraction_content_<?=get_data('interactionSerial')?>" class="ui-widget-content ui-corner-bottom">
	<div class="ext-home-container ui-state-highlight">
		<?=get_data('formInteraction')?>
	</div>
	
	<div class="ext-home-container">
		<div id="formChoices_title" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
				<?=__('Choices editor:')?>
		</div>
		<?  $formChoices = get_data('formChoices');
			$groupSerials = get_data('groupSerials'); ?>
			
		<?foreach($groupSerials as $order => $groupSerial):?>
		<div id="formContainer_choices_container_<?=$groupSerial?>" class="ui-widget-content ui-corner-bottom formContainer_choices" style="padding:15px;">
			<div id="formContainer_choices_<?=$groupSerial?>">
			<?foreach($formChoices[$groupSerial] as $choiceId => $choiceForm):?>
				<div id='<?=$choiceId?>' class='formContainer_choice'>
					<?=$choiceForm?>
				</div>
			<?endforeach;?>
			</div>

			<div id="add_choice_button_<?=$groupSerial?>">
				<a href="#"><img src="<?=ROOT_URL?>/tao/views/img/save.png"> Add a choice</a>
			</div>
		</div>
		<?endforeach;?>
				
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
	
	<?foreach($groupSerials as $order => $groupSerial):?>
	$('#add_choice_button_<?=$groupSerial?>').click(function(){
		//add a choice to the current interaction:
		interactionEdit.addChoice(interactionEdit.interactionSerial, $('#formContainer_choices_<?=$groupSerial?>'), 'formContainer_choice', '<?=$groupSerial?>');//need an extra param "groupSerial"
		return false;
	});
	<?endforeach;?>
	
	//add adv. & delete button
	interactionEdit.initToggleChoiceOptions();
	
	//add move up and down button
	interactionEdit.orderedChoices = [];//double dimension array:
	// var i=0;//i={0,1}
	<?foreach(get_data('orderedChoices') as $groupSerial => $group):?>
		interactionEdit.orderedChoices['<?=$groupSerial?>'] = [];
		<?foreach($group as $choice):?>
			interactionEdit.orderedChoices['<?=$groupSerial?>'].push('<?=$choice->getSerial()?>');
		<?endforeach;?>
		// i++;
	<?endforeach;?>
	
	matchInteractionEdit.setOrderedChoicesButtons(interactionEdit.orderedChoices);
	
	//add the listener to the form changing 
	interactionEdit.setFormChangeListener();//all form
	
	//always load the mappingForm (show and hide it according to the value of the qtiEdit.responseMappingMode)
	interactionEdit.loadResponseMappingForm();/**/
	
});
</script>

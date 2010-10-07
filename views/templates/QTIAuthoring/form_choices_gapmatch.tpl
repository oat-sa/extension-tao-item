
<div id="formChoices_title" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
		<?=__('Choices editor:')?>
</div>

<div id="formContainer_groups_container" class="ui-widget-content ui-corner-bottom formContainer_choices" style="padding:15px;">
	<div id="formContainer_groups">
	<? $formChoices = get_data('formChoices');?>
	<?foreach($formGroups as $groupSerial => $groupForm):?>
		<div id='<?=$groupSerial?>' class='formContainer_choice'>
			<?=$groupForm?>
		</div>
	<?endforeach;?>
	</div>
</div>

<div id="formContainer_choices_container" class="ui-widget-content ui-corner-bottom formContainer_choices" style="padding:15px;">
	<div id="formContainer_choices">
	<?foreach($formChoices as $choiceSerial => $choiceForm):?>
		<div id='<?=$choiceSerial?>' class='formContainer_choice'>
			<?=$choiceForm?>
		</div>
	<?endforeach;?>
	</div>

	<div id="add_choice_button">
		<a href="#"><img src="<?=ROOT_URL?>/tao/views/img/save.png"> Add a choice</a>
	</div>
</div>


<script type="text/javascript">

$(document).ready(function(){
	$('#add_choice_button').click(function(){
		//add a choice to the current interaction:
		interactionEdit.addChoice(interactionEdit.interactionSerial, $('#formContainer_choices'), 'formContainer_choice');//need an extra param "groupSerial"
		return false;
	});
	
	//add adv. & delete button
	interactionEdit.initToggleChoiceOptions();
	
	//add move up and down button to choices only (not groups!!)
	interactionEdit.orderedChoices = [];
	<?foreach(get_data('orderedChoices') as $order => $choice):?>
	interactionEdit.orderedChoices[<?=$order?>] = '<?=$choice->getSerial()?>';
	<?endforeach;?>
	
	interactionEdit.setOrderedChoicesButtons(interactionEdit.orderedChoices);
	
	//add the listener to the form changing 
	interactionEdit.setFormChangeListener();//all form
	
});
</script>

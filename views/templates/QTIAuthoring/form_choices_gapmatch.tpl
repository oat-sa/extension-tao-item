<div id="formChoices_title" class="ui-widget-header ui-corner-top ui-state-default qti-authoring-form-container">
		<?=__('Choices editor:')?>
</div>
<div class="ui-widget-content ui-corner-bottom qti-authoring-form-container">
	<div id="formContainer_groups_container" class="qti-authoring-form-container-column">
		<div id="formContainer_groups" class="formContainer_choices">
		<? $formChoices = get_data('formChoices');?>
		<?foreach($formGroups as $groupSerial => $groupForm):?>
			<div id='<?=$groupSerial?>' class="formContainer_choice">
				<?=$groupForm?>
			</div>
		<?endforeach;?>
		</div>
	</div>

	<div id="formContainer_choices_container" class="qti-authoring-form-container-column">
		<div id="formContainer_choices" class="formContainer_choices">
		<?foreach($formChoices as $choiceSerial => $choiceForm):?>
			<div id='<?=$choiceSerial?>' class="formContainer_choice">
				<?=$choiceForm?>
			</div>
		<?endforeach;?>
		</div>

		<div id="add_choice_button" class="add_choice_button">
			<a href="#"><img src="<?=ROOT_URL?>/tao/views/img/add.png"> Add a choice</a>
		</div>
	</div>
	
	<div style="clear:both" />
</div>	

<script type="text/javascript">

$(document).ready(function(){
	$('a.form-choice-adder, #add_choice_button').click(function(){
		//add a choice to the current interaction:
		myInteraction.addChoice(myInteraction.interactionSerial, $('#formContainer_choices'), 'formContainer_choice');//need an extra param "groupSerial"
		return false;
	});
	
	//add adv. & delete button
	myInteraction.initToggleChoiceOptions();
	
	//add move up and down button to choices only (not groups!!)
	myInteraction.orderedChoices = [];
	<?foreach(get_data('orderedChoices') as $order => $choice):?>
	myInteraction.orderedChoices[<?=$order?>] = '<?=$choice->getSerial()?>';
	<?endforeach;?>
	
	myInteraction.setOrderedChoicesButtons(myInteraction.orderedChoices);
	
	//add the listener to the form changing 
	myInteraction.setFormChangeListener();//all form
	
});
</script>

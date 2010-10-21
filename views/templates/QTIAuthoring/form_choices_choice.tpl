<div id="formChoices_title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=__('Choices editor:')?>
</div>
<div id="formContainer_choices_title" class="ui-widget-content ui-corner-bottom formContainer_choices qti-authoring-form-container">
	<div id="formContainer_choices">
	<?foreach(get_data('formChoices') as $choiceId => $choiceForm):?>
		<div id='<?=$choiceId?>' class='formContainer_choice'>
			<?=$choiceForm?>
		</div>
	<?endforeach;?>
	</div>

	<div id="add_choice_button" class="add_choice_button">
		<a href="#"><img src="<?=ROOT_URL?>/tao/views/img/add.png"> Add a choice</a>
	</div>
</div>


<script type="text/javascript">
$(document).ready(function(){
	
	$('a.form-choice-adder, #add_choice_button').click(function(){
		//add a choice to the current interaction:
		myInteraction.addChoice($('#formContainer_choices'), 'formContainer_choice');
		return false;
	});
	
	//add adv. & delete button
	myInteraction.initToggleChoiceOptions();
	
	//add move up and down button
	myInteraction.orderedChoices = [];
	<?foreach(get_data('orderedChoices') as $choice):?>
		myInteraction.orderedChoices.push('<?=$choice->getSerial()?>');
	<?endforeach;?>
	myInteraction.setOrderedChoicesButtons(myInteraction.orderedChoices);
	
	//add the listener to the form changing 
	myInteraction.setFormChangeListener();//all form
	
});
</script>

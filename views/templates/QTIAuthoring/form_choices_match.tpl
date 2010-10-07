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


<script type="text/javascript">
$(document).ready(function(){
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
	
});
</script>

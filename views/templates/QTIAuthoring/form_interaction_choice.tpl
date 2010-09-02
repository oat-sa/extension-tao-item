
<div id="formInteraction_title_<?=get_data('interactionId')?>" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
		<?=__('Interaction editor:')?>
</div>
<div id="formInteraction_content_<?=get_data('interactionId')?>" class="ui-widget-content ui-corner-bottom">
	<div class="ext-home-container ui-state-highlight">
		<?=get_data('formInteraction')?>
	</div>
	
	<div class="ext-home-container">
		<div id="formChoices_title" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
				<?=__('Choices editor:')?>
		</div>
		<div id="formContainer_choices_title" class="ui-widget-content ui-corner-bottom formContainer_choices" style="padding:15px;">
			<div id="formContainer_choices">
			<?foreach(get_data('formChoices') as $choiceId => $choiceForm):?>
				<div id='<?=$choiceId?>' class='formContainer_choice'>
					<?=$choiceForm?>
					
				</div>
			<?endforeach;?>
			</div>
			<div id='add_choice_button'>
				<a href="#"><img src="<?=ROOT_URL?>/tao/views/img/save.png"> Add a choice</a>
			</div>
		</div>
	</div>
	
</div>

<script type="text/javascript">
$(document).ready(function(){
	
	interactionEdit.interactionId = '<?=get_data('interactionId')?>';
	interactionEdit.initInteractionFormSubmitter();
	
	$('#add_choice_button').click(function(){
		//add a choice to the current interaction:
		interactionEdit.addChoice(interactionEdit.interactionId, $('#formContainer_choices'), 'formContainer_choice');
		return false;
	});

	interactionEdit.initToggleChoiceOptions();
	
	interactionEdit.setFormChangeListener();//all form

});
</script>

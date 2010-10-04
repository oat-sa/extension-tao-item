
<div id="formInteraction_title_<?=get_data('interactionSerial')?>" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
		<?=__('Interaction editor:')?>
</div>
<div id="formInteraction_content_<?=get_data('interactionSerial')?>" class="ui-widget-content ui-corner-bottom">
	
	<div class="ext-home-container ui-state-highlight">
		<?=get_data('formInteraction')?>
	</div>
	
	<div class="ext-home-container">
		<div id="formChoices_title" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
				<?=__('Interaction content editor:')?>
		</div>
		<div id="formContainer_choices_title" class="ui-widget-content ui-corner-bottom formContainer_choices" style="padding:15px;">
			<textarea name="interactionEditor_wysiwyg_name" id="interactionEditor_wysiwyg"><?=get_data('interactionData')?></textarea>
		</div>
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
	
	interactionEdit.interactionSerial = '<?=get_data('interactionSerial')?>';
	interactionEdit.initInteractionFormSubmitter();
	
	
	
	$('#add_choice_button').click(function(){
		//append choice in the interaction editor:
		
		//add a choice to the current interaction:
		// interactionEdit.addChoice(interactionEdit.interactionSerial, $('#formContainer_choices'), 'formContainer_choice');
		return false;
	});
	
	//add advanced option button but not the delete button
	interactionEdit.initToggleChoiceOptions({'delete':false});
	
	/*
	//add move up and down button
	interactionEdit.orderedChoices = [];
	<?foreach(get_data('orderedChoices') as $choice):?>
		interactionEdit.orderedChoices.push('<?=$choice->getSerial()?>');
	<?endforeach;?>
	interactionEdit.setOrderedChoicesButtons(interactionEdit.orderedChoices);
	*/
	
	//add the listener to the form changing 
	interactionEdit.setFormChangeListener();//all form
	
	//always load the mappingForm (show and hide it according to the value of the qtiEdit.responseMappingMode)
	interactionEdit.loadResponseMappingForm();
	
	
	var createHotText = {
		visible : true,
		className: 'addInteraction',
		exec: function(){
			this.insertHtml('{qti_hottext_new}');
			interactionEdit.addHotText(this.getContent(), interactionEdit.interactionSerial);
		},
		tags: ['a'],
		tooltip: 'set hotText'
	};
	
	interactionEdit.buildInteractionEditor('#interactionEditor_wysiwyg', {'createHotText': createHotText});
	
	
});


</script>

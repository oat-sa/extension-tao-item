
<div id="formInteraction_title_<?=get_data('interactionSerial')?>" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
		<?=__('Interaction editor:')?>
</div>
<div id="formInteraction_content_<?=get_data('interactionSerial')?>" class="ui-widget-content ui-corner-bottom">
	<div class="ext-home-container ui-state-highlight">
		<?=get_data('formInteraction')?>
	</div>
	
	<div class="ext-home-container">
		<div id="formInteraction_title" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
				<?=__('Interaction content editor:')?>
		</div>
		<div id="formContainer_interaction" class="ui-widget-content ui-corner-bottom formContainer_choices" style="padding:15px;">
			<textarea name="interactionEditor_wysiwyg_name" id="interactionEditor_wysiwyg"><?=get_data('interactionData')?></textarea>
		</div>
	</div>
	
	<div id="formChoices_container" class="ext-home-container">
	</div>
	
</div>

<script type="text/javascript">
$(document).ready(function(){
	interactionEdit.interactionSerial = '<?=get_data('interactionSerial')?>';
	interactionEdit.initInteractionFormSubmitter();
		
	//always load the mappingForm (show and hide it according to the value of the qtiEdit.responseMappingMode)
	interactionEdit.loadResponseMappingForm();
	
	var createGap = {
		visible : true,
		className: 'addInteraction',
		exec: function(){
			this.insertHtml('{qti_gap_new}');
			interactionEdit.addGap(this.getContent(), interactionEdit.interactionSerial);
		},
		tooltip: 'add a gap'
	};
	
	interactionEdit.buildInteractionEditor('#interactionEditor_wysiwyg', {'createGap': createGap});
	
	interactionEdit.choicesFormContainer = '#formChoices_container';
	interactionEdit.loadChoicesForm(interactionEdit.choicesFormContainer);
});
</script>

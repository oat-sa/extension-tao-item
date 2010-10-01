
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
	
	//add adv. & delete button
	interactionEdit.initToggleChoiceOptions();
	
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
	
	
	interactionEdit.interactionEditor = new Object();
	interactionEdit.interactionDataContainer = '#interactionEditor_wysiwyg';
	
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
	
	var saveInteractionData = {
		visible : true,
		className: 'addInteraction',
		exec: function(){
			interactionEdit.saveInteractionData();
		},
		tooltip: 'save interaction data'
	};
	
	
	interactionEdit.interactionEditor = $(interactionEdit.interactionDataContainer).wysiwyg({
		controls: {
			  strikeThrough : { visible : true },
			  underline     : { visible : true },
			  
			  justifyLeft   : { visible : true },
			  justifyCenter : { visible : true },
			  justifyRight  : { visible : true },
			  justifyFull   : { visible : true },
			  
			  indent  : { visible : true },
			  outdent : { visible : true },
			  
			  subscript   : { visible : true },
			  superscript : { visible : true },
			  
			  undo : { visible : true },
			  redo : { visible : true },
			  
			  insertOrderedList    : { visible : true },
			  insertUnorderedList  : { visible : true },
			  insertHorizontalRule : { visible : true },

			  h4: {
					  visible: true,
					  className: 'h4',
					  command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
					  arguments: ($.browser.msie || $.browser.safari) ? '<h4>' : 'h4',
					  tags: ['h4'],
					  tooltip: 'Header 4'
			  },
			  h5: {
					  visible: true,
					  className: 'h5',
					  command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
					  arguments: ($.browser.msie || $.browser.safari) ? '<h5>' : 'h5',
					  tags: ['h5'],
					  tooltip: 'Header 5'
			  },
			  h6: {
					  visible: true,
					  className: 'h6',
					  command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
					  arguments: ($.browser.msie || $.browser.safari) ? '<h6>' : 'h6',
					  tags: ['h6'],
					  tooltip: 'Header 6'
			  },
			  cut   : { visible : true },
			  copy  : { visible : true },
			  paste : { visible : true },
			  html  : { visible: true },
			  addChoiceInteraction: {visible:false},
			  addAssociateInteraction: {visible:false},
			  addOrderInteraction: {visible:false},
			  addMatchInteraction: {visible:false},
			  addInlineChoiceInteraction: {visible:false},
			  addTextEntryInteraction: {visible:false},
			  addExtendedTextInteraction: {visible:false},
			  addHotTextInteraction: {visible:false},
			  saveItemData: {visible:false},
			  createHotText: createHotText,
			  saveInteractionData: saveInteractionData
			},
			events: {
				  keyup : function(e){
					if(interactionEdit.getDeletedChoices(true).length > 0){
						if(!confirm('please confirm deletion of the choice(s)')){
							// undo:
							interactionEdit.interactionEditor.wysiwyg('undo');
						}else{
							var deletedChoices = interactionEdit.getDeletedChoices();
							for(var key in deletedChoices){
								//delete choices one by one:
								interactionEdit.deleteChoice(deletedChoices[key]);
							}
						}
					}
				  }
			}
	});
	
	
	
});


</script>

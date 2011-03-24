<div id="qtiAuthoring_interaction_left_container">
	<div id="qtiAuthoring_interactionEditor"> 

		<div id="formInteraction_title_<?=get_data('interactionSerial')?>" class="ui-widget-header ui-corner-top ui-state-default">
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
					<span class="interactionEditor_wysiwyg_instruction"><?=__('To insert a "hot text" in your interaction, set focus on the desired place then click on the "set hot text" button ')?></span><img src="<?=BASE_WWW?>img/qtiAuthoring/add_hottext.png" title ="set hot text button" alt=""/>.
					<textarea name="interactionEditor_wysiwyg_name" id="interactionEditor_wysiwyg"><?=get_data('interactionData')?></textarea>
					<div id="interactionEditor_wysiwyg_addChoice"></div>
				</div>
			</div>
			
			<div id="formChoices_container" class="ext-home-container">
			</div>
			
		</div>
		
	</div>
</div>

<script type="text/javascript">
var myInteraction = null;
$(document).ready(function(){
	try{
		myInteraction = new interactionClass('<?=get_data('interactionSerial')?>', myItem.itemSerial, '#formChoices_container');
	}catch(err){
		CL('error creating interaction', err);
	}
	
	try{
		var createHotText = {
			visible : true,
			className: 'addHottext',
			exec: function(){
				this.insertHtml('{qti_hottext_new}');
				myInteraction.addHotText();
			},
			tags: ['a'],
			tooltip: 'set a hot text'
		};
	
		myInteraction.buildInteractionEditor('#interactionEditor_wysiwyg', {'createHotText': createHotText}, {css:"<?=BASE_WWW?>css/qtiAuthoringFrame.css"});
	}catch(err){
		CL('error building interaction data editor', err);
	}
});
</script>

<div id="qtiAuthoring_interaction_right_container">
<?include('form_response_container.tpl');?>
</div>
<div style="clear:both"/>
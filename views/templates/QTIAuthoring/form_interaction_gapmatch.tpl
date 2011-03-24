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
					<span class="interactionEditor_wysiwyg_instruction"><?=__('To insert a "gap" in your interaction, set focus on the desired place in the editor below then click on the "add gap" button ')?></span><img src="<?=BASE_WWW?>img/qtiAuthoring/add_gap.png" title ="add gap button" alt=""/>.
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
		var createGap = {
			visible : true,
			className: 'addGap',
			exec: function(){
				this.insertHtml('{qti_gap_new}');
				myInteraction.addGroup();
			},
			tooltip: 'add a gap'
		};
		
		myInteraction.buildInteractionEditor('#interactionEditor_wysiwyg', {'createGap': createGap}, {css:"<?=BASE_WWW?>css/qtiAuthoringFrame.css"});
	}catch(err){
		CL('error building interaction data editor', err);
	}
});
</script>

<div id="qtiAuthoring_interaction_right_container">
<?include('form_response_container.tpl');?>
</div>
<div style="clear:both"/>

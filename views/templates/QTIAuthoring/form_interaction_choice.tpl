

<div id="formInteraction_title_<?=get_data('interactionId')?>" class="ui-widget-header ui-corner-top ui-state-default" style="margin-top:10px;">
		<?=__('Interaction editor:')?>
</div>
<div id="formInteraction_content_<?=get_data('interactionId')?>" class="ui-widget-content ui-corner-bottom">
	<div class="ext-home-container ui-state-highlight">
		<?=get_data('formInteraction')?>
	</div>
</div>


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



<script type="text/javascript">
function toggleChoiceOptions($group){
	var groupId = $group.attr('id');
	if(groupId.indexOf('choicePropOptions') == 0){
		
		// it is a choice group:
		if($('#a_'+groupId).length){
			$('#a_'+groupId).remove();
		}
		
		var deleteElt = $('<span id="delete_'+groupId+'" title="<?=__('Delete choice')?>" class="form-group-control ui-icon ui-icon-circle-close"></span>');
		$group.before(deleteElt);
		deleteElt.css('position', 'relative');
		// deleteElt.css('left',0);
		
		var buttonElt = $('<span id="a_'+groupId+'" title="<?=__('Advanced options')?>" class="form-group-control ui-icon ui-icon-circle-plus"></span>');
		// var buttonElt = '<a id="a_'+groupId+'" href="#">+/- <?=__('Advanced options')?></a>';
		$group.before(buttonElt);
		buttonElt.css('position', 'relative');
		buttonElt.css('left','18px');
		buttonElt.css('top','-16px');
		
		$group.css('position', 'relative');
		$group.css('top','-19px');
		$group.css('left','20px');
		$group.width('90%');
		
		$group.hide();
		
		// $('#a_'+groupId).unbind('click');
		$('#a_'+groupId).toggle(function(){
			$(this).switchClass('ui-icon-circle-plus', 'ui-icon-circle-minus');
			$('#'+groupId).show().effect('slide');
		},function(){
			$(this).switchClass('ui-icon-circle-minus', 'ui-icon-circle-plus');
			$('#'+groupId).hide().effect('fold');
		});
		
		$('#delete_'+groupId).click(function(){
			if(confirm('Do you want to delete the choice?')){
				var choiceId = $(this).attr('id').replace('delete_choicePropOptions_', '');
				CL('deleting the choice '+choiceId);
			}
		});
	}
}

function initToggleChoiceOptions(){
	$('.form-group').each(function(){
		toggleChoiceOptions($(this));
	});
}

$(document).ready(function(){

	$('#add_choice_button').click(function(){
		//add a choice to the current interaction:
		var interactionId = '<?=get_data('interactionId')?>';
		qtiEdit.addChoice(interactionId, $('#formContainer_choices'), 'formContainer_choice');
		
	});

	initToggleChoiceOptions();

});
</script>

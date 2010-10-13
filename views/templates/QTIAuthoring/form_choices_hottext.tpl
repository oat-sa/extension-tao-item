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
		<!--<a href="#"><img src="<?=ROOT_URL?>/tao/views/img/save.png"> Add a choice</a>-->
	</div>
</div>


<script type="text/javascript">
$(document).ready(function(){
	
	//add adv. & delete button
	myInteraction.initToggleChoiceOptions();//{'delete':false}
	
	//add the listener to the form changing 
	myInteraction.setFormChangeListener();//all form
});
</script>

<?include('form_interaction_without_choiceform.tpl');?>

<div id="qtiAuthoring_interaction_right_container">
<?include('form_response_container.tpl');?>
</div>
<div style="clear:both"/>

<script type="text/javascript">
$(document).ready(function(){
	var backgroundImagePath = "<?=get_data('backgroundImagePath')?>";
	if(backgroundImagePath){
		var options = {};
		var width = "<?=get_data('width')?>";
		var height = "<?=get_data('height')?>";
		if(width) options.width = width;
		if(height) options.height = height;
		
		myInteraction.buildShapeEditor(backgroundImagePath, options);
	}
});
</script>
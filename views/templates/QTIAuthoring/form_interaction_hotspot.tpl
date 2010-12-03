<?include('form_interaction_choice.tpl');?>

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
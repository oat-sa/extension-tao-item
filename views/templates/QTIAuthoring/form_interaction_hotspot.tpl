<?include('form_interaction_choice.tpl');?>

<script type="text/javascript">
$(document).ready(function(){
	var backgroundImagePath = "<?=get_data('backgroundImagePath')?>";
	if(backgroundImagePath){
		myInteraction.buildShapeEditor(backgroundImagePath);
	}
});
</script>
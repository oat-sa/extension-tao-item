<?include('form_interaction_choice.tpl');?>

<script type="text/javascript">
var myShapeEditor = null;
$(document).ready(function(){
	var backgroundImagePath = "<?=get_data('backgroundImagePath')?>";
	if(backgroundImagePath){
		myShapeEditor = new qtiShapesEditClass('formInteraction_object_container', backgroundImagePath);
		if(myShapeEditor){
			//map choices to the shape editor:
			
		}
	}
	
});
</script>
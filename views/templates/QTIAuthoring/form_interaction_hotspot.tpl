<?include('form_interaction_choice.tpl');?>

<script type="text/javascript">
var myShapeEditor = null;
$(document).ready(function(){
	var backgroundImagePath = "<?=get_data('backgroundImagePath')?>";
	if(backgroundImagePath){
		myShapeEditor = new qtiShapesEditClass(
			'formInteraction_object_container', 
			backgroundImagePath,
			{
				onDrawn: function(choiceSerial, shapeObject, self){
					//export shapeObject to qti:
					if(choiceSerial && shapeObject){
						var qtiCoords = self.exportShapeToQti(choiceSerial);
						if(qtiCoords){
							$('#ChoiceForm_'+choiceSerial).find('input[name=coords]').val(qtiCoords);
							//indicate manually that the choice has been modified:
							myInteraction.modifiedChoices[choiceSerial] = 'modified';//it is a choice form:
						}
					}
				}
			});
			
		if(myShapeEditor){
			//map choices to the shape editor:
			myInteraction.shapeEditor = myShapeEditor;
		}
	}
	
});
</script>
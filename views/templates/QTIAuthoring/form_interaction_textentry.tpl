<div id="qtiAuthoring_interaction_left_container">
	<div id="qtiAuthoring_interactionEditor">

		<div id="formInteraction_title_<?=get_data('interactionSerial')?>" class="ui-widget-header ui-corner-top ui-state-default">
				<?=__('Interaction editor:')?>
		</div>
		<div id="formInteraction_content_<?=get_data('interactionSerial')?>" class="ui-widget-content ui-corner-bottom">
			<div class="ext-home-container ui-state-highlight">
				<?=get_data('formInteraction')?>
			</div>
		</div>

	</div>
</div>

<script type="text/javascript">
var myInteraction = null;
$(document).ready(function(){
	try{
		myInteraction = new interactionClass('<?=get_data('interactionSerial')?>', myItem.itemSerial);
	}catch(err){
		CL('error creating interaction', err);
	}
	$("input[name=baseType]:radio").die('change').live('change', function(){
		if($.inArray($(this).val(), ['integer', 'float']) > -1){
			$("input[name=base]:text").attr('disabled', false);
			$("input[name=stringIdentifier]:text").attr('disabled', false);
		}
		else{
			$("input[name=base]:text").attr('disabled', true);
			$("input[name=stringIdentifier]:text").attr('disabled', true);
		}
	});
});
</script>

<div id="qtiAuthoring_interaction_right_container">
<?include('form_response_container.tpl');?>
</div>
<div style="clear:both"/>

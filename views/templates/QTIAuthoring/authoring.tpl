<script language="Javascript" type="text/javascript">
	// $(document).ready(function(){
		// if($.browser == 'msie'){
			// alert('getting script');
			// $.getScript('/taoItems/views/js/qtiAuthoring/firebug-lite/build/firebug-lite.js', function() {
			  // console.log('Load was performed.');
			// });
		// }
	// });
</script>
<!--<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>firebug-lite/build/firebug-lite.js"></script>-->
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>util.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>responseClass.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>qtiEditClass.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>interactionClass.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>json2.js"></script>
<script type="text/javascript" src="<?=get_data('jwysiwyg_path')?>jquery.wysiwyg.js"></script>
<script type="text/javascript" src="<?=get_data('simplemodal_path')?>jquery.simplemodal.js"></script>

<link rel="stylesheet" href="<?=get_data('jwysiwyg_path')?>jquery.wysiwyg.css" type="text/css" />
<link rel="stylesheet" href="<?=get_data('jwysiwyg_path')?>jquery.wysiwyg.modal.css" type="text/css" />
<link rel="stylesheet" href="<?=get_data('simplemodal_path')?>jquery.simplemodal.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/qtiAuthoring.css" />

<div id="qtiAuthoring_main_container">
	<div id="qtiAuthoring_left_container">
		<div id="qtiAuthoring_itemEditor_title" class="ui-widget-header ui-corner-top ui-state-default">
				<?=__('Item Editor:')?>
		</div>
		<div id="qtiAuthoring_itemEditor" class="ui-widget-content ui-corner-bottom">
			<div class="ext-home-container ui-state-highlight">
				<textarea name="wysiwyg" id="itemEditor_wysiwyg"><?=get_data('itemData')?></textarea>
			</div>

		</div>

		<div id='qtiAuthoring_interactionEditor'/>    
	</div>

	<div id="qtiAuthoring_right_container">
		
		<div id="qtiAuthoring_processing_title" class="ui-widget-header ui-corner-top ui-state-default">
				<?=__('Response processing template editor:')?>
		</div>
		<div id="qtiAuthoring_processingEditor" class="ui-widget-content ui-corner-bottom">
			
		</div>
		
		<div id="qtiAuthoring_mapping_container">
			
		</div>
		
		<div id="qtiAuthoring_response_title" class="ui-widget-header ui-corner-top ui-state-default">
				<?=__('Response editor:')?>
		</div>
		<div id="qtiAuthoring_responseEditor" class="ui-widget-content ui-corner-bottom">
			<div class="ext-home-container ui-state-highlight_cancel">
				<table id="qtiAuthoring_response_grid"></table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
//customized unload function:
$.jgrid.GridUnload = function(){
	return this.each(function(){
		if ( !this.grid ) {return;}
		var defgrid = {id: $(this).attr('id'),cl: $(this).attr('class')};
		if (this.p.pager) {
			$(this.p.pager).empty().removeClass("ui-state-default ui-jqgrid-pager corner-bottom");
		}
		var newtable = document.createElement('table');
		$(newtable).attr({id:defgrid.id});
		newtable.className = defgrid.cl;
		var gid = this.id;
		$(newtable).removeClass("ui-jqgrid-btable");
		if( $(this.p.pager).parents("#gbox_"+gid).length === 1 ) {
			$(newtable).insertBefore("#gbox_"+gid).show();
			$(this.p.pager).insertBefore("#gbox_"+gid);
		} else {
			$(newtable).insertBefore("#gbox_"+gid).show();
		}
		$("#gbox_"+gid).remove();
	});
};
		
$(document).ready(function(){
	// console.log('ssds', $.browser);
	try{
		//global item object
		myItem = new qtiEdit('<?=get_data('itemSerial')?>');
		
	}catch(err){
		
		CL('error creating the item', err);
	}
	
});

</script>
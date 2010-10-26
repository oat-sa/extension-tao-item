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

<div id="qtiAuthoring_loading">
	<div id="qtiAuthoring_loading_message">
		<img src="<?=ROOT_URL?>/tao/views/img/ajax-loader.gif" alt="loading" />
	</div>
</div>

<div id="qtiAuthoring_main_container">
	
	<div id="qtiAuthoring_menu_container" class="ui-widget-header ui-corner-all">
		<div id="qtiAuthoring_menu_left_container">
			<div id="qtiAuthoring_save_button" class="qti-menu-item">
				<img title="Save" src="<?=get_data('qtiAuthoring_img_path')?>document-save.png"/>
				<br/>
				<a href="#">Save</a>
			</div>
			
			<div id="qtiAuthoring_preview_button" class="qti-menu-item">
				<img title="Preview" src="<?=get_data('qtiAuthoring_img_path')?>view-fullscreen.png"/>
				<br/>
				<a href="#">Preview</a>
			</div>
		</div>
		<div id="qtiAuthoring_menu_right_container">
			<div id="add_choice_interaction" class="qti-menu-item">
				<img title="Choice" src="<?=get_data('qtiAuthoring_img_path')?>window-new.png"/>
				<br/>
				<a href="#">Choice</a>
			</div>
			
		</div>
	</div>
	<div id="qtiAuthoring_item_container">
		<div id="qtiAuthoring_item_left_container">
			<div id="item_option_accordion">
				<h3><a href="#"><?=__('Item Properties:')?></a></h3>
				<div id="qtiAuthoring_itemProperties" class="ui-widget-content ui-corner-bottom">
					<?=get_data('itemForm')?>
				</div>
				<h3><a href="#"><?=__('Response processing template editor:')?></a></h3>
				<div id="qtiAuthoring_processingEditor" class="ui-widget-content ui-corner-bottom"/>
				<h3><a href="#"><?=__('Stylesheets manager:')?></a></h3>
				<div id="qtiAuthoring_cssManager" class="ui-widget-content ui-corner-bottom"/>
			</div>
		</div>
		
		<div id="qtiAuthoring_item_right_container">
			<div id="qtiAuthoring_itemEditor_title" class="ui-widget-header ui-corner-top ui-state-default"><?=__('Item Editor:')?></div>
			<div id="qtiAuthoring_itemEditor" class="ui-widget-content ui-corner-bottom">
				<textarea name="wysiwyg" id="itemEditor_wysiwyg"><?=get_data('itemData')?></textarea>
			</div>
		</div>
		
		<div style="clear:both"/>
	</div>
	
	<div id="qtiAuthoring_interaction_container">
	</div>
	
</div>

<script type="text/javascript">

		
$(document).ready(function(){
	// console.log('ssds', $.browser);
	try{
		//global item object
		myItem = new qtiEdit('<?=get_data('itemSerial')?>');
		
	}catch(err){
		
		CL('error creating the item', err);
	}
	
	//link the qti object to the item rdf resource
	myItem.itemUri = '<?=get_data('itemUri')?>';
	
	//set the save button:
	$('#qtiAuthoring_save_button').click(function(){
		myItem.save();
		return false;
	});
	
	//set the preview button:
	$('#qtiAuthoring_preview_button').click(function(){
		myItem.preview();
		return false;
	});
	
	$( "#item_option_accordion" ).accordion({
		fillSpace: true
	});
	
	myItem.loadStyleSheetForm();
	
	setTimeout(function(){$('#qtiAuthoring_loading').hide();}, 1000);
});

</script>
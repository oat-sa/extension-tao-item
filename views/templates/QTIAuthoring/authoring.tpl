<!--<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>firebug-lite/build/firebug-lite.js"></script>
<script type="text/javascript" src="https://getfirebug.com/firebug-lite.js"></script>-->
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>util.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>responseClass.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>qtiEditClass.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>interactionClass.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>lib/json2.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>lib/raphael.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>qtiShapeEditClass.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>lib/jwysiwyg/jquery.wysiwyg.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>lib/simplemodal/jquery.simplemodal.js"></script>

<link rel="stylesheet" href="<?=get_data('qtiAuthoring_path')?>lib/jwysiwyg/jquery.wysiwyg.css" type="text/css" />
<link rel="stylesheet" href="<?=get_data('qtiAuthoring_path')?>lib/jwysiwyg/jquery.wysiwyg.modal.css" type="text/css" />
<link rel="stylesheet" href="<?=get_data('qtiAuthoring_path')?>lib/simplemodal/jquery.simplemodal.css" type="text/css" />
<link rel="stylesheet" href="<?=BASE_WWW?>css/qtiAuthoring.css" type="text/css" />
<link rel="stylesheet" href="<?=BASE_WWW?>css/qtiShapeEditClass.css" type="text/css" />

<div id="qtiAuthoring_loading">
	<div id="qtiAuthoring_loading_message">
		<img src="<?=ROOT_URL?>/tao/views/img/ajax-loader.gif" alt="loading" />
	</div>
</div>

<div id="qtiAuthoring_main_container">
	
	<div id="qtiAuthoring_menu_container" class="ui-widget-content ui-corner-all">
		<div id="qtiAuthoring_menu_left_container" class="ui-widget-header ui-corner-all">
			<div id="qtiAuthoring_save_button" class="qti-menu-item">
				<img title="<?=__('Save')?>" src="<?=get_data('qtiAuthoring_img_path')?>document-save.png"/>
				<br/>
				<a href="#"><?=__('Save')?></a>
			</div>
			
			<div id="qtiAuthoring_preview_button" class="qti-menu-item">
				<img title="<?=__('Preview')?>" src="<?=get_data('qtiAuthoring_img_path')?>view-fullscreen.png"/>
				<br/>
				<a href="#"><?=__('Preview')?></a>
			</div>
			
			<div id="qtiAuthoring_item_editor_button" class="qti-menu-item">
				<img title="<?=__('Return to item editor')?>" src="<?=get_data('qtiAuthoring_img_path')?>view-fullscreen.png"/>
				<br/>
				<a href="#"><?=__('Item Editor')?></a>
			</div>
		</div>
		<div id="qtiAuthoring_menu_right_container" class="">
		</div>
	</div>
	
	<div id="tabs-qti">
	
		<ul id="tabs-qti-menu">
			<li><a href="#qtiAuthoring_item_container"></a></li>
			<li><a href="#qtiAuthoring_interaction_container"></a></li>
		</ul>
		
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
				<!--<div id="qtiAuthoring_itemEditor_title" class="ui-widget-header ui-corner-top ui-state-default"><?=__('Item Editor:')?></div>-->
				<div id="qtiAuthoring_itemEditor" class="ui-widget-content ui-corner-bottom">
					<textarea name="wysiwyg" id="itemEditor_wysiwyg"><?=get_data('itemData')?></textarea>
				</div>
			</div>
			
			<div style="clear:both"/>
		</div>
		
		<div id="qtiAuthoring_interaction_container">
		</div>
	
	</div>
	
</div>

<script type="text/javascript">
var authoring_img_url = root_url + "/taoItems/views/img/qtiAuthoring/";
		
$(document).ready(function(){

	//init interface:
	$myTab = $("#tabs-qti");
	$myTab.tabs({
	   select: function(event, ui) {
			if(ui.index == 0){
				//reload the item editor:
				// if(confirm('save?')){
					return true;
				// }
			}else if(ui.index == 1){
				return true;
			}
			
			return false;
	   }
	});
	
	$('#tabs-qti-menu').hide();
	$('#qtiAuthoring_item_editor_button').hide();
	
	//init item editor:
	try{
		//global item object
		myItem = new qtiEdit('<?=get_data('itemSerial')?>', null, {css:"<?=BASE_WWW?>css/qtiAuthoringFrame.css"});
	}catch(err){
		CL('error creating the item', err);
	}
	
	//link the qti object to the item rdf resource
	myItem.itemUri = '<?=get_data('itemUri')?>';
	myItem.itemClassUri = '<?=get_data('itemClassUri')?>';
	
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

	
	// available interactions:
	var interactionTypes = {
		choice: {
			label: 'choice', 
			icon:"<?=get_data('qtiAuthoring_img_path')?>QTI_choice.png"
		},
		inlinechoice: {
			label: 'inline choice', 
			icon:"<?=get_data('qtiAuthoring_img_path')?>QTI_inlineChoice.png"
		},
		associate: {
			label: 'associate', 
			icon:"<?=get_data('qtiAuthoring_img_path')?>QTI_associate.png"
		},
		order: {
			label: 'order', 
			icon:"<?=get_data('qtiAuthoring_img_path')?>QTI_order.png"
		},
		match: {
			label: 'match', 
			icon:"<?=get_data('qtiAuthoring_img_path')?>QTI_match.png"
		},
		gapmatch: {
			label: 'gap match', 
			icon:"<?=get_data('qtiAuthoring_img_path')?>QTI_gapMatch.png"
		},
		textentry: {
			label: 'text entry', 
			icon:"<?=get_data('qtiAuthoring_img_path')?>QTI_textentry.png"
		},
		extendedtext: {
			label: 'extended text', 
			icon:"<?=get_data('qtiAuthoring_img_path')?>QTI_extendedText.png"
		},
		hottext: {
			label: 'hottext',
			icon:"<?=get_data('qtiAuthoring_img_path')?>QTI_hottext.png"
		},
		hotspot: {
			label: 'hotspot',
			icon:"<?=get_data('qtiAuthoring_img_path')?>QTI_hotspot.png"
		},
		graphicorder: {
			label: 'graphic order',
			icon:"<?=get_data('qtiAuthoring_img_path')?>QTI_graphicOrder.png"
		},
		graphicassociate: {
			label: 'graphic associate',
			icon:"<?=get_data('qtiAuthoring_img_path')?>QTI_graphicAssociate.png"
		}
	}

	for(interactionType in interactionTypes){
		var id = 'add_'+interactionType+'_interaction';
		$menuItem = $('<div/>');
		$menuItem.attr('id', id);
		$menuItem.addClass('qti-menu-item');
		$menuItem.appendTo($('#qtiAuthoring_menu_right_container'));
		
		var label = interactionTypes[interactionType].label;
		$imgElt = $('<img/>');
		$imgElt.attr('title', label);
		$imgElt.attr('src', interactionTypes[interactionType].icon);
		$menuItem.append($imgElt);
		$menuItem.append('<br/>');
		$menuItem.append('<a href="#">'+label+'</a>');
		
		$('#qtiAuthoring_itemEditor').find('li.'+id).hide();
		$menuItem.bind('click', {id:id}, function(e){
			$('#qtiAuthoring_itemEditor').find('li.'+e.data.id).click();
		});
	}
	
	setTimeout(function(){
		$('#qtiAuthoring_loading').hide();
		$('#qtiAuthoring_main_container').show();
	}, 1000);

});

</script>
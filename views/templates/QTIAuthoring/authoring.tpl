<script language="Javascript" type="text/javascript">
</script>

<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>util.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>responseEdit.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>qtiEdit.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>interactionEdit.js"></script>
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
jQuery.fn.selectedText = function(win){
	win = win || window;
	
	var obj = null;
	var text = null;

	// Get parent element to determine the formatting applied to the selected text
	if(win.getSelection){
		var obj = win.getSelection().anchorNode;

		var text = win.getSelection().toString();
		// Mozilla seems to be selecting the wrong Node, the one that comes before the selected node.
		// I'm not sure if there's a configuration to solve this,
		var sel = win.getSelection();
		console.log(win.getSelection());
		if(!sel.isCollapsed&&$.browser.mozilla){
			// If we've selected an element, (note: only works on Anchors, only checked bold and spans)
			// we can use the anchorOffset to find the childNode that has been selected
			if(sel.focusNode.nodeName !== '#text'){
				// Is selection spanning more than one node, then select the parent
				if((sel.focusOffset - sel.anchorOffset)>1)
					console.log("Selected spanning more than one",obj = sel.anchorNode);
				else if ( sel.anchorNode.childNodes[sel.anchorOffset].nodeName !== '#text' )
					console.log("Selected non-text",obj = sel.anchorNode.childNodes[sel.anchorOffset]);
				else
					console.log("Selected whole element",obj = sel.anchorNode);
			}
			// if we have selected text which does not touch the boundaries of an element
			// the anchorNode and the anchorFocus will be identical
			else if( sel.anchorNode.data === sel.focusNode.data ){
				console.log("Selected non bounding text",obj = sel.anchorNode.parentNode);
			}
			// This is the first element, the element defined by anchorNode is non-text.
			// Therefore it is the anchorNode that we want
			else if( sel.anchorOffset === 0 && !sel.anchorNode.data ){
				console.log("Selected whole element at start of paragraph (whereby selected element has not text e.g. &lt;script&gt;",obj = sel.anchorNode);
			}
			// If the element is the first child of another (no text appears before it)
			else if( typeof sel.anchorNode.data !== 'undefined' 
						&& sel.anchorOffset === 0 
						&& sel.anchorOffset < sel.anchorNode.data.length ){
				console.log("Selected whole element at start of paragraph",obj = sel.anchorNode.parentNode);
			}
			// If we select text preceeding an element. Then the focusNode becomes that element
			// The difference between selecting the preceeding word is that the anchorOffset is less that the anchorNode.length
			// Thus
			else if( typeof sel.anchorNode.data !== 'undefined'
						&& sel.anchorOffset < sel.anchorNode.data.length ){
				console.log("Selected preceeding element text",obj = sel.anchorNode.parentNode);
			}
			// Selected text which fills an element, i.e. ,.. <b>some text</b> ...
			// The focusNode becomes the suceeding node
			// The previous element length and the anchorOffset will be identical
			// And the focus Offset is greater than zero
			// So basically we are at the end of the preceeding element and have selected 0 of the current.
			else if( typeof sel.anchorNode.data !== 'undefined' 
					&& sel.anchorOffset === sel.anchorNode.data.length 
					&& sel.focusOffset === 0 ){
				console.log("Selected whole element text", obj = (sel.anchorNode.nextSibling || sel.focusNode.previousSibling));
			}
			// if the suceeding text, i.e. it bounds an element on the left
			// the anchorNode will be the preceeding element
			// the focusNode will belong to the selected text
			else if( sel.focusOffset > 0 ){
				console.log("Selected suceeding element text", obj = sel.focusNode.parentNode);
			}
		}
		else if(sel.isCollapsed)
			obj = obj.parentNode;
		
	}
	else if(win.document.selection){
		var sel = win.document.selection.createRange();
		var obj = sel;

		if(sel.parentElement)
			obj = sel.parentElement();
		else 
			obj = sel.item(0);

		text = sel.text || sel;
	
		if(text.toString)
			text = text.toString();
	}
	else 
		throw 'Error';
		
	// webkit
	if(obj.nodeName==='#text')
		obj = obj.parentNode;

	// if the selected object has no tagName then return false.
	if(typeof obj.tagName === 'undefined')
		return false;

	return {'obj':obj,'text':text};
};


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

qtiEdit.itemSerial = '<?=get_data('itemSerial')?>';
qtiEdit.itemDataContainer = '#itemEditor_wysiwyg';
qtiEdit.interactionFormContent = '#qtiAuthoring_interactionEditor';
qtiEdit.responseProcessingFormContent = '#qtiAuthoring_processingEditor';
qtiEdit.responseMappingOptionsFormContainer = '#qtiAuthoring_mapping_container';

//init the item's jwysiwyg editor here:
var addChoiceInteraction = {
	visible : true,
	className: 'addInteraction',
	exec: function(){
		CL('inserting interaction...');
		//display modal window with the list of available type of interactions
		var interactionType = 'choice';
		
		//insert location of the current interaction in the item:
		this.insertHtml('{qti_interaction_new}');
		
		//send to request to the server
		qtiEdit.addInteraction(interactionType, this.getContent(), qtiEdit.itemSerial);
	},
	tooltip: 'add choice interaction'
};

var addAssociateInteraction = {
	visible : true,
	className: 'addInteraction',
	exec: function(){
		var interactionType = 'associate';
		this.insertHtml('{qti_interaction_new}');
		qtiEdit.addInteraction(interactionType, this.getContent(), qtiEdit.itemSerial);
	},
	tooltip: 'add associate interaction'
};

var addOrderInteraction = {
	visible : true,
	className: 'addInteraction',
	exec: function(){
		this.insertHtml('{qti_interaction_new}');
		qtiEdit.addInteraction('order', this.getContent(), qtiEdit.itemSerial);
	},
	tooltip: 'add order interaction'
};

var addMatchInteraction = {
	visible : true,
	className: 'addInteraction',
	exec: function(){
		this.insertHtml('{qti_interaction_new}');
		qtiEdit.addInteraction('match', this.getContent(), qtiEdit.itemSerial);
	},
	tooltip: 'add match interaction'
};

var addInlineChoiceInteraction = {
	visible : true,
	className: 'addInteraction',
	exec: function(){
		this.insertHtml('{qti_interaction_new}');
		qtiEdit.addInteraction('inlineChoice', this.getContent(), qtiEdit.itemSerial);
	},
	tooltip: 'add inline choice interaction'
};

var addTextEntryInteraction = {
	visible : true,
	className: 'addInteraction',
	exec: function(){
		this.insertHtml('{qti_interaction_new}');
		qtiEdit.addInteraction('textEntry', this.getContent(), qtiEdit.itemSerial);
	},
	tooltip: 'add text entry interaction'
};

var addExtendedTextInteraction = {
	visible : true,
	className: 'addInteraction',
	exec: function(){
		this.insertHtml('{qti_interaction_new}');
		qtiEdit.addInteraction('extendedText', this.getContent(), qtiEdit.itemSerial);
	},
	tooltip: 'add extended text interaction'
};

var addHotTextInteraction = {
	visible : true,
	className: 'addInteraction',
	exec: function(){
		this.insertHtml('{qti_interaction_new}');
		qtiEdit.addInteraction('hotText', this.getContent(), qtiEdit.itemSerial);
	},
	tooltip: 'add hot text interaction'
};

var addGapMatchInteraction = {
	visible : true,
	className: 'addInteraction',
	exec: function(){
		this.insertHtml('{qti_interaction_new}');
		qtiEdit.addInteraction('gapMatch', this.getContent(), qtiEdit.itemSerial);
	},
	tooltip: 'add gap match interaction'
};

var saveItemData = {
	visible : true,
	className: 'addInteraction',
	exec: function(){
		qtiEdit.saveItemData();
	},
	tooltip: 'save'
};

var loadXmlQti = null;
var exportXmlQti = null;
		
$(document).ready(function(){

  qtiEdit.itemEditor = $(qtiEdit.itemDataContainer).wysiwyg({
    controls: {
      strikeThrough : { visible : true },
      underline     : { visible : true },
      
      justifyLeft   : { visible : true },
      justifyCenter : { visible : true },
      justifyRight  : { visible : true },
      justifyFull   : { visible : true },
      
      indent  : { visible : true },
      outdent : { visible : true },
      
      subscript   : { visible : true },
      superscript : { visible : true },
      
      undo : { visible : true },
      redo : { visible : true },
      
      insertOrderedList    : { visible : true },
      insertUnorderedList  : { visible : true },
      insertHorizontalRule : { visible : true },

      h4: {
              visible: true,
              className: 'h4',
              command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
              arguments: ($.browser.msie || $.browser.safari) ? '<h4>' : 'h4',
              tags: ['h4'],
              tooltip: 'Header 4'
      },
      h5: {
              visible: true,
              className: 'h5',
              command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
              arguments: ($.browser.msie || $.browser.safari) ? '<h5>' : 'h5',
              tags: ['h5'],
              tooltip: 'Header 5'
      },
      h6: {
              visible: true,
              className: 'h6',
              command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
              arguments: ($.browser.msie || $.browser.safari) ? '<h6>' : 'h6',
              tags: ['h6'],
              tooltip: 'Header 6'
      },
      cut   : { visible : true },
      copy  : { visible : true },
      paste : { visible : true },
      html  : { visible: true },
	  addChoiceInteraction: addChoiceInteraction,
	  addAssociateInteraction: addAssociateInteraction,
	  addOrderInteraction: addOrderInteraction,
	  addMatchInteraction: addMatchInteraction,
	  addInlineChoiceInteraction: addInlineChoiceInteraction,
	  addTextEntryInteraction: addTextEntryInteraction,
	  addExtendedTextInteraction: addExtendedTextInteraction,
	  addHotTextInteraction: addHotTextInteraction,
	  addGapMatchInteraction: addGapMatchInteraction,
	  saveItemData: saveItemData
    },
    events: {
	  keyup : function(e){
		if(qtiEdit.getDeletedInteractions(true).length > 0){
			if(!confirm('please confirm deletion of the interaction')){
				// undo:
				qtiEdit.itemEditor.wysiwyg('undo');
			}else{
				var deletedInteractions = qtiEdit.getDeletedInteractions();
				qtiEdit.deleteInteractions(deletedInteractions);
				
			}
		}
		// if ($('#click-inform:checked').length > 0){
		  // e.preventDefault();
		  // alert('You have clicked jWysiwyg content!');
		// }
	  }
    }
  });
  
	//the binding require the modified html data to be ready
	setTimeout(qtiEdit.bindInteractionLinkListener,250);
	
	qtiEdit.loadResponseProcessingForm();
});

</script>

<script type="text/javascript">
	
	
</script>
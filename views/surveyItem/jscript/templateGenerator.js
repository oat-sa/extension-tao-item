var MAX_LENGTH = 35;
var LAYOUT = false;
var TOP_DOC = false;
// as frame is loaded before filled, we do this trick to have a document.ready behaviour
function docReady(layout) {
	TOP_DOC = top.document;
	// get the current layour
	LAYOUT = layout;
	// create the button to generate the template selector
	$('<div  />')
	.html("Generate<br />template<br />selector")
	.attr('type', 'button')
	.addClass('toolbarbutton-1')
	.css({
		'position': 'absolute'
		,	top: '10px'
		,	left: '10px'
		,	'z-index': '1000'
	})
	.click(getTemplateType)
	.appendTo($('body'));
}

// function to get information about template and generate his preview tile
function generateSelector(templateType) {
	switch(templateType) {
		case 'info': informational_item = true;break;
		case 'item': informational_item = false;break;
		case 'rules': return closePopup('Rules templates can\'t be generated !');
		default: return closePopup('Unknown template type : ' . templateType);
	}
	// get content
	var content = $('#container').clone();
	// remove unused
	content.find('#info_dialog').remove();
	content.find('#menuDialog').remove();
	// transformHtml
	content = transformHtml(content);
	if(!informational_item) {
		// create structure item
		var div = $('<div />').addClass('item');
		$('<div />')
		.addClass('item_header')
		.appendTo(div)
		$('<div />')
		.addClass('item_content')
		.appendTo(div)
		// truncate question, description and instruction if needed
		$.each(content.find('p.question, p.question_description, p.instruction'), function () {
			if($(this).html().length > MAX_LENGTH) {
				$(this).html($(this).html().substr(0, MAX_LENGTH) + '...');
			}
			$(this).appendTo(div.find('.item_header'));
		});
		div.find('.item_content').append(content.html());
	} else {
		// create structure informational
		var div = $('<div />').addClass('informational');
		div.append(content.html());
	}
	// empty content
	content.html('');
	// put all text nodes in cdata
	div = cdataise(div);
	// reput in content
	div.appendTo(content);

	// get the name of the choosen template if already exist
	var old = '';
	$.ajax({
		url: '../templateGenerator.php'
		,	type: 'post'
		,	dataType: 'json'
		,	async: false
		, 	data: {
			getTitle: LAYOUT
		}
		, 	success: function (retour) {
			old = retour;
		}
	});
	// set the nex title of the template
	var title = '';
	while(title == '') {
		title = prompt("Be carefull, by doing this, you will override the template '" + LAYOUT + "' (if exists).\nClick 'Cancel' to avoid this action.\nWhich title want you to give to the template ?", old);
		// avoid if cancel is pressed
		if(title == null) {
			return abortGeneration();
		}
	}
	// launch generation
	$.ajax({
		url: '../templateGenerator.php'
		,	type: 'post'
		, 	data: {
			name: LAYOUT
			,	title: title
			,	content: content.html()
		}
		, 	success: function (msg) {
			$.ajax({
				url: '../templatesList.php',
		  dataType: 'html',
		  async: false,
		  success: function(data) {
			  // remove the overlay and its content
			  closePopup();
			  // update the tpl lis in case of succes
			  $(top('#templatesList')).html(data);
			  // and alert the return of ajax
			  alert(msg);
		  }
			});
		}
	});
}

// some modification for template generation in original html
function transformHtml(content) {
	//remove var name
	content.find('td.variable, th.variable').html('');
	$.each(content.find('input.variableText, .variableTextField'), function () {
		$(this).val('');
		$(this).html('');
	});
	return content;
}

// node text in CDATA
function cdataise(node) {
	$(node).contents().each(function() {
		// if ELEMENT node
		if (this.nodeType == 1) {
			cdataise(this);
		}
		// if TEXT node
		if (this.nodeType == 3) {
			var cdata = parent.document.createCDATASection($.trim(this.textContent));
			$(this).replaceWith(cdata);
		}
	});
	return node;
}

// open a kind of modal popup to ask type of template
function getTemplateType () {
	//close if not
	closePopup();
	// create an overlay
	createOverlay();
	// create content out of overlay to do not have transparence
	$('<div />')
	.css({
		'top': '0'
		,	'left': '0'
		,	'width': '100%'
		,	'height': '100%'
		,	'position': 'fixed'
		, 	'background-color': 'transparent'
		,	'z-index': 1002
	})
	.click(closePopup) // click on the 'overlay' abort generation
	.attr('id', 'overlayedContent')
	.appendTo(top('body')) // append it on the parent to cover all the window
	;
	// add close button
	$('<div />')
	.addClass('toolbarbutton-1')
	.html('Close')
	.css({
			position: 'fixed'
		,	top: '20px'
		,	right: '20px'
	})
	.click(closePopup)
	.appendTo(top('#overlayedContent'))
	;
	// append a div  in the overlay
	$('<div />')
	.attr('id', 'contentBox')
	.css({
		'position': 'absolute'
		,	'width': '400px'
		, 	'left': '50%'
		, 	'margin-left': '-200px'
		,	'height': '100px'
		, 	'top': '50%'
		, 	'margin-top': '-50px'
		, 	'background-color': 'silver'
		,	'border': 'solid 1px black'
		,	'border-radius': '25px'
		,	'text-align': 'center'
		,	'z-index': 1003
	})
	.click(function (){return false;}) //avoid overlay click
	.appendTo(top('#overlayedContent'));
	// append span with instruction
	$('<p />')
		.css({
			'font-size': '16px'
		,	'padding': '15px'
		})
		.html('Please choose the type of template :' + "<br />")
		.appendTo(top('#contentBox'))
	;
	// append a select to this div
	$('<select />')
		.attr('id', 'templateType')
		.change(selectTemplateType)
		.appendTo (top('#contentBox'))
	;
	// append option to select
	var opts = {
		'none': 'Choose'
		,	'item': 'Classical'
		,	'info': 'Informational'
		,	'rules': 'Rules'
	};
	$.each(opts, function (i, v) {
		$('<option />')
		.attr('label', v)
		.html(v)
		.val(i)
		.appendTo(top('#templateType'))
	});
}

function selectTemplateType () {
	if($(this).val() == 'none') {
		return abortGeneration();
	}
	generateSelector($(this).val());
}

function abortGeneration () {
	return closePopup('Generation aborted');
}

function closePopup(msg) {
	if(typeof(msg) != 'undefined' && typeof(msg) != 'object') { // because on click(fct), the object is automatiquely passed to the function and we only want string
		alert(msg);
	}
	top('#overlayedContent').remove();
	top('#overlay').remove();
	return false;
}

function top(selector) {
	return $(selector, parent.document);
}

function createOverlay() {
	//create an overlay
	$('<div />')
		.css({
				'top': '0'
			,	'left': '0'
			,	'width': '100%'
			,	'height': '100%'
			,	'position': 'fixed'
			, 	'background-color': 'white'
			,	'opacity': 0.8
			,	'filter': 'alpha(opacity=80)' /*for IE*/
			,	'z-index': 1001
		})
		.attr('id', 'overlay')
		.appendTo(top('body')) // append it on the parent to cover all the window
	;
}
// alert('qtiEdit loaded');

qtiEdit = new Object();

qtiEdit.itemEditor = null;
qtiEdit.itemId = '';

qtiEdit.getEltInFrame = function(selector){
	var foundElts = [];
	//for each iframe:
	$('iframe').each(function(){
	
		//get its document
		$(this).each( function(){
			var selectedDocument = this.contentWindow.document;
			$(selector, selectedDocument).each(function(){
				foundElts.push($(this));  
			});
		});
	});
	return foundElts;
}

qtiEdit.getUniqueEltInFrame = function(selector){
	var foundElts = qtiEdit.getEltInFrame(selector);
	if(foundElts.length != 1){
		throw 'incorrect number of found with the selector '+selector+' ('+foundElts.length+')';
	}
	return foundElts[0];
}

qtiEdit.bindInteractionLinkListener = function(){
	//destroy all listeners:
	// qtiEdit.getEltInFrame('.qti_interaction_link').unbind('click');
	
	var links = qtiEdit.getEltInFrame('.qti_interaction_link');
	for(var i in links){
		links[i].unbind('click').click(function(){
			CL("go to editing "+$(this).attr('id'));
		});
	}
}

qtiEdit.addInteraction = function(interactionType, itemData, itemId){
	
	if(!itemId){
		itemId = qtiEdit.itemId
	}

	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/addInteraction",
	   data: {
			'interactionType': interactionType,
			'itemData': itemData,
			'itemId': itemId
	   },
	   dataType: 'json',
	   success: function(r){
			// CD(r, 'res');
			
			//set the content:
			qtiEdit.itemEditor.wysiwyg('setContent', $("<div/>").html(r.itemData).html());
			
			//then add listener
			qtiEdit.bindInteractionLinkListener();
	   }
	});
}

qtiEdit.saveItemData = function(itemId){
	
	if(!itemId){
		var itemId = qtiEdit.itemId;
	}
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveItemData",
	   data: {
			'itemData': qtiEdit.itemEditor.wysiwyg('getContent'),
			'itemId': itemId
	   },
	   dataType: 'json',
	   success: function(r){
			CL('saved');
	   }
	});
}
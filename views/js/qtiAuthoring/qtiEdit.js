// alert('qtiEdit loaded');

qtiEdit = new Object();

qtiEdit.itemEditor = null;
qtiEdit.itemId = '';
qtiEdit.interactions = [];
qtiEdit.itemDataContainer = '';
qtiEdit.interactionFormContent = '';

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

//bind the interaction listener and refresh the table of interactions at the same time
qtiEdit.bindInteractionLinkListener = function(){
	//destroy all listeners:
	
	//reset the interaction array:
	qtiEdit.interactionIds = [];
	
	var links = qtiEdit.getEltInFrame('.qti_interaction_link');
	for(var i in links){
		
		var interactionId = links[i].attr('id');
		
		qtiEdit.interactions[interactionId] = interactionId;
		
		links[i].unbind('click').click(function(){
			CL("go to editing "+$(this).attr('id'));
			qtiEdit.loadInteractionForm($(this).attr('id'));
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

qtiEdit.deleteInteraction = function(interactionId){
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/deleteInteraction",
	   data: {
			'interactionId': interactionId,
			'itemId': qtiEdit.itemId
	   },
	   dataType: 'json',
	   success: function(r){
			
			delete qtiEdit.interactions[interactionId];
	   }
	});
}

qtiEdit.checkInteractionDeletion = function(all){

	if(typeof(all) == 'undefined'){
		var all = false; 
	}
	
	//TODO: improve with the use of regular expressions: 
	var itemData = $(qtiEdit.itemDataContainer).val();
	for(var interactionId in qtiEdit.interactions){
		if(itemData.indexOf(interactionId)<0){
			//not found:
			return false;
		}
	}
	
	return true;
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

qtiEdit.loadInteractionForm = function(interactionId){
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/editInteraction",
	   data: {
			'interactionId': interactionId,
			'itemId': qtiEdit.itemId
	   },
	   dataType: 'html',
	   success: function(form){
			$(qtiEdit.interactionFormContent).html(form);
	   }
	});
}
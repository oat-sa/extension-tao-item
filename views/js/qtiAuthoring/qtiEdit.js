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

qtiEdit.deleteInteractions = function(interactionIds){

	var data = '';
	//prepare the data to be sent:
	for(var i in interactionIds){
		data += 'interactionIds['+ i +']=' + interactionIds[i] + '&';
	}
	data += 'itemId=' + qtiEdit.itemId;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/deleteInteractions",
	   data: data,
	   dataType: 'json',
	   success: function(r){
			
			if(r.deleted){
				for(var i in interactionIds){
					delete qtiEdit.interactions[interactionIds[i]];
				}
				
				//save item data, i.e. validate the changes operated on the item data:
				qtiEdit.saveItemData();
			}
			
	   }
	});
	
}

/*
qtiEdit.checkInteractionDeletion = function(){
	
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
*/

qtiEdit.getDeletedInteractions = function(one){
	var deletedInteractions = [];
	var itemData = $(qtiEdit.itemDataContainer).val();//TODO: improve with the use of regular expressions:
	for(var interactionId in qtiEdit.interactions){
		if(itemData.indexOf(interactionId)<0){
			//not found so considered as deleted:
			deletedInteractions.push(interactionId);
			if(one){
				return deletedInteractions;
			}
		}
	}
	
	return deletedInteractions;
}

qtiEdit.addChoice = function(interactionId, $appendTo, containerClass){
	
	if(!$appendTo || !$appendTo.length){
		throw 'the append target element do not exists';
	}
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/addChoice",
	   data: {
			'interactionId': interactionId
	   },
	   dataType: 'json',
	   success: function(r){
			CL('choice added');
			if(r.added){
				var newFormElt = $('<div/>');
				newFormElt.attr('id', r.choiceId);
				newFormElt.attr('class', containerClass);
				newFormElt.append(r.choiceForm);
				$appendTo.append(newFormElt);
				
				newFormElt.hide();
				initToggleChoiceOptions();
				newFormElt.show();
			}
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
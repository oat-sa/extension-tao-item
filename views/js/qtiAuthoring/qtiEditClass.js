alert('qtiEdit loaded');

qtiEdit.instances = [];

function qtiEdit(itemSerial, formContainers){
	
	var defaultFormContainers = {
		itemDataContainer : '#itemEditor_wysiwyg',
		interactionFormContent : '#qtiAuthoring_interactionEditor',
		responseProcessingFormContent : '#qtiAuthoring_processingEditor',
		responseMappingOptionsFormContainer : '#qtiAuthoring_mapping_container',
		responseGrid: 'qtiAuthoring_response_grid'
	}
	
	if(!formContainers){
		var formContainers = defaultFormContainers;
	}else{
		$.extend(formContainers, defaultFormContainers);
	}
	
	this.interactions = [];
	this.itemSerial = itemSerial;
	this.itemDataContainer = formContainers.itemDataContainer;
	this.interactionFormContent = formContainers.interactionFormContent;
	this.responseProcessingFormContent = formContainers.responseProcessingFormContent;
	this.responseMappingOptionsFormContainer = formContainers.responseMappingOptionsFormContainer;
	this.responseGrid = formContainers.responseGrid;
	// this.responseMappingMode = false;
	
	var instance = this;
		
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
			instance.addInteraction(interactionType, this.getContent(), instance.itemSerial);
		},
		tooltip: 'add choice interaction'
	};

	var addAssociateInteraction = {
		visible : true,
		className: 'addInteraction',
		exec: function(){
			var interactionType = 'associate';
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction(interactionType, this.getContent(), instance.itemSerial);
		},
		tooltip: 'add associate interaction'
	};

	var addOrderInteraction = {
		visible : true,
		className: 'addInteraction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('order', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add order interaction'
	};

	var addMatchInteraction = {
		visible : true,
		className: 'addInteraction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('match', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add match interaction'
	};

	var addInlineChoiceInteraction = {
		visible : true,
		className: 'addInteraction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('inlineChoice', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add inline choice interaction'
	};

	var addTextEntryInteraction = {
		visible : true,
		className: 'addInteraction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('textEntry', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add text entry interaction'
	};

	var addExtendedTextInteraction = {
		visible : true,
		className: 'addInteraction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('extendedText', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add extended text interaction'
	};

	var addHotTextInteraction = {
		visible : true,
		className: 'addInteraction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('hotText', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add hot text interaction'
	};

	var addGapMatchInteraction = {
		visible : true,
		className: 'addInteraction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('gapMatch', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add gap match interaction'
	};

	var saveItemData = {
		visible : true,
		className: 'addInteraction',
		exec: function(){
			instance.saveItemData();
		},
		tooltip: 'save'
	};

	var loadXmlQti = null;
	var exportXmlQti = null;
	var instance = this;
	
	this.itemEditor = $(this.itemDataContainer).wysiwyg({
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
			if(instance.getDeletedInteractions(true).length > 0){
				if(!confirm('please confirm deletion of the interaction')){
					instance.itemEditor.wysiwyg('undo');
				}else{
					var deletedInteractions = instance.getDeletedInteractions();
					instance.deleteInteractions(deletedInteractions);
					
				}
				return false;
			}
		  }
		}
	});
	
	//the binding require the modified html data to be ready
	setTimeout(function(){instance.bindInteractionLinkListener();},250);
	
	this.loadResponseProcessingForm();
	
	qtiEdit.instances[this.itemSerial] = this;
}

qtiEdit.prototype.addInteraction = function(interactionType, itemData, itemSerial){
		
	if(!itemSerial){
		itemSerial = this.itemSerial;
	}
	
	var instance = this;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/addInteraction",
	   data: {
			'interactionType': interactionType,
			'itemData': itemData,
			'itemSerial': itemSerial
	   },
	   dataType: 'json',
	   success: function(r){
			//set the content:
			instance.itemEditor.wysiwyg('setContent', $("<div/>").html(r.itemData).html());
			
			//then add listener
			instance.bindInteractionLinkListener();
			
			//auto load the interaction form?
	   }
	});
}

qtiEdit.prototype.bindInteractionLinkListener = function(){
	
	//destroy all listeners:
	
	//reset the interaction array:
	var instance = this;
	instance.interactionSerials = [];
	
	var links = qtiEdit.getEltInFrame('.qti_interaction_link');
	
	for(var i in links){
		
		var interactionSerial = links[i].attr('id');
		
		instance.interactions[interactionSerial] = interactionSerial;
		
		links[i].unbind('click').click(function(){
			instance.currentInteractionSerial = $(this).attr('id');
			
			instance.loadInteractionForm(instance.currentInteractionSerial);
			try{
				// responseEdit.buildGrid(instance.responseGrid, instance.currentInteractionSerial);
			}catch(err){
				CL('building grid error:', err);
			}
		});
		
	}
}

qtiEdit.prototype.loadInteractionForm = function(interactionSerial){
	var instance = this;
	
	if(instance.itemSerial){
		$.ajax({
		   type: "POST",
		   url: "/taoItems/QtiAuthoring/editInteraction",
		   data: {
				'interactionSerial': interactionSerial,
				'itemSerial': instance.itemSerial
		   },
		   dataType: 'html',
		   success: function(form){
				$(instance.interactionFormContent).html(form);
		   }
		});
	}
	
}

qtiEdit.getEltInFrame = function(selector){
	var foundElts = [];
	// for each iframe:
	$('iframe').each(function(){
	
		// get its document
		$(this).each( function(){
			var selectedDocument = this.contentWindow.document;
			$(selector, selectedDocument).each(function(){
				foundElts.push($(this));  
			});
		});
		
	});
	return foundElts;
}

qtiEdit.prototype.saveItemData = function(itemSerial){
	
	var instance = this;
	
	if(!itemSerial){
		var itemSerial = instance.itemSerial;
	}
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveItemData",
	   data: {
			'itemData': instance.itemEditor.wysiwyg('getContent'),
			'itemSerial': itemSerial
	   },
	   dataType: 'json',
	   success: function(r){
			// CL('item saved');
	   }
	});
}

qtiEdit.prototype.getDeletedInteractions = function(one){
	var deletedInteractions = [];
	var itemData = $(this.itemDataContainer).val();//TODO: improve with the use of regular expressions:
	for(var interactionSerial in this.interactions){
		if(itemData.indexOf(interactionSerial)<0){
			//not found so considered as deleted:
			deletedInteractions.push(interactionSerial);
			if(one){
				return deletedInteractions;
			}
		}
	}
	
	return deletedInteractions;
}

qtiEdit.prototype.deleteInteractions = function(interactionSerials){
	
	if(!interactionSerials || interactionSerials.length <=0){
		return false;
	}
	
	var data = '';
	//prepare the data to be sent:
	for(var i in interactionSerials){
		data += 'interactionSerials['+ i +']=' + interactionSerials[i] + '&';
		delete this.interactions[interactionSerials[i]];
	}
	data += 'itemSerial=' + this.itemSerial;
	
	var instance = this;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/deleteInteractions",
	   data: data,
	   dataType: 'json',
	   success: function(r){
			
			if(r.deleted){
				for(var i in interactionSerials){
					if(interactionEdit.interactionSerial == interactionSerials[i]){
						// destroy the interaction form:
						$(instance.interactionFormContent).empty();
					}
					delete instance.interactions[interactionSerials[i]];
				}
				
				//destroy the response form:
				if(responseClass.grid) responseClass.grid.destroyGrid(instance.responseGrid);
				
				//save item data, i.e. validate the changes operated on the item data:
				instance.saveItemData();
				
			}else{
			
				for(var i in interactionSerials){
					instance.interactions[interactionSerials[i]] = interactionSerials[i];
				}
				
			}
			
	   }
	});
	
}

qtiEdit.prototype.loadResponseProcessingForm = function(){
	
	// if(!itemSerial){
		// var itemSerial = this.itemSerial;
	// }
	
	var instance = this;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/editResponseProcessing",
	   data: {
			'itemSerial': instance.itemSerial
	   },
	   dataType: 'html',
	   success: function(form){
			$(instance.responseProcessingFormContent).html(form);
	   }
	});
}
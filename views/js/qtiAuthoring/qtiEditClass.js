// alert('qtiEdit loaded');
alert('qtiEdit loaded');

qtiEdit.instances = [];

function qtiEdit(itemSerial, formContainers, options){
	
	var defaultFormContainers = {
		itemDataContainer : '#itemEditor_wysiwyg',
		interactionFormContent : '#qtiAuthoring_interaction_container',
		responseProcessingFormContent : '#qtiAuthoring_processingEditor',
		cssFormContent: '#qtiAuthoring_cssManager',
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
	this.cssFormContent = formContainers.cssFormContent;
	// this.responseMappingMode = false;
	
	var instance = this;
	
	//init the item's jwysiwyg editor here:
	var addChoiceInteraction = {
		visible : true,
		className: 'add_choice_interaction',
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
		className: 'add_associate_interaction',
		exec: function(){
			var interactionType = 'associate';
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction(interactionType, this.getContent(), instance.itemSerial);
		},
		tooltip: 'add associate interaction'
	};

	var addOrderInteraction = {
		visible : true,
		className: 'add_order_interaction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('order', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add order interaction'
	};

	var addMatchInteraction = {
		visible : true,
		className: 'add_match_interaction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('match', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add match interaction'
	};

	var addInlineChoiceInteraction = {
		visible : true,
		className: 'add_inlinechoice_interaction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('inlineChoice', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add inline choice interaction'
	};

	var addTextEntryInteraction = {
		visible : true,
		className: 'add_textentry_interaction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('textEntry', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add text entry interaction'
	};

	var addExtendedTextInteraction = {
		visible : true,
		className: 'add_extendedtext_interaction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('extendedText', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add extended text interaction'
	};

	var addHotTextInteraction = {
		visible : true,
		className: 'add_hottext_interaction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('hotText', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add hot text interaction'
	};

	var addGapMatchInteraction = {
		visible : true,
		className: 'add_gapmatch_interaction',
		exec: function(){
			this.insertHtml('{qti_interaction_new}');
			instance.addInteraction('gapMatch', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add gap match interaction'
	};

	var saveItemData = {
		visible : false,
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
		css: options.css,
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
		  },
		  frameReady: function(editorDoc){
			//the binding require the modified html data to be ready
			instance.bindInteractionLinkListener();
		  }
		}
	});
	
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

qtiEdit.prototype.bindInteractionLinkListener = function(editorDoc){
	
	//destroy all listeners:
	
	
	//reset the interaction array:
	var instance = this;
	instance.interactionSerials = [];
	
	
	var links = qtiEdit.getEltInFrame('.qti_interaction_link', editorDoc);
	
	for(var i in links){
		
		var interactionSerial = links[i].attr('id');
		
		instance.interactions[interactionSerial] = interactionSerial;
		
		links[i].click(function(e){
			e.preventDefault();
			instance.currentInteractionSerial = $(this).attr('id');
			instance.loadInteractionForm(instance.currentInteractionSerial);
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
				qtiEdit.mapHtmlEditor($(instance.interactionFormContent));
				
		   }
		});
	}
	
}

qtiEdit.mapHtmlEditor = function($container){
	//map the wysiwyg editor to the html-area fields
	$container.find('.qti-html-area').each(function(){
		if ($(this).css('display') != 'none' && !$(this).siblings('.wysiwyg').length){
		
			var controls = {
			
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
			  cut   : { visible : true },
			  copy  : { visible : true },
			  paste : { visible : true },
			  html  : { visible: false },
			  h4: { visible: false },
			  h5: { visible: false },
			  h6: { visible: false },
			  
			  insertTable: { visible: false },
			  addChoiceInteraction: {visible:false},
			  addAssociateInteraction: {visible:false},
			  addOrderInteraction: {visible:false},
			  addMatchInteraction: {visible:false},
			  addInlineChoiceInteraction: {visible:false},
			  addTextEntryInteraction: {visible:false},
			  addExtendedTextInteraction: {visible:false},
			  addHotTextInteraction: {visible:false},
			  addGapMatchInteraction: {visible:false},
			  createHotText: {visible:false},
			  createGap: {visible:false},
			  saveItemData: {visible:false},
			  saveInteractionData: {visible:false}
			  
			};
		
			$(this).wysiwyg({controls: controls});
		}
	});
}

qtiEdit.destroyHtmlEditor = function($container){
	// CL('$container', $container);
	$container.find('.qti-html-area').each(function(){
		// CL('$(this).siblings(".wysiwyg")', $(this).siblings('.wysiwyg'));
		// if ($(this).css('display') != 'none' && $(this).siblings('.wysiwyg').length){
			try{
				$(this).wysiwyg('destroy');
			}catch(err){
				
			}
			CL('destroyed!!');
		// }
	});
}

qtiEdit.getEltInFrame = function(selector, selectedDocument){
	var foundElts = [];
	
	if(selectedDocument){
		$(selector, selectedDocument).each(function(){
			foundElts.push($(this));  
		});
	}else{
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
	}
	
	return foundElts;
}

//the global save function
qtiEdit.prototype.save = function(itemUri){
	
	if(!this.itemUri) throw 'item uri cannot be empty';
	
	//save item data then export to rdf item:
	var instance = this;
	
	//get the item form values:
	var itemProperties = $('#AssessmentItem_Form').serialize();
	//could check if an interaction is being edited, so suggest to save it too:
	if(itemProperties){
		itemProperties += '&itemData=' + instance.itemEditor.wysiwyg('getContent');
		itemProperties += '&itemSerial=' + instance.itemSerial;
		itemProperties += '&itemUri=' + this.itemUri;
	}
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveItem",
	   data: itemProperties,
	   dataType: 'json',
	   success: function(r){
			if(r.saved){
				createInfoMessage(__('The item has been successfully saved'));
			}
	   }
	});
}

qtiEdit.prototype.preview = function(){
	// GenerisAction.fullScreen(this.itemSerial, '', '/taoItems/QtiAuthoring/preview');
	var url = '/taoItems/QtiAuthoring/preview';
	url += '?itemSerial='+this.itemSerial;
	window.open(url, 'tao', 'width=800,height=600,menubar=no,toolbar=no');
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
					if(instance.interactionSerial == interactionSerials[i]){
						// destroy the interaction form:
						$(instance.interactionFormContent).empty();
					}
					delete instance.interactions[interactionSerials[i]];
				}
				
				//destroy the response form:
				// if(responseClass.grid) responseClass.grid.destroyGrid(instance.responseGrid);
				
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

qtiEdit.prototype.loadStyleSheetForm = function(empty){

	var instance = this;
	
	//check if the form is not empty:
	var post = '';
	if($('#css_uploader').length && !empty){
		post = $('#css_uploader').serialize();
		post += '&itemUri='+this.itemUri;
	}else{
		post = {itemSerial: this.itemSerial, itemUri: this.itemUri};
	}
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/manageStyleSheets",
	   data: post,
	   dataType: 'html',
	   success: function(form){
			$(instance.cssFormContent).html(form);
			var timer = null;
			if($('#css_uploader').length){
				$('#css_import-AsyncFileUploader_starter').click(function(){
					if(timer) clearInterval(timer);
					var checkComplete = function(){
						if( $('#css_import').val() != ''){
							clearInterval(timer);
							instance.loadStyleSheetForm();
						}
					};
					
					timer = setInterval(checkComplete, 1000);
					return false;
				});
				
			}
	   }
	});
}

qtiEdit.prototype.deleteStyleSheet = function(css_href){
	var instance = this;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/deleteStyleSheet",
	   data: {
			'itemSerial': this.itemSerial,
			'itemUri': this.itemUri,
			'css_href': css_href
	   },
	   dataType: 'json',
	   success: function(r){
			if(r.deleted){
				instance.loadStyleSheetForm(true);
			}
	   }
	});
}

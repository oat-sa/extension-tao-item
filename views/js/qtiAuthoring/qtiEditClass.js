// alert('qtiEdit loaded');

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
	
	this.currentInteraction = null;
	
	var instance = this;
	
	//init the item's jwysiwyg editor here:
	var addChoiceInteraction = {
		visible : true,
		className: 'add_choice_interaction',
		exec: function(){
			// CL('inserting interaction...');
			//display modal window with the list of available type of interactions
			var interactionType = 'choice';
			
			//insert location of the current interaction in the item:
			this.insertHtml("{qti_interaction_new}");
			
			
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
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction(interactionType, this.getContent(), instance.itemSerial);
		},
		tooltip: 'add associate interaction'
	};

	var addOrderInteraction = {
		visible : true,
		className: 'add_order_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('order', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add order interaction'
	};

	var addMatchInteraction = {
		visible : true,
		className: 'add_match_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
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
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('hottext', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add hot text interaction'
	};

	var addGapMatchInteraction = {
		visible : true,
		className: 'add_gapmatch_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('gapMatch', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add gap match interaction'
	};
	
	var addHotspotInteraction = {
		visible : true,
		className: 'add_hotspot_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('hotspot', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add hot spot interaction'
	};
	
	var addGraphicOrderInteraction = {
		visible : true,
		className: 'add_graphicorder_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('graphicorder', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add graphic order interaction'
	};
	
	var addGraphicAssociateInteraction = {
		visible : true,
		className: 'add_graphicassociate_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('graphicassociate', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add graphic associate interaction'
	};
	
	var addGraphicGapMatchInteraction = {
		visible : true,
		className: 'add_graphicgapmatch_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('graphicgapmatch', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add hot spot interaction'
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
				  visible: false,
				  className: 'h4',
				  command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
				  arguments: ($.browser.msie || $.browser.safari) ? '<h4>' : 'h4',
				  tags: ['h4'],
				  tooltip: 'Header 4'
		  },
		  h5: {
				  visible: false,
				  className: 'h5',
				  command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
				  arguments: ($.browser.msie || $.browser.safari) ? '<h5>' : 'h5',
				  tags: ['h5'],
				  tooltip: 'Header 5'
		  },
		  h6: {
				  visible: false,
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
		  addHotspotInteraction: addHotspotInteraction,
		  addGraphicOrderInteraction: addGraphicOrderInteraction,
		  addGraphicAssociateInteraction: addGraphicAssociateInteraction,
		  addGraphicGapMatchInteraction: addGraphicGapMatchInteraction,
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
	itemData = util.htmlEncode(itemData);
	
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
		
		var $interaction = links[i];
		var interactionSerial = $interaction.attr('id');
		
		instance.interactions[interactionSerial] = interactionSerial;
		
		$interaction.click(function(e){
			e.preventDefault();
			instance.currentInteractionSerial = $(this).attr('id');
			instance.loadInteractionForm(instance.currentInteractionSerial);
		});
		
		//append the delete button:
		$interactionContainer = $interaction.parent('.qti_interaction_box');
		$deleteButton = $('<a/>').appendTo($interactionContainer);
		$deleteButton.append('<img src="http://localhost/tao/views/img/cancel.png">');
		$deleteButton.css('top', 0);
		$deleteButton.css('float', 'right');
		$deleteButton.bind('click', {'interactionSerial': interactionSerial}, function(e){
			instance.deleteInteractions([e.data.interactionSerial]);
		});
	}
}

qtiEdit.prototype.loadInteractionForm = function(interactionSerial){
	var self = this;
	
	if(self.itemSerial){
		$.ajax({
		   type: "POST",
		   url: "/taoItems/QtiAuthoring/editInteraction",
		   data: {
				'interactionSerial': interactionSerial,
				'itemSerial': self.itemSerial
		   },
		   dataType: 'html',
		   success: function(form){
				$(self.interactionFormContent).html(form);
				qtiEdit.initFormElements($(self.interactionFormContent));
				
				position = $(self.interactionFormContent).position();
				window.scrollTo(0, parseInt(position.top));
		   }
		});
	}
	
}

qtiEdit.initFormElements = function($container){
	qtiEdit.mapFileManagerField($container);
	qtiEdit.mapHtmlEditor($container);
}

qtiEdit.mapFileManagerField = function($container){
	$container.find('.qti-file-img').each(function(){
		if($.fn.fmbind){
			//dynamically change the style:
			$(this).width('50%');
			
			//add tao file manager
			$(this).fmbind({type: 'image'}, function(elt, value){
				$(elt).val(value);
				
				$modifiedForm = $(elt).parents('form');
				if($modifiedForm.length){
					//find the active interaction:
					for(var itemSerial in qtiEdit.instances){
						var item = qtiEdit.instances[itemSerial];
					
						var interaction = item.currentInteraction;
						if(interaction){
							var id = $modifiedForm.attr('id');
							if(id.indexOf('ChoiceForm') == 0){
								interaction.modifiedChoices[id] = 'modified';//it is a choice form:
							}else if(id.indexOf('InteractionForm') == 0){
								interaction.modifiedInteraction = true;
							}else if(id.indexOf('GroupForm') == 0){
								interaction.modifiedGroups[id] = 'modified';
							}
						}
					}
				}
			});
		}
	});
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

//TODO: side effect to be fully tested
qtiEdit.destroyHtmlEditor = function($container){
	// CL('$container', $container);
	$container.find('.qti-html-area').each(function(){
		// CL('$(this).siblings(".wysiwyg")', $(this).siblings('.wysiwyg'));
		// if ($(this).css('display') != 'none' && $(this).siblings('.wysiwyg').length){
			try{
				$(this).wysiwyg('destroy');
			}catch(err){
				
			}
			// CL('destroyed!!');
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
	var self = this;
	
	var itemProperties = $('#AssessmentItem_Form').serializeObject();
	itemProperties.itemSerial = this.itemSerial;
	itemProperties.itemUri = this.itemUri;
	itemProperties.itemData = util.htmlEncode(this.itemEditor.wysiwyg('getContent'));
	//could check if an interaction is being edited, so suggest to save it too:
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveItem",
	   data: itemProperties,
	   dataType: 'json',
	   success: function(r){
			if(r.saved){
				createInfoMessage(__('The item has been successfully updated'));
			}
	   }
	});
}

qtiEdit.prototype.preview = function(){
	//save the item before previewing?
	
	// GenerisAction.fullScreen(this.itemSerial, '', '/taoItems/QtiAuthoring/preview');
	var url = '/taoItems/QtiAuthoring/preview';
	url += '?itemSerial='+this.itemSerial;
	window.open(url, 'tao', 'width=800,height=600,menubar=no,toolbar=no,scrollbars=1');
}


qtiEdit.prototype.saveItemData = function(itemSerial){
	
	var instance = this;
	
	if(!itemSerial){
		var itemSerial = instance.itemSerial;
	}
	
	//make sure that the data is up to date:
	
	instance.itemEditor.wysiwyg('saveContent');
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveItemData",
	   data: {
			'itemData': util.htmlEncode(instance.itemEditor.wysiwyg('getContent')),
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
					var interactionSerial = interactionSerials[i];
					if(instance.interactionSerial == interactionSerial){
						// destroy the interaction form:
						$(instance.interactionFormContent).empty();
					}
					delete instance.interactions[interactionSerial];
				
					//delete:
					var $interactions = qtiEdit.getEltInFrame('#'+interactionSerial);
					if($interactions.length){
						if($interactions[0]){
							$interactions[0].parent().remove();
						}
					}
				}
				
				//unload the interaction form if needed
				
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
	
	var self = this;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/editResponseProcessing",
	   data: {
			'itemSerial': self.itemSerial
	   },
	   dataType: 'html',
	   success: function(form){
			$(self.responseProcessingFormContent).html(form);
	   }
	});
}

qtiEdit.prototype.saveResponseProcessing = function($myForm){
	var self = this;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveResponseProcessing",
	   data: $myForm.serialize(),
	   dataType: 'json',
	   success: function(r){
			if(r.saved){
				self.setResponseMode(r.setResponseMode);
				createInfoMessage(__('The response processing has been saved'));
			}
	   }
	});
}

//useful for single page interface only: reload interaction form, to reload 
qtiEdit.prototype.setResponseMode = function(visible){
	if(visible){
		if(this.responseMode){
		
		}else{
			this.responseMode = true;
			//reload the current interaction form entirely, to display the response template
			if(this.currentInteraction) this.loadInteractionForm(this.currentInteraction.interactionSerial);
		}
	}else{
		this.responseMode = false;
		//reload the current interaction form entirely, to display the response template
		if(this.currentInteraction) this.loadInteractionForm(this.currentInteraction.interactionSerial);
	}
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

qtiEdit.prototype.saveCurrentInteraction = function(){
	//auto save the current interaction, after confirming the choice to the user:
}
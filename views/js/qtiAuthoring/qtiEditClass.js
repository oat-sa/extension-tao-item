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
			instance.addInteraction('graphicOrder', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add graphic order interaction'
	};
	
	var addGraphicAssociateInteraction = {
		visible : true,
		className: 'add_graphicassociate_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('graphicAssociate', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add graphic associate interaction'
	};
	
	var addGraphicGapMatchInteraction = {
		visible : true,
		className: 'add_graphicgapmatch_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('graphicGapMatch', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add hot spot interaction'
	};
	
	var addSelectPointInteraction = {
		visible : true,
		className: 'add_selectpoint_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('selectPoint', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add select point interaction'
	};
	
	var addPositionObjectInteraction = {
		visible : false,
		className: 'add_positionobject_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('positionObject', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add position object interaction'
	};
	
	var addSliderInteraction = {
		visible : true,
		className: 'add_slider_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('slider', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add slider interaction'
	};
	
	var addUploadInteraction = {
		visible : true,
		className: 'add_fileupload_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('upload', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add file upload interaction'
	};
	
	var addEndAttemptInteraction = {
		visible : true,
		className: 'add_endattempt_interaction',
		exec: function(){
			this.insertHtml("{qti_interaction_new}");
			instance.addInteraction('endAttempt', this.getContent(), instance.itemSerial);
		},
		tooltip: 'add end attempt interaction'
	};
	
	var saveItemData = {
		visible : false,
		className: 'addInteraction',
		exec: function(){
			instance.saveItemData();
		},
		tooltip: 'save'
	};

	var addMedia = {
		visible : true,
		className: 'addMedia',
		exec: function(){
			
			var self = this;
			var formDataHtml = '<form class="wysiwyg"><fieldset><legend>Insert Media</legend>';
			formDataHtml += '<label>Media URL: <input type="text" name="url" value="http://" /></label><label>Media height: <input type="text" name="mediaHeight" value="" /></label><label>Media width: <input type="text" name="mediaWidth" value="" /></label><label>Media Description: <input type="text" name="description" value="" /></label><input type="submit" class="button" value="Insert Media" /> <input type="reset" value="Cancel" /></fieldset></form>';
			if ($.modal){
				
				$.modal(formDataHtml, {
					onShow: function(dialog){
						var mediaType = '';
						if($.fn.fmbind){
							//add tao file manager
							$('input[name="url"]').fmbind({type: 'file'}, function(elt, value, mediaData){
								$(elt).val(value);
								if(mediaData){
									if(mediaData.height) $('input[name="mediaHeight"]').val(mediaData.height);
									if(mediaData.width) $('input[name="mediaWidth"]').val(mediaData.width);
									if(mediaData.type) mediaType = mediaData.type;
								}
								
							});
						}
						
						$('input:submit', dialog.data).click(function(e){
							e.preventDefault();
							var mediaURL = $('input[name="url"]', dialog.data).val();
							var height = $('input[name="mediaHeight"]', dialog.data).val();
							var width = $('input[name="mediaWidth"]', dialog.data).val();
							var description = $('input[name="description"]', dialog.data).val();
							
							var objectEltHtml='';
							objectEltHtml = "<object data='" + mediaURL + "' height='" + height+ "' width='" + width + "' type='"+mediaType+"'><div class='qti-wysiwyg-mediabox' height='" + height+ "' width='" + width + "'><img  src='"+img_url+"media-display.png' alt='"+description+"' title='"+description+"'/></div></object>";
							
							self.insertHtml(objectEltHtml);
							
							self.saveContent();//line added to update the original textarea
							
							$.modal.close();
						});
						
						$('input:reset', dialog.data).click(function(e){
							e.preventDefault();
							$.modal.close();
						});
					},
					maxWidth: $.fn.wysiwyg.defaults.formWidth,
					maxHeight: $.fn.wysiwyg.defaults.formHeight,
					overlayClose: true,
					containerCss:{
						minHeight: '315px'
					}
				});
			}else{
				
				 if ($.fn.dialog){
					var dialog = $(formDataHtml).appendTo('body');
					dialog.dialog({
						modal: true,
						width: $.fn.wysiwyg.defaults.formWidth,
						height: $.fn.wysiwyg.defaults.formHeight,
						open: function(ev, ui){
							 $('input:submit', $(this)).click(function(e){
							   
							   e.preventDefault();
								var mediaURL = $('input[name="url"]', dialog).val();
								var height = $('input[name="mediaHeight"]', dialog).val();
								var width = $('input[name="mediaWidth"]', dialog).val();
								var description = $('input[name="description"]', dialog).val();
								var objectEltHtml="<object data='" + mediaURL + "' height='" + height+ "' width='" + width + "' type='"+mediaType+"'><img src='' alt='"+description+"' title='"+description+"'/></object>";
								self.insertHtml(objectEltHtml);
							   
							   self.saveContent();//line added to update the original textarea

							   $(dialog).dialog("close");
							 });
							 $('input:reset', $(this)).click(function(e){
								e.preventDefault();
								$(dialog).dialog("close");
							});
						},
						close: function(ev, ui){
							  $(this).dialog("destroy");
						}
					});
				}else{
					// if ($.browser.msie){
						// this.focus();
						// this.editorDoc.execCommand('insertImage', true, null);
					// }
					// else{
						// var szURL = prompt('URL', 'http://');
						// if (szURL && szURL.length > 0){
							// this.editorDoc.execCommand('insertImage', false, szURL);
						// }
					// }
				}
			}
			
		},
		tooltip: 'insert media',
		groupIndex: 2
	};
	
	var instance = this;
	
	this.itemEditor = $(this.itemDataContainer).wysiwyg({
		css: options.css,
		iFrameClass: 'wysiwyg-item',
		controls: {
		  strikeThrough : { visible : true },
		  underline     : { visible : false },
		  insertTable 	: { visible : false },
		  
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

		  addMedia : addMedia,
		  
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
		  addSelectPointInteraction: addSelectPointInteraction,
		  addPositionObjectInteraction: addPositionObjectInteraction,
		  addSliderInteraction: addSliderInteraction,
		  addUploadInteraction: addUploadInteraction,
		  addEndAttemptInteraction: addEndAttemptInteraction,
		  saveItemData: saveItemData
		},
		events: {
		  keyup : function(e){
			if(instance.getDeletedInteractions(true).length > 0){
				if(!confirm(__('please confirm deletion of the interaction'))){
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
			
			editorDoc.body.focus();
		  },
		  unsetHTMLview: function(){
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
	   url: root_url + "/taoItems/QtiAuthoring/addInteraction",
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
		$interaction.mousedown(function(e){
			e.preventDefault();
		});
		$interaction.click(function(e){
			e.preventDefault();
			instance.currentInteractionSerial = $(this).attr('id');
			instance.loadInteractionForm(instance.currentInteractionSerial);
		});
		qtiEdit.makeNoEditable($interaction);
		
		//append the delete button:
		var $interactionContainer = $interaction.parent('.qti_interaction_box');
		$interactionContainer.bind('dragover drop',function(e){
			e.preventDefault();
			return false;
		});
		qtiEdit.makeNoEditable($interactionContainer);
		
		var $deleteButton = $('<span class="qti_interaction_box_delete"></span>').appendTo($interactionContainer);
		$deleteButton.attr('title', __('Delete interaction'));
		$deleteButton.hide();
		$deleteButton.bind('click', {'interactionSerial': interactionSerial}, function(e){
			e.preventDefault();
			if(confirm(__('Please confirm interaction deletion'))){
				instance.deleteInteractions([e.data.interactionSerial]);
			}
			return false;
		});
		$deleteButton.bind('mousedown contextmenu',function(e){
			e.preventDefault();
		});
		
		qtiEdit.makeNoEditable($deleteButton);
		
		$interaction.parent().hover(function(){
			$(this).children('.qti_interaction_box_delete').show();
			if($(this).hasClass('qti_interaction_inline')){
				$(this).css('padding-right', '20px');
			}
		},function(){
			$(this).children('.qti_interaction_box_delete').hide();
			if($(this).hasClass('qti_interaction_inline')){
				$(this).css('padding-right', 0);
			}
		});
	}
}

qtiEdit.makeNoEditable = function($DOMelement){
	if($DOMelement.length){
		// $DOMelement[0].contenteditable = 'false';
		
		$DOMelement.focus(function(e){
			//CL('try removing focus');
			if (e.preventDefault) { e.preventDefault(); } else { e.returnValue = false; }
			$(this).blur();
		});
		
		$DOMelement.keydown(function(){
			//CL('key downed');
		});
		$DOMelement.bind('mousedown contextmenu keypress keydown', function(e){
			//CL(e);
			if (e.preventDefault) { e.preventDefault(); } else { e.returnValue = false; }
			return false;
		});
	}
}

qtiEdit.prototype.loadInteractionForm = function(interactionSerial){
	var self = this;
	
	if(self.itemSerial){
		$.ajax({
		   type: "POST",
		   url: root_url + "/taoItems/QtiAuthoring/editInteraction",
		   data: {
				'interactionSerial': interactionSerial,
				'itemSerial': self.itemSerial
		   },
		   dataType: 'html',
		   success: function(form){
				$(self.interactionFormContent).empty();
				$(self.interactionFormContent).html(form);
				qtiEdit.initFormElements($(self.interactionFormContent));
				
				// var position = $(self.interactionFormContent).position();
				// window.scrollTo(0, parseInt(position.top));
				
				if($myTab) $myTab.tabs("select" , 1);
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
		
		var displayPreview = function(elt, imagePath, width, height){
			if($(elt).hasClass('qti-with-preview') && width && height){
				var maxHeight = 150;
				var maxWidth = 150;
				var baseRatio = width/height;
				var previewDescription = '';
				if(Math.max(width, height)<150){
					//no need to resize
					previewDescription = __('preview (1:1)');
				}else{
					//resize to the maximum lenght:
					previewDescription = __('preview (real size:')+' '+width+'px*'+height+'px)';
					if(height>width){
						height = maxHeight;
						width = height*baseRatio;
					}else{
						width = maxWidth;
						height = width/baseRatio;
					}
				}
				
				//insert the image preview
				var $parentElt = $(elt).parent();
				var $previewElt = $parentElt.find('div.qti-img-preview');
				if(!$previewElt.length){
					
					$previewElt = $('<div class="qti-img-preview">').appendTo($parentElt);
				}
				// var $descriptionElt = $();
				$previewElt.empty().html('<img src="'+util.getMediaResource(imagePath)+'" style="width:'+width+'px;height:'+height+'px;" title="preview" alt="no preview available"/>');
				$previewElt.append('<br/><span class="qti-img-preview-label">'+previewDescription+'</span>');
			}
		}
		
		var imgPath = $(this).val();
		if(imgPath){
			//check if the weight and height are defined:
			var $modifiedForm = $(this).parents('form');
			var height = parseInt($modifiedForm.find('input#object_height').val());
			var width = parseInt($modifiedForm.find('input#object_width').val());
			
			if($(this).hasClass('qti-with-preview') && width && height) displayPreview(this, imgPath, width, height);
		}
		
		if($.fn.fmbind){
			//dynamically change the style:
			$(this).width('50%');
			
			//add tao file manager
			$(this).fmbind({type: 'image'}, function(elt, imgPath, mediaData){
				
				var height = 0;
				var width = 0;
				if(mediaData){
					if(mediaData.height) height = mediaData.height;
					if(mediaData.width) width = mediaData.width;
				}
				
				$(elt).val(imgPath);
				
				var $modifiedForm = $(elt).parents('form');
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
								interaction.setModifiedInteraction(true);
							}else if(id.indexOf('GroupForm') == 0){
								interaction.modifiedGroups[id] = 'modified';
							}
						}
					}
				}
				
				if(height) $modifiedForm.find('input#object_height').val(height);
				if(width) $modifiedForm.find('input#object_width').val(width);
				
				displayPreview(elt, imgPath, width, height);
				
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
			  underline     : { visible : false },
			  insertTable 	: { visible : false },
			  
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
			  html  : { visible: true },
			  h1: { visible: false },
			  h2: { visible: false },
			  h3: { visible: false },
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
			  addHotspotInteraction: {visible:false},
			  addGraphicOrderInteraction: {visible:false},
			  addGraphicAssociateInteraction: {visible:false},
			  addGraphicGapMatchInteraction: {visible:false},
			  addSelectPointInteraction: {visible:false},
			  addPositionObjectInteraction: {visible:false},
			  addSliderInteraction: {visible:false},
			  addUploadInteraction: {visible:false},
			  addEndAttemptInteraction: {visible:false},
			  createHotText: {visible:false},
			  createGap: {visible:false},
			  saveItemData: {visible:false},
			  saveInteractionData: {visible:false}
			};
		
			$(this).wysiwyg({
				iFrameClass: 'wysiwyg-htmlarea',
				controls: controls
			});
		}
	});
}

//TODO: side effect to be fully tested
qtiEdit.destroyHtmlEditor = function($container){
	$container.find('.qti-html-area').each(function(){
		// if ($(this).css('display') != 'none' && $(this).siblings('.wysiwyg').length){
			try{
				$(this).wysiwyg('destroy');
			}catch(err){
				
			}
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

qtiEdit.createInfoMessage = function(message){
	createInfoMessage('<img src="'+img_url+'ok.png" alt="" style="float:left;margin-right:10px;"/>'+message);
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
	var saveItemFunction = function(){
		$.ajax({
		   type: "POST",
		   url: root_url + "/taoItems/QtiAuthoring/saveItem",
		   data: itemProperties,
		   dataType: 'json',
		   success: function(r){
				if(r.saved){
					qtiEdit.createInfoMessage(__('The item has been successfully saved'));
				}
		   }
		});
	}
	
	if(this.currentInteraction){
		if(this.currentInteraction.modifiedInteraction){
			if(confirm(__('The current interaction has been modified but the modifications has not been updated yet,\n do you want to update the interaction with the modifications before saving your item?\n(Otherwise, the modifications are lost)'))){
				this.saveCurrentInteraction(saveItemFunction);
				return;
			}
		}
	}
	
	saveItemFunction();
}

qtiEdit.prototype.preview = function(){
	
	//full preview require item saving before preview:
	// var url = root_url + '/taoItems/Items/preview';
	// url += '?uri='+this.itemUri;
	// url += '&classUri='+this.itemClassUri;
	// url += '&itemSerial='+this.itemSerial;
	
	var url = root_url + '/taoItems/QtiAuthoring/preview';
	url += '?itemSerial='+this.itemSerial;
	
	var openUrlFunction = function(){
		window.open(url, 'QTI authoring - light preview', 'width=800,height=600,menubar=no,toolbar=no,scrollbars=1');
	}
	
	if(this.currentInteraction){
		if(this.currentInteraction.modifiedInteraction){
			if(confirm(__('The current interaction has been modified but the modifications has not been updated yet.\nDo you want to do update the interaction with the modifications before previewing your item?'))){
				this.saveCurrentInteraction(openUrlFunction);
				return;
			}
		}
	}
	
	openUrlFunction();
}

qtiEdit.prototype.debug = function(){
	window.open('/taoItems/QtiAuthoring/debug?itemSerial='+this.itemSerial, 'QTI authoring - debug', 'width=800,height=600,menubar=no,toolbar=no,scrollbars=1');
}

qtiEdit.prototype['export'] = function(){
	//when the export action is transformed into a service:
	//window.open('/taoItems/ItemExport/index?uri='+this.itemUri+'&classUri='+this.itemClassUri, 'QTI authoring - item export', 'width=800,height=600,menubar=no,toolbar=no,scrollbars=1');
	
	window.open('/taoItems/Items/downloadItemContent?uri='+this.itemUri+'&classUri='+this.itemClassUri, 'QTI authoring - item export', 'width=800,height=600,menubar=no,toolbar=no,scrollbars=1');
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
	   url: root_url + "/taoItems/QtiAuthoring/saveItemData",
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
	   url: root_url + "/taoItems/QtiAuthoring/deleteInteractions",
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
							
							// _dump($interactions[0].parent()[0].childNodes);
							// CD($interactions[0].parent(), 'interatiocn to delete');
							// CL('('+$interactions[0].parent()[0].tagName+')'+$interactions[0].parent()[0].innerHtml);
							
							$interactionBlock = $interactions[0].parent();
							$interactionBlock.empty();
							$interactionBlock.detach();
							// $interactionBlock.remove('div');
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
	   url: root_url + "/taoItems/QtiAuthoring/editResponseProcessing",
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
	   url: root_url + "/taoItems/QtiAuthoring/saveResponseProcessing",
	   data: $myForm.serialize(),
	   dataType: 'json',
	   success: function(r){
			if(r.saved){
				self.setResponseMode(r.setResponseMode);
				qtiEdit.createInfoMessage(__('The response processing has been saved'));
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
	   url: root_url + "/taoItems/QtiAuthoring/manageStyleSheets",
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
	   url: root_url + "/taoItems/QtiAuthoring/deleteStyleSheet",
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

qtiEdit.prototype.saveCurrentInteraction = function(callback){
	//auto save the current interaction, after confirming the choice to the user:
	
	if(this.currentInteraction){
		var interaction = this.currentInteraction;
		$(".interaction-form-submitter").click();
		
		var timer = null;
		var stopTimer = function(){
			callback();
			window.clearInterval(timer);
		}
		//check every half a second if all choices have been saved:
		timer = window.setInterval(function(){
			if(!interaction.modifiedChoices.length && !interaction.modifiedGroups.length && !interaction.modifiedInteraction){
				stopTimer();
			}
		}, 500);
	}
	
}

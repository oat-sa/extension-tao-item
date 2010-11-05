// alert('interaction edit loaded');
interactionClass.instances = [];

function interactionClass(interactionSerial, relatedItemSerial, choicesFormContainer, responseFormContainer){
	
	if(!interactionSerial){ throw 'no interaction serial found';}
	if(!relatedItemSerial){ throw 'no related item serial found';}
	
	var defaultResponseFormContainer = {
		responseMappingOptionsFormContainer : '#qtiAuthoring_mapping_container',
		responseGrid: 'qtiAuthoring_response_grid'
	};
	if(!responseFormContainer){
		var responseFormContainer = defaultResponseFormContainer;
	}
	this.responseMappingOptionsFormContainer = responseFormContainer.responseMappingOptionsFormContainer;
	this.responseGrid = responseFormContainer.responseGrid;
	
	this.interactionSerial = interactionSerial;
	this.relatedItemSerial = relatedItemSerial;
	
	this.choices = [];
	this.modifiedInteraction = false;
	this.modifiedChoices = [];
	this.modifiedGroups = [];
	this.orderedChoices = [];
	
	this.initInteractionFormSubmitter();
	
	//always load the mappingForm (show and hide it according to the value of the qtiEdit.responseMappingMode)
	this.loadResponseMappingForm();
	
	//load choices form if necessary:
	if(choicesFormContainer) {
		this.choicesFormContainer = choicesFormContainer;
		this.loadChoicesForm(this.choicesFormContainer);
	}else{
		//immediately set the form change listener (no need to wait for the choice forms)
		this.setFormChangeListener();
		
		//and load the response form and grid:
		new responseClass(this.responseGrid, this);
	}
	
	interactionClass.instances[interactionSerial] = this;
}

interactionClass.prototype.initInteractionFormSubmitter = function(){
	var instance = this;
	$(".interaction-form-submitter").click(function(){
		
		var $myForm = $(this).parents("form");
		//linearize it and post it:
		if(instance.modifiedInteraction){
			instance.saveInteraction($myForm);
		}
		
		for(var groupSerial in instance.modifiedGroups){
			var $groupForm = $('#'+groupSerial);
			
			//linearize+submit:
			if($groupForm.length){
				instance.saveGroup($groupForm);
			}
		}
		
		for(var choiceSerial in instance.modifiedChoices){
			var $choiceForm = $('#'+choiceSerial);
			
			//linearize+submit:
			if($choiceForm.length){
				instance.saveChoice($choiceForm);
			}
		}
		
		//check modified choices then send it as well:
		return false;
	});
}

interactionClass.prototype.saveInteraction = function($myForm){
	//TODO: check unicity of the id:
	
	//serialize the order:
	var orderedChoices = '';
	if(this.orderedChoices[0]){
		for(var i=0;i<this.orderedChoices.length;i++){
			orderedChoices += '&choiceOrder['+i+']='+this.orderedChoices[i];
		}
	}else{
		//for match and gapmatch interaction:
		var i = 0;
		for(var groupSerial in this.orderedChoices){
			orderedChoices += '&choiceOrder'+i+'[groupSerial]='+groupSerial;
			for(var j=0; j<this.orderedChoices[groupSerial].length; j++){
				orderedChoices += '&choiceOrder'+i+'['+j+']='+this.orderedChoices[groupSerial][j];
			}
			i++;
		}
	}
	
	//check if it is required to save data (hotText and gapMatch interactions):
	var interactionData = '';
	if(this.interactionDataContainer){
		if($(this.interactionDataContainer).length && this.interactionEditor.length){
			//there is a wysiwyg editor that contains the interaciton data:
				interactionData = '&data='+this.interactionEditor.wysiwyg('getContent');
		}
	}
	
	var interaction = this;
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveInteraction",
	   data: $myForm.serialize()+orderedChoices+interactionData,
	   dataType: 'json',
	   success: function(r){
			if(r.saved){
				createInfoMessage(__('Modification on interaction applied'));
				interaction.modifiedInteraction = false;
				
				if(r.reloadResponse){
					new responseClass(interaction.responseGrid, interaction);
				}
			}
	   }
	});
}

interactionClass.prototype.saveChoice = function($choiceForm){

	var interaction = this;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveChoice",
	   data: $choiceForm.serialize(),
	   dataType: 'json',
	   success: function(r){
			
			if(!r.saved){
				createErrorMessage(__('The choice cannot be saved'));
			}else{
				createInfoMessage(__('Modification on choice applied'));
				delete interaction.modifiedChoices['ChoiceForm_'+r.choiceSerial];
				
				//only when the identifier has changed:
				if(r.reload){
					interaction.loadChoicesForm();
				}else if(r.identifierUpdated){
					new responseClass(interaction.responseGrid, interaction);
				}
				
			}
	   }
	});
}

interactionClass.prototype.saveGroup = function($groupForm){

	var interaction = this;
	//save group order?
	
	var choiceOrder = ''
	var i = 0;
	for(var order in this.orderedChoices){
		choiceOrder += '&choiceOrder['+i+']='+this.orderedChoices[order];
		i++;
	}
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveGroup",
	   data: $groupForm.serialize()+choiceOrder,
	   dataType: 'json',
	   success: function(r){
			
			if(!r.saved){
				createErrorMessage(__('The choice cannot be saved'));
			}else{
				createInfoMessage(__('Modification on choice applied'));
				delete interaction.modifiedGroups['GroupForm_'+r.choiceSerial];
				
				//only when the identifier has changed:
				if(r.reload){
					interaction.loadChoicesForm();
				}else if(r.identifierUpdated){
					new responseClass(interaction.responseGrid, interaction);
				}
				
			}
	   }
	});
}

interactionClass.prototype.loadResponseMappingForm = function(){
	var relatedItem = this.getRelatedItem();
	relatedItem.responseMappingMode = true;
	var interacton = this;
	
	if(relatedItem){
		$.ajax({
		   type: "POST",
		   url: "/taoItems/QtiAuthoring/editMappingOptions",
		   data: {
				'interactionSerial': this.interactionSerial
		   },
		   dataType: 'html',
		   success: function(form){
				
				$formContainer = $(interacton.responseMappingOptionsFormContainer);
				$formContainer.html(form);
				if(relatedItem.responseMappingMode){
					$formContainer.show();
				}else{
					$formContainer.hide();
				}
		   }
		});
	}else{
		throw 'the related item cannot be found';
	}
	
}

interactionClass.prototype.getRelatedItem = function(strict){
	if(qtiEdit.instances[this.relatedItemSerial]){
		return qtiEdit.instances[this.relatedItemSerial];
	}
	if(strict){throw 'no related item found';}
	return null;
}

interactionClass.prototype.loadChoicesForm = function(containerSelector){
	if(!containerSelector){
		var containerSelector = '';
		if(this.choicesFormContainer){
			var containerSelector = this.choicesFormContainer;
		}
	}
	var interactionSerial = this.interactionSerial;
	var interaction = this;
	
	if($(containerSelector).length){
		$.ajax({
		   type: "POST",
		   url: "/taoItems/QtiAuthoring/editChoices",
		   data: {
				'interactionSerial': interactionSerial
		   },
		   dataType: 'html',
		   success: function(form){
				$formContainer = $(containerSelector);
				$formContainer.html(form);
				
				qtiEdit.mapHtmlEditor($formContainer);
				interaction.setFormChangeListener();
				
				//reload the grid:
				new responseClass(interaction.responseGrid, interaction);
		   }
		});
	}
	
}

interactionClass.prototype.addChoice = function($appendTo, containerClass, groupSerial){
	
	if(!$appendTo || !$appendTo.length){
		throw 'the append target element do not exists';
	}
	
	var postData = {};
	postData.interactionSerial = this.interactionSerial;
	
	if(groupSerial){
		postData.groupSerial = groupSerial;
	}
	
	var interaction = this;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/addChoice",
	   data: postData,
	   dataType: 'json',
	   success: function(r){
			CL('choice added');
			if(r.added){
				
				if(r.reload){
					interaction.loadChoicesForm();
					return;
				}
				
				var $newFormElt = $('<div/>');
				$newFormElt.attr('id', r.choiceSerial);
				$newFormElt.attr('class', containerClass);
				$newFormElt.append(r.choiceForm);
				$appendTo.append($newFormElt);
				
				$newFormElt.hide();
				interaction.initToggleChoiceOptions();
				$newFormElt.show();
				
				qtiEdit.mapHtmlEditor($newFormElt);
				interaction.setFormChangeListener('#'+r.choiceSerial);
				
				
				//add to the local choices order array:
				//if interaction type is match, save the new choice in one of the group array:
				if(r.groupSerial){
					if(interaction.orderedChoices[r.groupSerial]){
						interaction.orderedChoices[r.groupSerial].push(r.choiceSerial);
					}else{
						throw 'the group serial is not defined in the ordered choices array';
					}
				}else{
					interaction.orderedChoices.push(r.choiceSerial);
				}
				
				//rebuild the response grid:
				new responseClass(interaction.responseGrid, interaction);
			}
	   }
	});
}

interactionClass.prototype.initToggleChoiceOptions = function(options){
	var interaction = this;
	$('.form-group').each(function(){
		interaction.toggleChoiceOptions($(this), options);
	});
}

interactionClass.prototype.toggleChoiceOptions = function($group, options){
	var interaction = this;
	var groupId = $group.attr('id');
	if(groupId.indexOf('choicePropOptions') == 0){
		
		if(!options){
			var options = {'delete': true, 'group':true};
		}else{
			if(options['delete'] !== false){
				options['delete'] = true;
			}
			if(options.group !== false){
				options.group = true;
			}
		}
		
		// it is a choice group:
		if($('#a_'+groupId).length){
			$('#a_'+groupId).remove();
		}
		if($('#delete_'+groupId).length){
			$('#delete_'+groupId).remove();
		}
		
		if(options.delete){
			var $deleteElt = $('<span id="delete_'+groupId+'" title="'+__('Delete choice')+'" class="form-group-control choice-button-delete ui-icon ui-icon-circle-close"></span>');
			$group.before($deleteElt);
			// $deleteElt.css('position', 'relative');
			
			//add click event listener:
			$('#delete_'+groupId).click(function(){
				if(confirm('Do you want to delete the choice?')){
					var choiceSerial = $(this).attr('id').replace('delete_choicePropOptions_', '');
					// CL('deleting the choice '+choiceSerial);
					interaction.deleteChoice(choiceSerial);
				}
			});
		}
		
		if(options.group){
			var $buttonElt = $('<span id="a_'+groupId+'" title="'+__('Advanced options')+'" class="form-group-control choice-button-advanced ui-icon ui-icon-circle-plus"></span>');
			$group.before($buttonElt);
			
			//TODO: put into a css file!!
			// $buttonElt.css('position', 'relative');
			// $buttonElt.css('left','18px');
			// $buttonElt.css('top','-16px');
			
			// $group.css('position', 'relative');
			// $group.css('top','-19px');
			// $group.css('left','20px');
			// $group.width('90%');
			
			$group.hide();
			
			// $('#a_'+groupId).unbind('click');
			$('#a_'+groupId).toggle(function(){
				$(this).switchClass('ui-icon-circle-plus', 'ui-icon-circle-minus');
				$('#'+groupId).show().effect('slide');
			},function(){
				$(this).switchClass('ui-icon-circle-minus', 'ui-icon-circle-plus');
				$('#'+groupId).hide().effect('fold');
			});
		}
		
	}
}

interactionClass.prototype.setFormChangeListener = function(target){
	
	var interaction = this;
	
	if(!target){
		var target = "form";
	}else{
		if(!$(target).length){
			return false;
		}
	}
	
	var setChanges = function($modifiedForm){
		if($modifiedForm.length){
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
	
	$("form").children().change(function(){
		var $modifiedForm = $(this).parents('form');
		setChanges($modifiedForm);
	});
	
	$("form").find('iframe').each(function(){
		var $modifiedForm = $(this).parents('form');
		$($(this)[0].contentWindow.document).keyup(function(){
			setChanges($modifiedForm);
		});
	});
	
	return true;
}

interactionClass.prototype.setOrderedChoicesButtons = function(list){
	 return false;//deactivate it for now
	 
	var interaction = this;
	var total = list.length;
	for(var i=0; i<total; i++){
		$upElt = $('<span id="up_'+list[i]+'" title="'+__('Move Up')+'" class="form-group-control choice-button-up ui-icon ui-icon-circle-triangle-n"></span>');
		
		//get the corresponding group id:
		$("#delete_choicePropOptions_"+list[i]).after($upElt);
		$upElt.click(function(){
			var choiceSerial = $(this).attr('id').substr(3);
			interaction.orderedChoices = interaction.switchOrder(interaction.orderedChoices, choiceSerial, 'up');
		});
		
		$downElt = $('<span id="down_'+list[i]+'" title="'+__('Move Down')+'" class="form-group-control choice-button-down ui-icon ui-icon-circle-triangle-s"></span>');
		$upElt.after($downElt);
		$downElt.click(function(){
			var choiceSerial = $(this).attr('id').substr(5);
			interaction.orderedChoices = interaction.switchOrder(interaction.orderedChoices, choiceSerial, 'down');
		});
	}
}

interactionClass.prototype.setOrderedMatchChoicesButtons = function(doubleList){
	return false;
	
	var interaction = this;
	
	// var length = doubleList.length;
	for(var groupSerial in doubleList){
		// interactionEdit.setOrderedChoicesButtons(doubleList[j]);
		var list = doubleList[groupSerial];
		var total = list.length;
		for(var i=0; i<total; i++){
			if(!list[i]){
				throw 'broken order in array';
				break;
			}
			
			$upElt = $('<span id="up_'+list[i]+'" title="'+__('Move Up')+'" class="form-group-control choice-button-up ui-icon ui-icon-circle-triangle-n"></span>');
			
			//get the corresponding group id:
			$("#a_choicePropOptions_"+list[i]).after($upElt);
			$upElt.bind('click', {'groupSerial':groupSerial}, function(e){
				var choiceSerial = $(this).attr('id').substr(3);
				interaction.orderedChoices[e.data.groupSerial] = interaction.switchOrder(interaction.orderedChoices[e.data.groupSerial], choiceSerial, 'up');
			});
			
			$downElt = $('<span id="down_'+list[i]+'" title="'+__('Move Down')+'" class="form-group-control choice-button-down ui-icon ui-icon-circle-triangle-s"></span>');
			$upElt.after($downElt);
			$downElt.bind('click', {'groupSerial':groupSerial}, function(e){
				var choiceSerial = $(this).attr('id').substr(5);
				interaction.orderedChoices[e.data.groupSerial] = interaction.switchOrder(interaction.orderedChoices[e.data.groupSerial], choiceSerial, 'down');
			});
		}
	}
}

interactionClass.prototype.switchOrder = function(list, choiceId, direction){
	
	var currentPosition = 0;
	for(var i=0; i<list.length; i++){
		if(list[i] == choiceId){
			currentPosition = i;
			break;
		}
	}
	try{
	var $parentFormChoiceContainer = $('#'+choiceId).parents(".formContainer_choices");
	var newOrder = [];
	var sorted = false;
	switch(direction){
		case 'up':{
			//get the previous choice:
			if(currentPosition>0){
				qtiEdit.destroyHtmlEditor($parentFormChoiceContainer);
				$('#'+choiceId).insertBefore('#'+list[currentPosition-1]);
				qtiEdit.mapHtmlEditor($parentFormChoiceContainer);
				
				// $('#'+choiceId).remove();
				for(var i=0;i<list.length;i++){
					if(i == currentPosition-1){
						newOrder[i] = list[i+1];
					}else if(i == currentPosition){
						newOrder[i] = list[i-1];
					}else{
						newOrder[i] = list[i];
					}
				}
				
				sorted = true;
			}
			break;
		}
		case 'down':{
			//get the previous choice:
			if(currentPosition < list.length-1){
				try{
					qtiEdit.destroyHtmlEditor($parentFormChoiceContainer);
					$('#'+choiceId).insertAfter('#'+list[currentPosition+1]);
					qtiEdit.mapHtmlEditor($parentFormChoiceContainer);
				}catch(err){
					
				}
				// $('#'+choiceId).remove();
				var newOrder = [];
				for(var i=0;i<list.length;i++){
					if(i == currentPosition){
						newOrder[i] = list[i+1];
					}else if(i == currentPosition+1){
						newOrder[i] = list[i-1];
					}else{
						newOrder[i] = list[i];
					}
				}
				
				sorted = true;
			}
			break;
		}
	}
	
	if(sorted){
		//indicates that the interaction has changed:
		this.modifiedInteraction = true;
	}else{
		//return the old order
		newOrder = list;
	}
	
	}catch(err){
		
	}
	return newOrder;
}

interactionClass.prototype.deleteChoice = function(choiceSerial, reloadInteraction){

	var interaction = this;
	delete interaction.choices[choiceSerial];
	
	if(!reloadInteraction) var reloadInteraction = false;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/deleteChoice",
	   data: {
			'choiceSerial': choiceSerial,
			'groupSerial': choiceSerial,
			'reloadInteraction': reloadInteraction,
			'interactionSerial': interaction.interactionSerial
	   },
	   dataType: 'json',
	   success: function(r){
			if(r.deleted){
				if(r.reloadInteraction){
					// var item = interaction.getRelatedItem();
					interaction.getRelatedItem(true).loadInteractionForm(interaction.interactionSerial);
					return;
				}else if(r.reload){
					//reload form choices
					interaction.loadChoicesForm();
					return;
				}
			
				$('#'+choiceSerial).remove();
				//TODO: need to be optimized: only after the last choice saving
				new responseClass(interaction.responseGrid, interaction);
				interaction.saveInteractionData();
			}else{
				interaction.choices[choiceSerial] = choiceSerial;
			}
	   }
	});
}

interactionClass.prototype.saveResponseMappingOptions = function($myForm){
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveMappingOptions",
	   data: $myForm.serialize(),
	   dataType: 'json',
	   success: function(r){
			if(r.saved){
				createInfoMessage(__('The mapping options have been applied'));
			}
	   }
	});
}

interactionClass.prototype.buildInteractionEditor = function(interactionDataContainerSelector, extraControls){
	
	//re-init the interaction editor object: 
	this.interactionEditor = new Object();
	
	//interaction data container selector:
	this.interactionDataContainer = interactionDataContainerSelector;
	
	var interaction = this;
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
	  
	  saveInteractionData: {
			visible : true,
			className: 'addInteraction',
			exec: function(){
				interaction.saveInteractionData();
			},
			tooltip: 'save interaction data'
		}
	};
	
	if(extraControls){
		var controls = $.extend(controls, extraControls);
	}
	
	this.interactionEditor = $(this.interactionDataContainer).wysiwyg({
		controls: controls,
		gridComplete: this.bindChoiceLinkListener,
		events: {
			  keyup : function(e){
				if(interaction.getDeletedChoices(true).length > 0){
					if(!confirm('please confirm deletion of the choice(s)')){
						// undo:
						interaction.interactionEditor.wysiwyg('undo');
					}else{
						var deletedChoices = interaction.getDeletedChoices();
						for(var key in deletedChoices){
							//delete choices one by one:
							interaction.deleteChoice(deletedChoices[key]);
						}
					}
					return false;
				}
			  }
		}
	});
	
	//the binding require the modified html data to be ready
	setTimeout(function(){interaction.bindChoiceLinkListener();},1000);
}

interactionClass.prototype.saveInteractionData = function(interactionSerial){
	// if(!interactionSerial){
		// if(interactionEdit.interactionSerial){
			// var interactionSerial = interactionEdit.interactionSerial;
		// }else{
			// throw 'no interaction serial found to save the data from';
			// return false;
		// }
		
	// }
	
	var interactionSerial = this.interactionSerial;
	
	if(this.interactionDataContainer){
		if($(this.interactionDataContainer).length && this.interactionEditor.length){
			//save data if and only if the data content exists
			$.ajax({
			   type: "POST",
			   url: "/taoItems/QtiAuthoring/saveInteractionData",
			   data: {
					'interactionData': this.interactionEditor.wysiwyg('getContent'),
					'interactionSerial': interactionSerial
			   },
			   dataType: 'json',
			   success: function(r){
					CL('interaction data saved');
			   }
			});
			
			return true;
		}
	}
	
	return false;
}

interactionClass.prototype.getDeletedChoices = function(one){
	var deletedChoices = [];
	var interactionData = $(this.interactionDataContainer).val();//TODO: improve with the use of regular expressions:
	for(var choiceSerial in this.choices){
		if(interactionData.indexOf(choiceSerial)<0){
			//not found so considered as deleted:
			deletedChoices.push(choiceSerial);
			if(one){
				return deletedChoices;
			}
		}
	}
	
	return deletedChoices;
}

//idem for adding gap in gapmatch
interactionClass.prototype.addHotText = function(interactionData, $appendTo){
	var interactionSerial = this.interactionSerial;
	var interaction = this;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/addHotText",
	   data: {
			'interactionSerial': interactionSerial,
			'interactionData': interactionData
	   },
	   dataType: 'json',
	   success: function(r){
			//set the content:
			interaction.interactionEditor.wysiwyg('setContent', $("<div/>").html(r.interactionData).html());
			
			//then add listener
			interaction.bindChoiceLinkListener();
			
			//add choice form:
			var $newFormElt = $('<div/>');
			$newFormElt.attr('id', r.choiceSerial);
			$newFormElt.attr('class', 'formContainer_choice');//hard-coded: bad
			$newFormElt.append(r.choiceForm);
			
			//add to parameter
			if(!$appendTo){
				var $appendTo = $('#formContainer_choices');
			}
			$appendTo.append($newFormElt);
			
			$newFormElt.hide();
			interaction.initToggleChoiceOptions();//{'delete':false}
			$newFormElt.show();
			
			interaction.setFormChangeListener('#'+r.choiceSerial);
					
			//rebuild the response grid:
			new responseClass(interaction.responseGrid, interaction);
	   }
	});
}

interactionClass.prototype.addGap = function(interactionData, $appendTo){

	var interactionSerial = this.interactionSerial;
	var interaction = this;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/addGroup",
	   data: {
			'interactionSerial': interactionSerial,
			'interactionData': interactionData
	   },
	   dataType: 'json',
	   success: function(r){
			//set the content:
			interaction.interactionEditor.wysiwyg('setContent', $("<div/>").html(r.interactionData).html());
			
			//then add listener
			interaction.bindChoiceLinkListener();//ok keep
			
			//reload choices form
			if(r.reload){
				interaction.loadChoicesForm();
				return;
			}
			
			//add choice form:
			var $newFormElt = $('<div/>');
			$newFormElt.attr('id', r.groupSerial);//r.groupSerial
			$newFormElt.attr('class', 'formContainer_choice');//hard-coded: bad
			$newFormElt.append(r.groupForm);
			
			//add to parameter
			if(!$appendTo){
				var $appendTo = $('#formContainer_groups');//append to group!
			}
			$appendTo.append($newFormElt);
			
			$newFormElt.hide();
			interaction.initToggleChoiceOptions({'delete': false});
			$newFormElt.show();
			
			interaction.setFormChangeListener('#'+r.groupSerial);
						
			//rebuild the response grid:
			new responseClass(interaction.responseGrid, interaction);
			
			
	   }
	});
}

interactionClass.prototype.bindChoiceLinkListener = function(){
	
	//destroy all listeners:
	
	//reset the choice array:
	this.choices = [];
	
	var links = qtiEdit.getEltInFrame('.qti_choice_link');
	for(var i in links){
		
		var choiceSerial = links[i].attr('id');
		
		this.choices[choiceSerial] = choiceSerial;
		
		links[i].unbind('click').click(function(){
			//focus the clicked choice form:
			window.location.hash = '#'+$(this).attr('id');
			
			//add then remove the highlight class
			// CL('highlighting the choice', $(this).attr('id'));
		});
		
	}
	
}
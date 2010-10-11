alert('interaction edit loaded');

interactionEdit = new Object();
interactionEdit.interactionSerial = '';

//record all choices of the interaction: (note: gapmatch interaction has choices that are groups in the php model)
interactionEdit.choices = [];
// interactionEdit.groups = [];

interactionEdit.modifiedInteraction = false;
interactionEdit.modifiedChoices = [];
// interactionEdit.modifiedGroups = [];
interactionEdit.orderedChoices = [];

interactionEdit.setOrderedChoicesButtons = function(list){
	var total = list.length;
	for(var i=0; i<total; i++){
		$upElt = $('<span id="up_'+list[i]+'" title="'+__('Move Up')+'" class="form-group-control ui-icon ui-icon-circle-triangle-n"></span>');
		
		//get the corresponding group id:
		$("#a_choicePropOptions_"+list[i]).after($upElt);
		$upElt.click(function(){
			var choiceSerial = $(this).attr('id').substr(3);
			interactionEdit.orderedChoices = interactionEdit.switchOrder(interactionEdit.orderedChoices, choiceSerial, 'up');
		});
		
		$downElt = $('<span id="down_'+list[i]+'" title="'+__('Move Down')+'" class="form-group-control ui-icon ui-icon-circle-triangle-s"></span>');
		$upElt.after($downElt);
		$downElt.click(function(){
			var choiceSerial = $(this).attr('id').substr(5);
			interactionEdit.orderedChoices = interactionEdit.switchOrder(interactionEdit.orderedChoices, choiceSerial, 'down');
		});
	}
}

interactionEdit.switchOrder = function(list, choiceId, direction){
	
	var currentPosition = 0;
	for(var i=0; i<list.length; i++){
		if(list[i] == choiceId){
			currentPosition = i;
			break;
		}
	}
	
	var newOrder = [];
	var sorted = false;
	switch(direction){
		case 'up':{
			//get the previous choice:
			if(currentPosition>0){
				$('#'+choiceId).insertBefore('#'+list[currentPosition-1]);
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
				$('#'+choiceId).insertAfter('#'+list[currentPosition+1]);
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
		interactionEdit.modifiedInteraction = true;
	}else{
		//return the old order
		newOrder = list;
	}
	
	return newOrder;
}

interactionEdit.sortOrderedChoices = function(list, order){
	
}

interactionEdit.toggleChoiceOptions = function($group, options){
	var groupId = $group.attr('id');
	if(groupId.indexOf('choicePropOptions') == 0){
		
		if(!options){
			var options = {'delete': true, 'group':true};
		}else{
			if(options.delete !== false){
				options.delete = true;
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
		CL('delete', options);
		if(options.delete){
			var $deleteElt = $('<span id="delete_'+groupId+'" title="'+__('Delete choice')+'" class="form-group-control ui-icon ui-icon-circle-close"></span>');
			$group.before($deleteElt);
			$deleteElt.css('position', 'relative');
			
			//add click event listener:
			$('#delete_'+groupId).click(function(){
				if(confirm('Do you want to delete the choice?')){
					var choiceSerial = $(this).attr('id').replace('delete_choicePropOptions_', '');
					// CL('deleting the choice '+choiceSerial);
					interactionEdit.deleteChoice(choiceSerial);
				}
			});
		}
		
		if(options.group){
			var $buttonElt = $('<span id="a_'+groupId+'" title="'+__('Advanced options')+'" class="form-group-control ui-icon ui-icon-circle-plus"></span>');
			$group.before($buttonElt);
			
			//TODO: put into a css file!!
			$buttonElt.css('position', 'relative');
			$buttonElt.css('left','18px');
			$buttonElt.css('top','-16px');
			
			$group.css('position', 'relative');
			$group.css('top','-19px');
			$group.css('left','20px');
			$group.width('90%');
			
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

interactionEdit.initToggleChoiceOptions = function(options){
	$('.form-group').each(function(){
		interactionEdit.toggleChoiceOptions($(this), options);
	});
}

interactionEdit.initInteractionFormSubmitter = function(){
	$(".form-submiter").click(function(){
		
		var $myForm = $(this).parents("form");
		//linearize it and post it:
		if(interactionEdit.modifiedInteraction){
			interactionEdit.saveInteraction($myForm);
		}
		
		for(var choiceSerial in interactionEdit.modifiedChoices){
			var $choiceForm = $('#'+choiceSerial);
			
			//linearize+submit:
			if($choiceForm.length){
				interactionEdit.saveChoice($choiceForm);
			}
		}
		
		//check modified choices then send it as well:
		return false;
	});
}

interactionEdit.saveInteraction = function($myForm){
	//TODO: check unicity of the id:
	// CL("saving "+$myForm.attr('id'), $myForm.serialize());
	//serialize the order:
	var orderedChoices = '';
	if(interactionEdit.orderedChoices[0]){
		for(var i=0;i<interactionEdit.orderedChoices.length;i++){
			orderedChoices += '&choiceOrder['+i+']='+interactionEdit.orderedChoices[i];
		}
	}else{
		//for match and gapmatch interaction:
		var i = 0;
		for(var groupSerial in interactionEdit.orderedChoices){
			orderedChoices += '&choiceOrder'+i+'[groupSerial]='+groupSerial;
			for(var j=0; j<interactionEdit.orderedChoices[groupSerial].length; j++){
				orderedChoices += '&choiceOrder'+i+'['+j+']='+interactionEdit.orderedChoices[groupSerial][j];
			}
			i++;
		}
	}
	
	//check if it is required to save data (hotText and gapMatch interactions):
	var interactionData = '';
	if(interactionEdit.interactionDataContainer){
		if($(interactionEdit.interactionDataContainer).length && interactionEdit.interactionEditor.length){
			//there is a wysiwyg editor that contains the interaciton data:
				interactionData = '&data='+interactionEdit.interactionEditor.wysiwyg('getContent');
		}
	}
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveInteraction",
	   data: $myForm.serialize()+orderedChoices+interactionData,
	   dataType: 'json',
	   success: function(r){
			// $(interactionEdit.interactionFormContent).html(form);
			if(r.saved){
				createInfoMessage(__('The interaction has been saved'));
				interactionEdit.modifiedInteraction = false;

			}
	   }
	});
}

interactionEdit.saveInteractionData = function(interactionSerial){
	if(!interactionSerial){
		if(interactionEdit.interactionSerial){
			var interactionSerial = interactionEdit.interactionSerial;
		}else{
			throw 'no interaction serial found to save the data from';
			return false;
		}
		
	}
	
	if(interactionEdit.interactionDataContainer){
		if($(interactionEdit.interactionDataContainer).length && interactionEdit.interactionEditor.length){
			//save data if and only if the data content exists
			$.ajax({
			   type: "POST",
			   url: "/taoItems/QtiAuthoring/saveInteractionData",
			   data: {
					'interactionData': interactionEdit.interactionEditor.wysiwyg('getContent'),
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

interactionEdit.getDeletedChoices = function(one){
	var deletedChoices = [];
	var interactionData = $(interactionEdit.interactionDataContainer).val();//TODO: improve with the use of regular expressions:
	for(var choiceSerial in interactionEdit.choices){
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

interactionEdit.bindChoiceLinkListener = function(){
	
	//destroy all listeners:
	
	//reset the choice array:
	interactionEdit.choices = [];
	
	var links = qtiEdit.getEltInFrame('.qti_choice_link');
	for(var i in links){
		
		var choiceSerial = links[i].attr('id');
		
		interactionEdit.choices[choiceSerial] = choiceSerial;
		
		links[i].unbind('click').click(function(){
			//focus the clicked choice form:
			window.location.hash = '#'+$(this).attr('id');
			
			//add then remove the highlight class
			// CL('highlighting the choice', $(this).attr('id'));
		});
		
	}
	
}

interactionEdit.saveChoice = function($choiceForm){
	// CL("saving "+$choiceForm.attr('id'), $choiceForm.serialize());
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveChoice",
	   data: $choiceForm.serialize(),
	   dataType: 'json',
	   success: function(r){
			// $(interactionEdit.interactionFormContent).html(form);
			if(!r.saved){
				createErrorMessage(__('The choice cannot be saved'));
			}else{
				createInfoMessage(__('The choice has been saved'));
				delete interactionEdit.modifiedChoices['ChoiceForm_'+r.choiceSerial];
				
				//only when the identifier has changed:
				responseEdit.buildGrid(qtiEdit.responseGrid, interactionEdit.interactionSerial);
			}
	   }
	});
}



interactionEdit.loadResponseMappingForm = function(){
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/editMappingOptions",
	   data: {
			'interactionSerial': interactionEdit.interactionSerial
	   },
	   dataType: 'html',
	   success: function(form){
			$formContainer = $(qtiEdit.responseMappingOptionsFormContainer);
			$formContainer.html(form);
			if(qtiEdit.responseMappingMode){
				$formContainer.show();
			}else{
				$formContainer.hide();
			}
	   }
	});
}

interactionEdit.loadChoicesForm = function(containerSelector){
	if(!containerSelector){
		var containerSelector = '';
		if(interactionEdit.choicesFormContainer){
			var containerSelector = interactionEdit.choicesFormContainer;
		}
	}
	if($(containerSelector).length){
		$.ajax({
		   type: "POST",
		   url: "/taoItems/QtiAuthoring/editChoices",
		   data: {
				'interactionSerial': interactionEdit.interactionSerial
		   },
		   dataType: 'html',
		   success: function(form){
				$formContainer = $(containerSelector);
				$formContainer.html(form);
				
				//reload the grid:
				responseEdit.buildGrid(qtiEdit.responseGrid, interactionEdit.interactionSerial);
		   }
		});
	}
	
}

interactionEdit.saveResponseMappingOptions = function($myForm){
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveMappingOptions",
	   data: $myForm.serialize(),
	   dataType: 'json',
	   success: function(r){
			if(r.saved){
				createInfoMessage(__('The mapping options has been saved'));
			}
	   }
	});
}

interactionEdit.setFormChangeListener = function(target){
	if(!target){
		var target = "form";
	}else{
		if(!$(target).length){
			return false;
		}
	}
	
	// $("form").children().unbind('change');//use finely targetted object
	$("form").children().change(function(){
		// CL('changed', $(this).parents('form').attr('id'));
		var $modifiedForm = $(this).parents('form');
		if($modifiedForm.length){
			var id = $modifiedForm.attr('id');
			if(id.indexOf('ChoiceForm') == 0){
				//it is a choice form:
				interactionEdit.modifiedChoices[id] = 'modified';
			}else if(id.indexOf('InteractionForm') == 0){
				interactionEdit.modifiedInteraction = true;
			}/*else if(id.indexOf('ChoiceForm_group') == 0){
				interactionEdit.modifiedGroups[id] = 'modified';
			}*/
		}
	});
	
	return true;
}

interactionEdit.addChoice = function(interactionSerial, $appendTo, containerClass, groupSerial){
	
	if(!$appendTo || !$appendTo.length){
		throw 'the append target element do not exists';
	}
	
	var postData = {};
	if(!interactionSerial && interactionEdit.interactionSerial){
		var interactionSerial = interactionEdit.interactionSerial;
	}
	postData.interactionSerial = interactionSerial;
	
	if(groupSerial){
		postData.groupSerial = groupSerial;
	}
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/addChoice",
	   data: postData,
	   dataType: 'json',
	   success: function(r){
			CL('choice added');
			if(r.added){
				
				if(r.reload){
					interactionEdit.loadChoicesForm();
					return;
				}
				
				var $newFormElt = $('<div/>');
				$newFormElt.attr('id', r.choiceSerial);
				$newFormElt.attr('class', containerClass);
				$newFormElt.append(r.choiceForm);
				$appendTo.append($newFormElt);
				
				$newFormElt.hide();
				interactionEdit.initToggleChoiceOptions();
				$newFormElt.show();
				
				interactionEdit.setFormChangeListener('#'+r.choiceSerial);
				
				//add to the local choices order array:
				//if interaction type is match, save the new choice in one of the group array:
				if(r.groupSerial){
					if(interactionEdit.orderedChoices[r.groupSerial]){
						interactionEdit.orderedChoices[r.groupSerial].push(r.choiceSerial);
					}else{
						throw 'the group serial is not defined in the ordered choices array';
					}
				}else{
					interactionEdit.orderedChoices.push(r.choiceSerial);
				}
				
				
				//rebuild the response grid:
				responseEdit.buildGrid(qtiEdit.responseGrid, interactionEdit.interactionSerial);
			}
	   }
	});
}

interactionEdit.deleteChoice = function(choiceSerial, reloadInteraction){
	delete interactionEdit.choices[choiceSerial];
	
	if(!reloadInteraction) var reloadInteraction = false;
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/deleteChoice",
	   data: {
			'choiceSerial': choiceSerial,
			'groupSerial': choiceSerial,
			'reloadInteraction': reloadInteraction,
			'interactionSerial': interactionEdit.interactionSerial
	   },
	   dataType: 'json',
	   success: function(r){
			if(r.deleted){
				if(r.reloadInteraction){
					qtiEdit.loadInteractionForm(interactionEdit.interactionSerial);
					return;
				}else if(r.reload){
					//reload form choices
					interactionEdit.loadChoicesForm();
					return;
				}
			
				$('#'+choiceSerial).remove();
				//TODO: need to be optimized: only after the last choice saving
				responseEdit.buildGrid(qtiEdit.responseGrid, interactionEdit.interactionSerial);
				interactionEdit.saveInteractionData();
			}else{
				interactionEdit.choices[choiceSerial] = choiceSerial;
			}
	   }
	});
}

//idem for adding gap in gapmatch
interactionEdit.addHotText = function(interactionData, interactionSerial, $appendTo){
	if(!interactionSerial){
		var interactionSerial = interactionEdit.interactionSerial;
	}

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
			interactionEdit.interactionEditor.wysiwyg('setContent', $("<div/>").html(r.interactionData).html());
			
			//then add listener
			interactionEdit.bindChoiceLinkListener();
			
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
			interactionEdit.initToggleChoiceOptions();//{'delete':false}
			$newFormElt.show();
			
			interactionEdit.setFormChangeListener('#'+r.choiceSerial);
						
			//rebuild the response grid:
			responseEdit.buildGrid(qtiEdit.responseGrid, interactionEdit.interactionSerial);
	   }
	});
}

interactionEdit.addGap = function(interactionData, interactionSerial, $appendTo){

	if(!interactionSerial){
		var interactionSerial = interactionEdit.interactionSerial;
	}else{
		interactionEdit.interactionSerial = interactionSerial;
	}

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
			interactionEdit.interactionEditor.wysiwyg('setContent', $("<div/>").html(r.interactionData).html());
			
			//then add listener
			interactionEdit.bindChoiceLinkListener();//ok keep
			
			//reload choices form
			if(r.reload){
				interactionEdit.loadChoicesForm();
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
			interactionEdit.initToggleChoiceOptions({'delete': false});
			$newFormElt.show();
			
			interactionEdit.setFormChangeListener('#'+r.groupSerial);
						
			//rebuild the response grid:
			responseEdit.buildGrid(qtiEdit.responseGrid, interactionEdit.interactionSerial);
			
			
	   }
	});
}

interactionEdit.buildInteractionEditor = function(interactionDataContainerSelector, extraControls){
	
	//re-init the interaction editor object: 
	interactionEdit.interactionEditor = new Object();
	
	//interaction data container selector:
	interactionEdit.interactionDataContainer = interactionDataContainerSelector;
	
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
				interactionEdit.saveInteractionData();
			},
			tooltip: 'save interaction data'
		}
	};
	
	if(extraControls){
		var controls = $.extend(controls, extraControls);
	}
	
	interactionEdit.interactionEditor = $(interactionEdit.interactionDataContainer).wysiwyg({
		controls: controls,
		gridComplete: interactionEdit.bindChoiceLinkListener,
		events: {
			  keyup : function(e){
				if(interactionEdit.getDeletedChoices(true).length > 0){
					if(!confirm('please confirm deletion of the choice(s)')){
						// undo:
						interactionEdit.interactionEditor.wysiwyg('undo');
					}else{
						var deletedChoices = interactionEdit.getDeletedChoices();
						for(var key in deletedChoices){
							//delete choices one by one:
							interactionEdit.deleteChoice(deletedChoices[key]);
						}
					}
					return false;
				}
			  }
		}
	});
	
	//the binding require the modified html data to be ready
	setTimeout(interactionEdit.bindChoiceLinkListener,1000);
}
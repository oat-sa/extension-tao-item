alert('interaction edit loaded');

function interactionClass(interactionSerial, relatedItemSerial, choicesFormContainer){
	
	if(!interactionSerial){ throw 'no interaction serial found';}
	if(!relatedItemSerial){ throw 'no related item serial found';}
	
	this.interactionSerial = interactionSerial;
	this.relatedItemSerial = relatedItemSerial;
	this.choicesFormContainer = choicesFormContainer;
	this.choices = [];
	this.modifiedInteraction = false;
	this.modifiedChoices = [];
	this.orderedChoices = [];
	
	this.initInteractionFormSubmitter();
	
	//always load the mappingForm (show and hide it according to the value of the qtiEdit.responseMappingMode)
	this.loadResponseMappingForm();
	
	//load choices form
	this.loadChoicesForm(this.choicesFormContainer);
}

interactionClass.prototype.initInteractionFormSubmitter = function(){
	var instance = this;
	$(".form-submiter").click(function(){
		
		var $myForm = $(this).parents("form");
		//linearize it and post it:
		if(instance.modifiedInteraction){
			instance.saveInteraction($myForm);
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
	
	var instance = this;
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveInteraction",
	   data: $myForm.serialize()+orderedChoices+interactionData,
	   dataType: 'json',
	   success: function(r){
			if(r.saved){
				createInfoMessage(__('The interaction has been saved'));
				instance.modifiedInteraction = false;
			}
	   }
	});
}

interactionClass.prototype.loadResponseMappingForm = function(){
	var relatedItem = this.getRelatedItem();
	relatedItem.responseMappingMode = true;
	
	if(relatedItem){
		$.ajax({
		   type: "POST",
		   url: "/taoItems/QtiAuthoring/editMappingOptions",
		   data: {
				'interactionSerial': this.interactionSerial
		   },
		   dataType: 'html',
		   success: function(form){
				
				$formContainer = $(relatedItem.responseMappingOptionsFormContainer);
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
	var relatedItem = this.getRelatedItem(true);
	
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
				
				//reload the grid:
				// responseEdit.buildGrid(relatedItem.responseGrid, interactionSerial);
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
				// responseEdit.buildGrid(qtiEdit.responseGrid, postData.interactionSerial);
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
		
		if(options.delete){
			var $deleteElt = $('<span id="delete_'+groupId+'" title="'+__('Delete choice')+'" class="form-group-control ui-icon ui-icon-circle-close"></span>');
			$group.before($deleteElt);
			$deleteElt.css('position', 'relative');
			
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

interactionClass.prototype.setFormChangeListener = function(target){
	
	var interaction = this;
	
	if(!target){
		var target = "form";
	}else{
		if(!$(target).length){
			return false;
		}
	}
	
	$("form").children().change(function(){
		
		var $modifiedForm = $(this).parents('form');
		if($modifiedForm.length){
			var id = $modifiedForm.attr('id');
			if(id.indexOf('ChoiceForm') == 0){
				interaction.modifiedChoices[id] = 'modified';//it is a choice form:
			}else if(id.indexOf('InteractionForm') == 0){
				interaction.modifiedInteraction = true;
			}
		}
	});
	
	return true;
}

interactionClass.prototype.setOrderedChoicesButtons = function(list){
	var interaction = this;
	var total = list.length;
	for(var i=0; i<total; i++){
		$upElt = $('<span id="up_'+list[i]+'" title="'+__('Move Up')+'" class="form-group-control ui-icon ui-icon-circle-triangle-n"></span>');
		
		//get the corresponding group id:
		$("#a_choicePropOptions_"+list[i]).after($upElt);
		$upElt.click(function(){
			var choiceSerial = $(this).attr('id').substr(3);
			interaction.orderedChoices = interaction.switchOrder(interaction.orderedChoices, choiceSerial, 'up');
		});
		
		$downElt = $('<span id="down_'+list[i]+'" title="'+__('Move Down')+'" class="form-group-control ui-icon ui-icon-circle-triangle-s"></span>');
		$upElt.after($downElt);
		$downElt.click(function(){
			var choiceSerial = $(this).attr('id').substr(5);
			interaction.orderedChoices = interaction.switchOrder(interaction.orderedChoices, choiceSerial, 'down');
		});
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
		this.modifiedInteraction = true;
	}else{
		//return the old order
		newOrder = list;
	}
	
	return newOrder;
}
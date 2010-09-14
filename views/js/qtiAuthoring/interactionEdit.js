// alert('interaction edit loaded');

interactionEdit = new Object();
interactionEdit.interactionSerial = '';
interactionEdit.modifiedInteraction = false;
interactionEdit.modifiedChoices = [];
interactionEdit.orderedChoices = [];

interactionEdit.setOrderedChoicesButtons = function(list){
	var total = list.length;
	for(var i=0; i<total; i++){
		$upElt = $('<span id="up_'+list[i]+'" title="'+__('Move Up')+'" class="form-group-control ui-icon ui-icon-circle-triangle-n"></span>');
		
		//get the corresponding group id:
		$("#a_choicePropOptions_"+list[i]).after($upElt);
		$upElt.click(function(){
			var choiceSerial = $(this).attr('id').substr(3);
			interactionEdit.switchOrder(choiceSerial, 'up');
		});
		
		$downElt = $('<span id="down_'+list[i]+'" title="'+__('Move Down')+'" class="form-group-control ui-icon ui-icon-circle-triangle-s"></span>');
		$upElt.after($downElt);
		$downElt.click(function(){
			var choiceSerial = $(this).attr('id').substr(5);
			interactionEdit.switchOrder(choiceSerial, 'down');
		});
	}
}

interactionEdit.switchOrder = function(choiceId, direction){
	
	var currentPosition = 0;
	for(var i=0; i<interactionEdit.orderedChoices.length; i++){
		if(interactionEdit.orderedChoices[i] == choiceId){
			currentPosition = i;
			break;
		}
	}
			
	switch(direction){
		case 'up':{
			//get the previous choice:
			if(currentPosition>0){
				$('#'+choiceId).insertBefore('#'+interactionEdit.orderedChoices[currentPosition-1]);
				// $('#'+choiceId).remove();
				var newOrder = [];
				for(var i=0;i<interactionEdit.orderedChoices.length;i++){
					if(i == currentPosition-1){
						newOrder[i] = interactionEdit.orderedChoices[i+1];
					}else if(i == currentPosition){
						newOrder[i] = interactionEdit.orderedChoices[i-1];
					}else{
						newOrder[i] = interactionEdit.orderedChoices[i];
					}
				}
				interactionEdit.orderedChoices = newOrder;
			}
			break;
		}
		case 'down':{
			//get the previous choice:
			if(currentPosition<interactionEdit.orderedChoices.length){
				$('#'+choiceId).insertAfter('#'+interactionEdit.orderedChoices[currentPosition+1]);
				// $('#'+choiceId).remove();
				var newOrder = [];
				for(var i=0;i<interactionEdit.orderedChoices.length;i++){
					if(i == currentPosition){
						newOrder[i] = interactionEdit.orderedChoices[i+1];
					}else if(i == currentPosition+1){
						newOrder[i] = interactionEdit.orderedChoices[i-1];
					}else{
						newOrder[i] = interactionEdit.orderedChoices[i];
					}
				}
				interactionEdit.orderedChoices = newOrder;
			}
			break;
		}
	}
	
	//indicates that the interaction has changed:
	interactionEdit.modifiedInteraction = true;
}

interactionEdit.sortOrderedChoices = function(list, order){
	
}

interactionEdit.toggleChoiceOptions = function($group){
	var groupId = $group.attr('id');
	if(groupId.indexOf('choicePropOptions') == 0){
		
		// it is a choice group:
		if($('#a_'+groupId).length){
			$('#a_'+groupId).remove();
		}
		if($('#delete_'+groupId).length){
			$('#delete_'+groupId).remove();
		}
		
		var $deleteElt = $('<span id="delete_'+groupId+'" title="'+__('Delete choice')+'" class="form-group-control ui-icon ui-icon-circle-close"></span>');
		$group.before($deleteElt);
		$deleteElt.css('position', 'relative');
		// deleteElt.css('left',0);
		
		var $buttonElt = $('<span id="a_'+groupId+'" title="'+__('Advanced options')+'" class="form-group-control ui-icon ui-icon-circle-plus"></span>');
		$group.before($buttonElt);
		
		// var $buttonElt = $('<span id="a_'+groupId+'" title="'+__('Advanced options')+'" class="form-group-control ui-icon ui-icon-circle-plus"></span>');
		// $group.before($buttonElt);
		
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
		
		$('#delete_'+groupId).click(function(){
			if(confirm('Do you want to delete the choice?')){
				var choiceSerial = $(this).attr('id').replace('delete_choicePropOptions_', '');
				// CL('deleting the choice '+choiceSerial);
				interactionEdit.deleteChoice(choiceSerial);
			}
		});
		
	}
}

interactionEdit.initToggleChoiceOptions = function(){
	$('.form-group').each(function(){
		interactionEdit.toggleChoiceOptions($(this));
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
	for(var i=0;i<interactionEdit.orderedChoices.length;i++){
		orderedChoices += '&choiceOrder['+i+']='+interactionEdit.orderedChoices[i];
	}
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveInteraction",
	   data: $myForm.serialize()+orderedChoices,
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
				responseEdit.buildGrid(qtiEdit.responseGrid, interactionEdit.interactionSerial);
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
		CL('changed', $(this).parents('form').attr('id'));
		var $modifiedForm = $(this).parents('form');
		if($modifiedForm.length){
			var id = $modifiedForm.attr('id');
			if(id.indexOf('ChoiceForm') == 0){
				//it is a choice form:
				interactionEdit.modifiedChoices[id] = 'modified';
			}else if(id.indexOf('InteractionForm') == 0){
				interactionEdit.modifiedInteraction = true;
			}
		}
	});
	
	return true;
}

interactionEdit.addChoice = function(interactionSerial, $appendTo, containerClass){
	
	if(!$appendTo || !$appendTo.length){
		throw 'the append target element do not exists';
	}
	
	if(!interactionSerial && interactionEdit.interactionSerial){
		var interactionSerial = interactionEdit.interactionSerial;
	}
	
	
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/addChoice",
	   data: {
			'interactionSerial': interactionSerial
	   },
	   dataType: 'json',
	   success: function(r){
			CL('choice added');
			if(r.added){
				var newFormElt = $('<div/>');
				newFormElt.attr('id', r.choiceSerial);
				newFormElt.attr('class', containerClass);
				newFormElt.append(r.choiceForm);
				$appendTo.append(newFormElt);
				
				newFormElt.hide();
				interactionEdit.initToggleChoiceOptions();
				newFormElt.show();
				
				interactionEdit.setFormChangeListener('#'+r.choiceSerial);
				
				//add to the local choices order array:
				interactionEdit.orderedChoices.push(r.choiceSerial);
				
				//rebuild the response grid:
				responseEdit.buildGrid(qtiEdit.responseGrid, interactionEdit.interactionSerial);
			}
	   }
	});
}

interactionEdit.deleteChoice = function(choiceSerial){
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/deleteChoice",
	   data: {
			'choiceSerial': choiceSerial,
			'interactionSerial': interactionEdit.interactionSerial
	   },
	   dataType: 'json',
	   success: function(r){
			if(r.deleted){
				$('#'+choiceSerial).remove();
				//TODO: need to be optimized: only after the last choice saving
				responseEdit.buildGrid(qtiEdit.responseGrid, interactionEdit.interactionSerial);
			}
	   }
	});
}
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
			CL('moving up '+choiceSerial);
		});
		$upElt.click();
	}
}

interactionEdit.orderChoices = function(list){
	
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
	$.ajax({
	   type: "POST",
	   url: "/taoItems/QtiAuthoring/saveInteraction",
	   data: $myForm.serialize(),
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
			}
	   }
	});
}
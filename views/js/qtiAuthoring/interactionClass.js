//alert('interaction edit loaded');
interactionClass.instances = [];

function interactionClass(interactionSerial, relatedItemSerial, options){
	if(!interactionSerial){throw 'no interaction serial found';}
	if(!relatedItemSerial){throw 'no related item serial found';}

	var defaultResponseFormContainer = {
		//responseMappingOptionsFormContainer : '#qtiAuthoring_mapping_container',
		responseGrid: 'qtiAuthoring_response_grid'
	};

	var settings = {};

	$.extend(settings, defaultResponseFormContainer, options);
	this.settings = settings;
	//this.responseMappingOptionsFormContainer = settings.responseMappingOptionsFormContainer;
	this.responseGrid = settings.responseGrid;

	this.interactionSerial = interactionSerial;
	this.relatedItemSerial = relatedItemSerial;

	//var responseMappingMode = false;//default:false
	//this.setResponseMappingMode(responseMappingMode);
	this.choiceAutoSave = true;
	
	this.type = 'unknown';
	this.choices = [];
	this.modifiedInteraction = false;
	this.modifiedChoices = [];
	this.modifiedGroups = [];
	this.orderedChoices = [];

	this.initInteractionFormSubmitter();

	var __this = this;
	var initChoicesAndResponse = function(){
		//always load the mappingForm (show and hide it according to the value of the qtiEdit.responseMappingMode) deprecated!!!
		// this.loadResponseMappingForm();

		//load choices form if necessary:
		if (settings.choicesFormContainer) {
			__this.choicesFormContainer = settings.choicesFormContainer;
			__this.loadChoicesForm(__this.choicesFormContainer);
		} else {
			//immediately set the form change listener (no need to wait for the choice forms)
			__this.setFormChangeListener();

			//and load the response form and grid:
			require([root_url  + 'taoItems/views/js/qtiAuthoring/responseClass.js'], function(responseClass) {
				new responseClass(__this.responseGrid, __this);
			});
		}
	};

	if(settings.shapeEditorOptions && settings.shapeEditorOptions.backgroundImagePath){
		var backgroundImagePath = settings.shapeEditorOptions['backgroundImagePath'];
		delete settings.shapeEditorOptions['backgroundImagePath'];
		settings.shapeEditorOptions.onReady = function(){
			initChoicesAndResponse();
		};
		this.buildShapeEditor(backgroundImagePath, settings.shapeEditorOptions);
	}else{
		initChoicesAndResponse();
	}

	interactionClass.instances[interactionSerial] = this;
	this.getRelatedItem().currentInteraction = this;
}

interactionClass.prototype.setType = function(type){
	this.type = type.toLowerCase();
}

interactionClass.prototype.initInteractionFormSubmitter = function(){
	var _this = this;
	var $myForm = null;
	$(".interaction-form-submitter").click(function(){

		$myForm = $(this).parents("form");
		//linearize it and post it:
		// if(_this.modifiedInteraction){
			_this.saveInteraction($myForm);
		// }

		_this.saveModifiedChoices();

		return false;
	});

	$('#qtiAuthoring_item_editor_button').unbind('click').bind('click', function(){

		var self = this;
		var switchTabFunction = function(){
			if($myTab) $myTab.tabs("select" , 0);
			$(self).hide();
			$('#qtiAuthoring_menu_interactions').show();
		}

		var relatedItem = _this.getRelatedItem();
		if(relatedItem){
			relatedItem.currentInteraction = null;
			if(_this.modifiedInteraction){
				if(confirm(__('The current interaction has been modified but the modifications has not been updated yet.\nDo you want to update the interaction with the modifications before returning to the item editor?\n(Otherwise, the modifications are lost)'))){
					relatedItem.saveCurrentInteraction(switchTabFunction);
					return;
				}
			}
		}

		switchTabFunction();

	}).show();

	$('#qtiAuthoring_menu_interactions').hide();
}

interactionClass.prototype.saveModifiedChoices = function(){
	for (var groupSerial in this.modifiedGroups) {
		var $groupForm = $('#'+groupSerial);

		//serialize+submit:
		if ($groupForm.length) {
			this.saveGroup($groupForm);
		}
	}

	for (var choiceSerial in this.modifiedChoices) {
		var $choiceForm = $('#'+choiceSerial);

		//serialize+submit:
		if ($choiceForm.length) {
			this.saveChoice($choiceForm);
		}
	}
}



interactionClass.prototype.saveInteraction = function($myForm, option){
	//TODO: check unicity of the id:
	if($myForm.length){

		var interactionProperties = $myForm.serializeObject();

		//filter the prompt html area field:
		if(interactionProperties.prompt){
			interactionProperties.prompt = util.htmlEncode(interactionProperties.prompt);
		}

		//serialize the order:
		var orderedChoices = '';
		if(this.orderedChoices[0]){
			interactionProperties.choiceOrder = [];
			for(var i=0;i<this.orderedChoices.length;i++){
				interactionProperties.choiceOrder[i] = this.orderedChoices[i];
			}
		}else{
			//for match and gapmatch interaction:
			var i = 0;
			for(var groupSerial in this.orderedChoices){
				interactionProperties['choiceOrder'+i] = [];
				interactionProperties['choiceOrder'+i]['groupSerial'] = groupSerial;
				for(var j=0; j<this.orderedChoices[groupSerial].length; j++){
					interactionProperties['choiceOrder'+i][j] = this.orderedChoices[groupSerial][j];
				}
				i++;
			}
		}

		//check if it is required to save data (hotText and gapMatch interactions):
		if(this.interactionDataContainer){
			if($(this.interactionDataContainer).length && this.interactionEditor.length){
				//there is a wysiwyg editor that contains the interaciton data:
				interactionProperties.interactionData = util.htmlEncode(this.interactionEditor.wysiwyg('getContent'));
			}
		}

		var async = true;
		if(option){
			if(option.async === false){
				async = false;
			}
		}

		var interaction = this;

		$.ajax({
		   type: "POST",
		   url: root_url + "/taoItems/QtiAuthoring/saveInteraction",
		   data: interactionProperties,
		   // async: async,
		   dataType: 'json',
		   success: function(r){
				if(r.saved){
					qtiEdit.createInfoMessage(__('Modification(s) on interaction has been updated'));
					interaction.setModifiedInteraction(false);

					if (r.reloadResponse) {
						require([root_url  + 'taoItems/views/js/qtiAuthoring/responseClass.js'], function(responseClass) {
							new responseClass(interaction.responseGrid, interaction);
						});
					}

					if(r.newGraphicObject){
						if(r.newGraphicObject.data){
							if(interaction.shapeEditor){
								interaction.shapeEditor.setBackground(r.newGraphicObject.data, r.newGraphicObject.width, r.newGraphicObject.height);
							}else{
								interaction.buildShapeEditor(r.newGraphicObject.data, {width: r.newGraphicObject.width, height:r.newGraphicObject.height});
								interaction.setShapeEditListener();
							}
						}else{
							if(r.newGraphicObject.errorMessage){
								helpers.createErrorMessage(__('Error in background file:')+'<br/>'+r.newGraphicObject.errorMessage);
							}
						}
					}

				}
		    }
		});
	}

}

interactionClass.prototype.saveChoice = function($choiceFormContainer){

	var _this = this;
	var $choiceForm = null;

	if($choiceFormContainer.length){

		if($choiceFormContainer[0].nodeName.toLowerCase() == 'form'){
			$choiceForm = $choiceFormContainer;
		}else{
			$choiceForm = $choiceFormContainer.find('form');
		}
	}

	if($choiceForm){
		if($choiceForm.length){

			var choiceProperties = $choiceForm.serializeObject();
			if(choiceProperties.data){
				var choiceData = choiceProperties.data;//keep un altered data string
				choiceProperties.data = util.htmlEncode(choiceProperties.data);
			}

			var interaction = this;
			$.ajax({
			   type: "POST",
			   url: root_url + "/taoItems/QtiAuthoring/saveChoice",
			   data: choiceProperties,
			   dataType: 'json',
			   success: function(r){

					if(!r.saved){
						helpers.createErrorMessage(__('The choice cannot be saved'));
					}else{
						qtiEdit.createInfoMessage(__('Modification on choice applied'));
						delete interaction.modifiedChoices['ChoiceForm_'+r.choiceSerial];

						//only if it is the last updated choice!
						if (!interaction.modifiedChoices.length && !interaction.modifiedGroups.length) {
							//only when the identifier has changed:
							if (r.reload) {
								interaction.loadChoicesForm();
							} else if (r.identifierUpdated) {
								require([root_url  + 'taoItems/views/js/qtiAuthoring/responseClass.js'], function(responseClass) {
									//reload the response grid tu update the identifier
									new responseClass(interaction.responseGrid, interaction);
								});
							}
						}

						//for hotspot interaction, update the input value:
						if(_this.interactionEditor && choiceData){
							var $choiceInputs = qtiEdit.getEltInFrame('#'+choiceProperties.choiceSerial);
							if($choiceInputs.length){
								$choiceInputs[0].val(choiceData);
							}
						}

						if(r.errorMessage){
							helpers.createErrorMessage(r.errorMessage);
						}
					}
				}
			});

		}
	}


}

interactionClass.prototype.saveGroup = function($groupForm){
	if ($groupForm) {
		var interaction = this;
		//save group order?

		var choiceOrder = ''
		var i = 0;
		for (var order in this.orderedChoices) {
			choiceOrder += '&choiceOrder['+i+']='+this.orderedChoices[order];
			i++;
		}

		$.ajax({
		   type: "POST",
		   url: root_url + "/taoItems/QtiAuthoring/saveGroup",
		   data: $groupForm.serialize()+choiceOrder,
		   dataType: 'json',
		   success: function(r){
				if (!r.saved) {
					helpers.createErrorMessage(__('The choice cannot be saved'));
				} else {
					qtiEdit.createInfoMessage(__('Modification on choice applied'));
					delete interaction.modifiedGroups['GroupForm_'+r.groupSerial];

					//only when the identifier has changed:
					if (r.reload) {
						interaction.loadChoicesForm();

						//for gap match interaction:
						if (r.identifierUpdated && r.newIdentifier && interaction.interactionEditor) {
							$groupInputs = qtiEdit.getEltInFrame('#'+r.groupSerial);
							if ($groupInputs.length) {
								$groupInputs[0].val(r.newIdentifier);
							}
						}
					} else if(r.identifierUpdated) {
						require([root_url  + 'taoItems/views/js/qtiAuthoring/responseClass.js'], function(responseClass) {
							new responseClass(interaction.responseGrid, interaction);
						});
					}
				}
			}
		});
	}
}

interactionClass.prototype.loadResponseOptionsForm = function(){
	var relatedItem = this.getRelatedItem();
	var interaction = this;

	if(relatedItem){
		$.ajax({
		   type: "POST",
		   url: root_url + "/taoItems/QtiAuthoring/editOptions",
		   data: {
				'interactionSerial': this.interactionSerial
		   },
		   dataType: 'html',
		   success: function(form){

				var $formContainer = $(interaction.responseOptionsFormContainer);
				// TODO: change by call of function setResponseOptionsMode()
				$formContainer.html(form);
				if(interaction.responseOptionsMode){
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
	if (!containerSelector) {
		var containerSelector = '';
		if (this.choicesFormContainer) {
			var containerSelector = this.choicesFormContainer;
		}
	}
	var interactionSerial = this.interactionSerial;
	var interaction = this;

	if ($(containerSelector).length) {
		$.ajax({
		   type: "POST",
		   url: root_url + "/taoItems/QtiAuthoring/editChoices",
		   data: {
				'interactionSerial': interactionSerial
		   },
		   dataType: 'html',
		   success: function(form){
				var $formContainer = $(containerSelector);
				$formContainer.html(form);
				
				//populate choices local model:
				//@TODO: replace the form based data filing by a clean data model
				$formContainer.find('input#choiceSerial').each(function(){
					var serial = $(this).val();
					interaction.choices[serial] = {};
					
					$(this).siblings('div').each(function(){
						
						var choiceIdElt = $(this).find('input#choiceIdentifier');
						if(choiceIdElt.length){
							interaction.setInteractionChoiceAttribute(serial, 'identifier', $(choiceIdElt[0]).val());
						}
						
						var choiceShapeElt = $(this).find('select#shape');
						if(choiceShapeElt.length){
							var shape = $(choiceShapeElt[0]).val();
							
							interaction.setInteractionChoiceAttribute(serial, 'shape', shape);
							
							$(choiceShapeElt[0]).siblings('img.shapeEditorPencil').each(function(){
								if(shape == 'default'){
									$(this).hide();
								}else{
									$(this).show();
								}
							});
						}
					});
					
				});
				
				qtiEdit.initFormElements($formContainer);
				interaction.setFormChangeListener();
				interaction.setShapeEditListener();
				
				require([root_url  + 'taoItems/views/js/qtiAuthoring/responseClass.js'], function(responseClass) {
					//reload the grid:
					new responseClass(interaction.responseGrid, interaction);
				});
		   }
		});
	}

}

interactionClass.prototype.getInteractionChoiceAttribute = function(choiceSerial, attribute){
	var returnValue = null;
	if(this.choices[choiceSerial] && this.choices[choiceSerial][attribute]){
		returnValue = this.choices[choiceSerial][attribute];
	}
	return returnValue;
}

interactionClass.prototype.setInteractionChoiceAttribute = function(choiceSerial, attribute, value){
	var returnValue = false;
	if(this.choices[choiceSerial]){
		this.choices[choiceSerial][attribute] = value;
		returnValue = true;
	}
	return returnValue;
}

interactionClass.prototype.setShapeEditListener = function(target){

	if(this.shapeEditor){

		var interaction = this;

		//define the function:
		var setListener = function($choiceForm){

			if($choiceForm.length){
				var choiceSerial = $choiceForm.attr('id');
				choiceSerial = choiceSerial.replace('GroupForm_', '');
				choiceSerial = choiceSerial.replace('ChoiceForm_', '');

				var $qtiShapeCombobox = $choiceForm.find('.qti-shape').bind('change', {choiceSerial:choiceSerial}, function(e){
					
					var cSerial = e.data.choiceSerial;
					var newShape = $(this).val();
					var oldShape = interaction.getInteractionChoiceAttribute(cSerial, 'shape');
					
					if(newShape != oldShape){
						var redrawShape = false;
						if(oldShape == 'default'){
							redrawShape = true;
						}else{
							if(confirm(__('Changing shape type will delete the old shape, are you sure?'))){
								redrawShape = true;
							}else{
								//restore shape and interaction modification :
								$(this).val(oldShape);
							}
						}
					
						if(redrawShape){
							interaction.shapeEditor.removeShapeObj(cSerial);
							interaction.shapeEditor.startDrawing(cSerial, newShape);
							interaction.setInteractionChoiceAttribute(cSerial, 'shape', newShape);
						}
					}
					
					$(this).siblings('img.shapeEditorPencil').each(function(){
						if(interaction.getInteractionChoiceAttribute(cSerial, 'shape') == 'default'){
							$(this).hide();
						}else{
							$(this).show();
						}
					});
				});

				//append the edit button:
				var $imageLink = $('<img src="'+img_url+'pencil.png"/>').insertAfter($qtiShapeCombobox);
				$imageLink.attr('title', __('draw it'));
				$imageLink.addClass('shapeEditorPencil');
				$imageLink.bind('click', {choiceSerial:choiceSerial, defaultShape:$qtiShapeCombobox.val()}, function(e){
					var shape = $(this).siblings('.qti-shape').val();
					interaction.shapeEditor.startDrawing(e.data.choiceSerial, shape);
				});

				//check if the coords are not empty, if so, draw the shape:
				var $qtiCoordsInput = $choiceForm.find('input[name=coords]');
				if($qtiCoordsInput.length){
					if($qtiCoordsInput.val() && $qtiShapeCombobox.val()){
						interaction.shapeEditor.createShape(choiceSerial, 'qti', {data:$qtiCoordsInput.val(), shape:$qtiShapeCombobox.val()});
						interaction.shapeEditor.exportShapeToCanvas(choiceSerial);
					}
				}

				//finally add the hover/blur control:
				$choiceForm.parents('div.formContainer_choice').hover(function(){
					// var choiceSerial = $(this).attr('id');
					interaction.shapeEditor.hoverIn($(this).attr('id'));
				},function(){
					interaction.shapeEditor.hoverOut($(this).attr('id'));
				});
			}

		}

		if(!target){
			//all choices:
			$('div.formContainer_choice').find('form').each(function(){
				setListener($(this));
			});
		}else{
			if(true){
				setListener($(target));
			}
		}
	}

}


interactionClass.prototype.addChoice = function(number, $appendTo, containerClass, groupSerial){

	var interaction = this;

	if(!$appendTo || !$appendTo.length){
		throw 'the append target element do not exists';
	}

	var addChoice = function($appendTo, containerClass, groupSerial){

		var postData = {};
		postData.interactionSerial = interaction.interactionSerial;

		if(groupSerial){
			postData.groupSerial = groupSerial;
		}

		$.ajax({
		   type: "POST",
		   url: root_url + "/taoItems/QtiAuthoring/addChoice",
		   data: postData,
			 async: false,
		   dataType: 'json',
		   success: function(r){
				if (r.added) {
					if (r.reload) {
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

					qtiEdit.initFormElements($newFormElt);
					interaction.setFormChangeListener('#'+r.choiceSerial);
					interaction.setShapeEditListener('#'+r.choiceSerial);

					//add to the local choices order array:
					//if interaction type is match, save the new choice in one of the group array:
					if (r.groupSerial) {
						if (interaction.orderedChoices[r.groupSerial]) {
							interaction.orderedChoices[r.groupSerial].push(r.choiceSerial);
						} else {
							throw 'the group serial is not defined in the ordered choices array';
						}
					} else {
						interaction.orderedChoices.push(r.choiceSerial);
					}

					require([root_url  + 'taoItems/views/js/qtiAuthoring/responseClass.js'], function(responseClass) {
						//rebuild the response grid:
						new responseClass(interaction.responseGrid, interaction);
					});
				}
		   }
		});
	}

	if(this.choiceAutoSave){
		this.saveModifiedChoices();

		var timer = null;
		var stopTimer = function(){
			if(!number){
				number = 1;
			}
			clearTimeout(timer);
			for(var i=0;i<number;i++){
				addChoice($appendTo, containerClass, groupSerial);
			}
		}
		//check every half a second if all choices have been saved:
		timer = setTimeout(function(){
			if(!interaction.modifiedChoices.length && !interaction.modifiedGroups.length){
				stopTimer();
			}
		}, 500);

	}else{
		addChoice($appendTo, containerClass, groupSerial);
	}

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

		if(options['delete']){
			var $deleteElt = $('<span id="delete_'+groupId+'" title="'+__('Delete choice')+'" class="form-group-control choice-button-delete ui-icon ui-icon-circle-close"></span>');
			$group.before($deleteElt);
			// $deleteElt.css('position', 'relative');

			//add click event listener:
			$('#delete_'+groupId).click(function(){
				if(confirm('Do you want to delete the choice?')){
					var choiceSerial = $(this).attr('id').replace('delete_choicePropOptions_', '');
					interaction.deleteChoice(choiceSerial);
				}
			});
		}

		if(options.group){
			
			var $buttonElt = $('<span id="a_'+groupId+'" title="'+__('Advanced options')+'" class="form-group-control choice-button-advanced ui-icon ui-icon-circle-plus"></span>');
			$group.before($buttonElt);
			$group.hide();

			$('#a_'+groupId).toggle(function(){
				$(this).switchClass('ui-icon-circle-plus', 'ui-icon-circle-minus');
				$group.slideToggle();
			},function(){
				$(this).switchClass('ui-icon-circle-minus', 'ui-icon-circle-plus');
				$group.slideToggle();
			});
		}

	}
}

interactionClass.prototype.setModifiedChoicesByForm = function($modifiedForm){
	if($modifiedForm.length){
		var id = $modifiedForm.attr('id');
		if(id.indexOf('ChoiceForm') == 0){
			this.modifiedChoices[id] = 'modified';//it is a choice form:
			this.setModifiedInteraction(true);//the interaction must be correctly saved
		}else if(id.indexOf('InteractionForm') == 0){
			this.setModifiedInteraction(true);
		}else if(id.indexOf('GroupForm') == 0){
			this.modifiedGroups[id] = 'modified';
			this.setModifiedInteraction(true);
		}
	}
}

//define when the form is considered as changed:
interactionClass.prototype.setFormChangeListener = function(target){
	var interaction = this;
	var $choiceForm = null;

	if (!target) {
		$choiceForm = $('form');//all forms
	} else {
		if (!$(target).length) {
			return false;
		} else {
			var $choiceFormContainer = $(target);
			if ($choiceFormContainer[0].nodeName.toLowerCase() == 'form') {
				$choiceForm = $choiceFormContainer;
			} else {
				$choiceForm = $choiceFormContainer.find('form');
			}
		}
	}

	$choiceForm.children().bind('change paste', function() {
		var $modifiedForm = $(this).parents('form');
		interaction.setModifiedChoicesByForm($modifiedForm);
	});

	$choiceForm.find('iframe').each(function() {
		var $modifiedForm = $(this).parents('form');
		var setChangesfunction = function(){
			interaction.setModifiedChoicesByForm($modifiedForm);
		};

		// contentWindow does not exist under google chrome and other browsers.
		var contentW;
		if (this.contentDocument) {
			contentW = this.contentDocument;
		} else if (this.contentWindow) {
			contentW = this.contentWindow.document;
		} else {
			contentW = this.document;
		}

		$(contentW).bind('click focus', setChangesfunction); //focusin
		$(this).siblings('ul').click(setChangesfunction);
	});

	return true;
}

interactionClass.prototype.setOrderedChoicesButtons = function(list){
	 return false;//deactivate it for now

	var interaction = this;
	var total = list.length;
	for(var i=0; i<total; i++){
		var $upElt = $('<span id="up_'+list[i]+'" title="'+__('Move Up')+'" class="form-group-control choice-button-up ui-icon ui-icon-circle-triangle-n"></span>');

		//get the corresponding group id:
		$("#delete_choicePropOptions_"+list[i]).after($upElt);
		$upElt.click(function(){
			var choiceSerial = $(this).attr('id').substr(3);
			interaction.orderedChoices = interaction.switchOrder(interaction.orderedChoices, choiceSerial, 'up');
		});

		var $downElt = $('<span id="down_'+list[i]+'" title="'+__('Move Down')+'" class="form-group-control choice-button-down ui-icon ui-icon-circle-triangle-s"></span>');
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

			var $upElt = $('<span id="up_'+list[i]+'" title="'+__('Move Up')+'" class="form-group-control choice-button-up ui-icon ui-icon-circle-triangle-n"></span>');

			//get the corresponding group id:
			$("#a_choicePropOptions_"+list[i]).after($upElt);
			$upElt.bind('click', {'groupSerial':groupSerial}, function(e){
				var choiceSerial = $(this).attr('id').substr(3);
				interaction.orderedChoices[e.data.groupSerial] = interaction.switchOrder(interaction.orderedChoices[e.data.groupSerial], choiceSerial, 'up');
			});

			var $downElt = $('<span id="down_'+list[i]+'" title="'+__('Move Down')+'" class="form-group-control choice-button-down ui-icon ui-icon-circle-triangle-s"></span>');
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
		this.setModifiedInteraction(true);
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
	var oldChoice = this.choices[choiceSerial];
	delete this.choices[choiceSerial];

	var newOrderedChoices = [];
	var j = 0;
	for(var i=0; i<this.orderedChoices.length; i++){
		if(this.orderedChoices[i] != choiceSerial){
				newOrderedChoices[j++] = this.orderedChoices[i];
		}
	}
	this.orderedChoices = newOrderedChoices;

	if(!reloadInteraction) var reloadInteraction = false;

	$.ajax({
	   type: "POST",
	   url: root_url + "/taoItems/QtiAuthoring/deleteChoice",
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

				//delete the shape, if exists:
				if(interaction.shapeEditor){
					interaction.shapeEditor.removeShapeObj(choiceSerial);
				}

				require([root_url  + 'taoItems/views/js/qtiAuthoring/responseClass.js'], function(responseClass) {
					//TODO: need to be optimized: only after the last choice saving
					new responseClass(interaction.responseGrid, interaction);
					interaction.saveInteractionData();
				});
			}else{
				interaction.choices[choiceSerial] = oldChoice;
				//interaction.orderedChoices[choiceSerial] = choiceSerial;
			}
	   }
	});
}
/*
interactionClass.prototype.saveResponseOptions = function($myForm){

	var self = this;

	$.ajax({
	   type: "POST",
	   url: root_url + "/taoItems/QtiAuthoring/saveOptions",
	   data: $myForm.serialize(),
	   dataType: 'json',
	   success: function(r){
			if(r.saved){
				qtiEdit.createInfoMessage(__('The options have been updated'));
			}
	   }
	});
}*/

interactionClass.prototype.buildInteractionEditor = function(interactionDataContainerSelector, extraControls, options){

	//re-init the interaction editor object:
	this.interactionEditor = new Object();

	//interaction data container selector:
	this.interactionDataContainer = interactionDataContainerSelector;

	var interaction = this;
	var controls = {
	  strikeThrough : {visible : true},
	  underline     : {visible : false},
	  insertTable 	: {visible : false},

	  h1 : {visible : true},
	  h2 : {visible : true},
	  h3 : {visible : true},

	  justifyLeft   : {visible : true},
	  justifyCenter : {visible : true},
	  justifyRight  : {visible : true},
	  justifyFull   : {visible : true},

	  indent  : {visible : true},
	  outdent : {visible : true},

	  subscript   : {visible : true},
	  superscript : {visible : true},

	  undo : {visible : true},
	  redo : {visible : true},

	  insertOrderedList    : {visible : true},
	  insertUnorderedList  : {visible : true},
	  insertHorizontalRule : {visible : true},

	  cut   : {visible : true},
	  copy  : {visible : true},
	  paste : {visible : true},
	  html  : {visible: true},

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

	  saveInteractionData: {
			visible : false,//currently deactivated
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

	if(!options){
		options = {
			css: ''
		}
	}

	this.interactionEditor = $(this.interactionDataContainer).wysiwyg({
		iFrameClass: 'wysiwyg-interaction',
		css: options.css,
		controls: controls,
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
			},
			frameReady: function(editorDoc){
				interaction.bindChoiceLinkListener();
				editorDoc.body.focus();
			}
		},
		frameReady: function(editorDoc){

		}
	});

}


interactionClass.prototype.buildShapeEditor = function(backgroundImagePath, options){

	var interaction = this;

	var defaultOptions = {
		onDrawn: function(choiceSerial, shapeObject, self){
			//export shapeObject to qti:
			if(choiceSerial && shapeObject){
				var qtiCoords = self.exportShapeToQti(choiceSerial);
				if(qtiCoords){
					// $('#ChoiceForm_'+choiceSerial).find('input[name=coords]').val(qtiCoords);
					// interaction.modifiedChoices[choiceSerial] = 'modified';//it is a choice form:

					var $choiceGroupForm = $('#'+choiceSerial).find('form');
					if($choiceGroupForm.length){
						$choiceGroupForm.find('input[name=coords]').val(qtiCoords);
						interaction.setModifiedChoicesByForm($choiceGroupForm);
					}else{
						//throw 'no choice or group form found';
					}

				}
			}
		}
	};

	var shapeEditorOption = $.extend(defaultOptions, options);

	var myShapeEditor = new qtiShapesEditClass(
		'formInteraction_object',
		backgroundImagePath,
		shapeEditorOption
	);

	if(myShapeEditor){
		//map choices to the shape editor:
		this.shapeEditor = myShapeEditor;

	}
}


interactionClass.prototype.saveInteractionData = function(){

	if(this.interactionDataContainer){
		if($(this.interactionDataContainer).length && this.interactionEditor.length){
			//save data if and only if the data content exists
			$.ajax({
			   type: "POST",
			   url: root_url + "/taoItems/QtiAuthoring/saveInteractionData",
			   data: {
					'interactionData': util.htmlEncode(this.interactionEditor.wysiwyg('getContent')),
					'interactionSerial': this.interactionSerial
			   },
			   dataType: 'json',
			   success: function(r){
					qtiEdit.createInfoMessage(__('Interaction data saved.'));
			   }
			});

			return true;
		}
	}

	return false;
}

/*
*Method used to check if a choice has been deleted in the interaction wysiwyg data editor (for gapmatch and hottext only)
*/
interactionClass.prototype.getDeletedChoices = function(one){
	var deletedChoices = [];
	var interactionData = $(this.interactionDataContainer).val();//TODO: improve with the use of regular expressions:
	for(var choiceSerial in this.choices){
		
		var doCheck = false;
		if(this.type == 'gapmatch' && choiceSerial.indexOf('group') == 0 ){
			doCheck = true;
		}else if(this.type == 'hottext'){
			doCheck = true;
		}else{
			break;
		}
		
		if(doCheck && interactionData.indexOf(choiceSerial)<0){
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
		url: root_url + "/taoItems/QtiAuthoring/addHotText",
		data: {
			'interactionSerial': interactionSerial,
			'interactionData': util.htmlEncode(interaction.interactionEditor.wysiwyg('getContent'))
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
			if (!$appendTo) {
				var $appendTo = $('#formContainer_choices');
			}
			$appendTo.append($newFormElt);

			$newFormElt.hide();
			interaction.initToggleChoiceOptions();//{'delete':false}
			$newFormElt.show();

			interaction.setFormChangeListener('#'+r.choiceSerial);

			//rebuild the response grid:
			require([root_url  + 'taoItems/views/js/qtiAuthoring/responseClass.js'], function(responseClass) {
				new responseClass(interaction.responseGrid, interaction);
			});
		}
	});
}

interactionClass.prototype.addGroup = function(number, interactionData, $appendTo){

	var interaction = this;

	var addGroup = function(interactionData, $appendTo){

		$.ajax({
		   type: "POST",
		   url: root_url + "/taoItems/QtiAuthoring/addGroup",
		   data: {
				'interactionSerial': interaction.interactionSerial,
				'interactionData': interactionData
		   },
		   dataType: 'json',
		   success: function(r){

				if(interaction.interactionEditor){//gapmatch interaction only
					//set the content:
					interaction.interactionEditor.wysiwyg('setContent', $("<div/>").html(r.interactionData).html());

					//then add listener
					interaction.bindChoiceLinkListener();//ok keep
				}

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
				interaction.setShapeEditListener('#'+r.groupSerial);

				require([root_url  + 'taoItems/views/js/qtiAuthoring/responseClass.js'], function(responseClass) {
					//rebuild the response grid:
					new responseClass(interaction.responseGrid, interaction);
				});
		   }
		});

	}

	//get directly interaction data from the editor:
	var interactionData = '';
	if(this.interactionEditor) interactionData = util.htmlEncode(this.interactionEditor.wysiwyg('getContent'));

	if(this.choiceAutoSave){
		this.saveModifiedChoices();

		var timer = null;
		var stopTimer = function(){
			if(!number){
				number = 1;
			}
			for(var i=0;i<number;i++){
				addGroup(interactionData, $appendTo);
			}
			clearTimeout(timer);
		}
		//check every half a second if all choices have been saved:
		timer = setTimeout(function(){
			if(!interaction.modifiedChoices.length && !interaction.modifiedGroups.length){
				stopTimer();
			}
		}, 500);

	}else{
		addGroup(interactionData, $appendTo);
	}
}

interactionClass.prototype.bindChoiceLinkListener = function(){

	//destroy all listeners:

	//reset the choice array:
	this.choices = [];

	var links = qtiEdit.getEltInFrame('.qti_choice_link');
	for(var i in links){

		var choiceSerial = links[i].attr('id');

		this.choices[choiceSerial] = {};

		links[i].unbind('click').click(function(){
			//focus the clicked choice form:
			window.location.hash = '#'+$(this).attr('id');

		});

		qtiEdit.makeNoEditable(links[i]);
		qtiEdit.makeNoEditable(links[i].parent('div'));
		links[i].parent('div').click(function(e){
			e.preventDefault();
		});
	}

}

interactionClass.prototype.setResponseOptionsMode = function(optionsMode){
	this.responseOptionsMode = optionsMode;
	switch (optionsMode) {
		case 'manual':
			break;

		case 'map':
			if (this.responseOptionsMode != 'map') {
				//display the scoring form: //TODO: load it only when necessary:
				//this.responseMappingMode = true;
				//$('#qtiAuthoring_mappingEditor').show();

				require([root_url  + 'taoItems/views/js/qtiAuthoring/responseClass.js'], function(responseClass) {
					//reload the response grid, to update column model:
					new responseClass(this.responseGrid, this);
				});
			}
			break;

		case 'correct':
			//this.responseMappingMode = false;
			//$('#qtiAuthoring_mappingEditor').hide();

			require([root_url  + 'taoItems/views/js/qtiAuthoring/responseClass.js'], function(responseClass) {
				//reload the response grid, to update column model:
				new responseClass(this.responseGrid, this);
			});
			break;
	}
	/*if(isMapping){
		//set the reponse mapping to true:
		if(this.responseMappingMode){
			//nothing to do:
		}else{
			//display the scoring form: //TODO: load it only when necessary:
			this.responseMappingMode = true;
			$('#qtiAuthoring_mappingEditor').show();

			//reload the response grid, to update column model:
			new responseClass(this.responseGrid, this);
		}
	}else{
		this.responseMappingMode = false;
		$('#qtiAuthoring_mappingEditor').hide();

		//reload the response grid, to update column model:
		new responseClass(this.responseGrid, this);
	}*/
}

interactionClass.prototype.setModifiedInteraction = function(modified){
	if(modified){
		this.modifiedInteraction = true;
		$('a.interaction-form-submitter').addClass('form-submitter-emphasis');
	}else{
		this.modifiedInteraction = false;
		$('a.interaction-form-submitter').removeClass('form-submitter-emphasis');
	}
}

interactionClass.prototype.isModified = function(){
	return this.modifiedInteraction;
}
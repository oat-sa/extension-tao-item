// alert('response edit loaded');

//customized unload function:
$.jgrid.GridUnload = function(){
	return this.each(function(){
		if ( !this.grid ) {return;}
		var defgrid = {id: $(this).attr('id'),cl: $(this).attr('class')};
		if (this.p.pager) {
			$(this.p.pager).empty().removeClass("ui-state-default ui-jqgrid-pager corner-bottom");
		}
		var newtable = document.createElement('table');
		$(newtable).attr({id:defgrid.id});
		newtable.className = defgrid.cl;
		var gid = this.id;
		$(newtable).removeClass("ui-jqgrid-btable");
		if( $(this.p.pager).parents("#gbox_"+gid).length === 1 ) {
			$(newtable).insertBefore("#gbox_"+gid).show();
			$(this.p.pager).insertBefore("#gbox_"+gid);
		} else {
			$(newtable).insertBefore("#gbox_"+gid).show();
		}
		$("#gbox_"+gid).remove();
	});
};

responseClass.grid = null;

function responseClass(tableElementId, interaction, responseFormContainer){
	if(responseClass.grid){
		responseClass.grid.destroyGrid();//only one response grid available at a time.
	}

	responseClass.grid = this;
	var response = this;

	this.interactionSerial = interaction.interactionSerial;

	this.currentRowId = -1;
	this.maxChoices = null;
	this.setModifiedResponseProperties(false);

	if(!responseFormContainer) var responseFormContainer = '#qtiAuthoring_response_formContainer';

	if($(responseFormContainer).length){
		this.responseFormContainer = responseFormContainer;

		$.ajax({
			url: root_url + "/taoItems/QtiAuthoring/editResponse",
			type: "POST",
			data: {
				'interactionSerial': this.interactionSerial,
				'itemSerial': interaction.getRelatedItem(true).itemSerial
			},
			dataType: 'json',
			success: function(r){
				if (r.ok){
					//reset the grid:
					$('#'+tableElementId).empty();

					//set the response form if needed:
					//$(response.responseFormContainer).html(r.responseForm);
					$(response.responseFormContainer).html('');
					for (f in r.forms) {
						$(response.responseFormContainer).append(r.forms[f]);
						$('textarea', response.responseFormContainer).autogrow();
					}
					$(response.responseFormContainer + ' .form-toolbar:empty').remove();
					response.initResponseFormSubmitter();
					response.initResponseFormProcessingTypeChange();
					response.setResponseFormChangeListener();

					//set the amximum allowed correct responses, according to the maxChoices attribute defined at the itneraction level.
					if(r.maxChoices) response.maxChoices = r.maxChoices;

					if(r.displayGrid){
						$('#'+tableElementId).prev('span').show();
						try{
							response.buildGrid(tableElementId, r);
						}catch(err){
							// alert('Building response grid exception: '+err);
							CL('Building response grid exception: '+err);
						}
					} else {
						response.myGrid = $("#"+tableElementId); //If not displayed, var never be set, so set it here
						$('#'+tableElementId).prev('span').hide();
					}

					/*if(r.setResponseOptionsMode){
						response.loadResponseOptionsForm();
					}*/

				}else{
					throw 'error in loading the response editing data';
				}
			}
		});
	}
}
/*
responseClass.prototype.loadResponseOptionsForm = function(){
	var interaction = interactionClass.instances[this.interactionSerial];
	var _this = this;
	if(interaction){
		$.ajax({
		   type: "POST",
		   url: root_url + "/taoItems/QtiAuthoring/editResponseOptions",
		   data: {
				'interactionSerial': interaction.interactionSerial
		   },
		   dataType: 'html',
		   success: function(form){
				$responseFormContainer = $('#qtiAuthoring_responseOptionsEditor');

				if(!$('#qtiAuthoring_responseOptionsEditor').length){
					$(_this.responseFormContainer).after(form);
					$('#qtiAuthoring_responseOptionsEditor').find('.form-toolbar').hide();
					interaction.setResponseOptionsMode(true);
					setTimeout(function(){_this.setResponseFormChangeListener();},1000);
				}
		   }
		});
	}
}
*/

responseClass.prototype.initResponseFormSubmitter = function(){
	var self = this;

	$(".response-form-submitter").click(function(event){
		event.preventDefault();
		var result = false;

		$('#qtiAuthoring_response_formContainer form').each(function(i) {
			switch ($(this).attr('name')) {
				case 'InteractionResponseProcessingForm':
					//linearize it and post it:
					$.ajax({
					   type: "POST",
					   url: root_url + "/taoItems/QtiAuthoring/saveInteractionResponseProcessing",
					   data: $(this).serialize(),
					   dataType: 'json',
					   async: false,
					   success: function(r){ //Assume save = changed
						    if (r.saved) qtiEdit.createInfoMessage(__('Modification on response applied'));
								result = r.saved;
					   }
					});
					break;

				case 'Response_Form':
					$.ajax({
					   type: "POST",
					   url: root_url + "/taoItems/QtiAuthoring/saveResponseProperties",
					   data: $(this).serialize(),
					   dataType: 'json',
					   async: false,
					   success: function(r){
								if (r.saved) qtiEdit.createInfoMessage(__('The response properties have been updated'));
					   }
					});
					break;

				case 'ResponseCodingOptionsForm':
					$.ajax({
					   type: "POST",
					   url: root_url + "/taoItems/QtiAuthoring/saveResponseCodingOptions",
					   data: $(this).serialize(),
					   dataType: 'json',
					   async: false,
					   success: function(r){
								if (r.saved) qtiEdit.createInfoMessage(__('The options have been updated'));
								result = r.saved;
					   }
					});
					break;
			}
		});

		if (result) {
			var interaction = interactionClass.instances[self.interactionSerial];
			if (interaction) {
				//reload the grid, just in case the response template has changed:
				new responseClass(self.myGrid.attr('id'), interaction);
//The new or the intercation do, but not both ! doing the same with + in interaction
				//set the response responseOptions mode:
				//interaction.setResponseOptionsMode(r.setResponseOptionsMode);
			}
			self.setModifiedResponseProperties(false);
		}
		//auto save the response options values
		//$('#qtiAuthoring_responseOptionsEditor').find('.form-submiter').click();

		//check modified choices then send it as well:
		return false;
	});
}

responseClass.prototype.initResponseFormProcessingTypeChange = function(){
	$('#interactionResponseProcessing').change(function() {
		$('.xhtml_form').next().nextAll().remove();
		$(".response-form-submitter").click();
	});
}

responseClass.prototype.setResponseFormChangeListener = function(){
   var __this = this;
	$responseFormContainer = $('div#qtiAuthoring_responseEditor');
	$responseFormContainer.children().unbind('change paste').bind('change paste', function(){
		__this.setModifiedResponseProperties(true);
	});
}

responseClass.prototype.setModifiedResponseProperties = function(modified){
   if(modified){
		this.modifiedResponseOptions = true;
		$('a.response-form-submitter').addClass('form-submitter-emphasis');
	}else{
		this.modifiedResponseOptions = false;
		$('a.response-form-submitter').removeClass('form-submitter-emphasis');
	}
}

responseClass.prototype.buildGrid = function(tableElementId, serverResponse){

	// CD(serverResponse, 'response:');

	//firstly, get the column models, from the interactionSerial:
	//label = columName
	//name = name&index
	//the column model is defined by the interaction + processMatching type:
	// serverResponse = new Object();
	// serverResponse.colModel = [
		// {name:'choice1', label:'choice 1', edittype: 'fixed', values:['r1', 'r2', 'r3']},
		// {name:'choice2', label:'choice 2', edittype: 'select', values:{a1_id:'a1', a2_id:'a2', a3_id:'a3', a4_id:'a4'}},
		// {name:'correct', label:'correct response', edittype: 'checkbox', values:['yes', 'no']},
		// {name:'score', label:'score', edittype: 'text'}
	// ];

	// serverResponse.data = [
		// {id:'1', choice1:'r3', choice2:'a2', correct:'yes', score:'-2', 'scrap':'yeah'},
		// {id:'2', choice1:'r1', 'scrap2':'yeah', choice2:'a3', correct:null}
	// ];

	var fixedColumn = [];
	var colNames = [];
	var colModel = [];

	for(var i=0; i<serverResponse.colModel.length; i++){
		var colElt = serverResponse.colModel[i];
		colNames[i] = colElt.label;

		colModel[i] = [];
		colModel[i].name = colElt.name;
		colModel[i].index = colElt.name;
		colModel[i].editable = true;//all field is editable by default (except "fixed" column and id)
		if(colElt.name == 'shape'){
			this.areaMapping = true;
		}

		switch(colElt.edittype){
			case 'checkbox':{
				colModel[i].edittype = colElt.edittype;

				if(colElt.values){
					if(colElt.values.length){

						var value = '';
						for(var j=0; j<colElt.values.length; j++){
							value += colElt.values[j]+':';
						}
						value = value.substring(0,value.length-1);

						colModel[i].editoptions = {
							value:value
						};

					}
				}

				break;
			}
			case 'select':{

				colModel[i].edittype = colElt.edittype;
				if(colElt.values){
					var value = '';
					for(var k in colElt.values){
						value += k+':'+colElt.values[k]+';';
					}
					value = value.substring(0,value.length-1);

					colModel[i].editoptions = {
						value:value
					};
				}

				break;
			}
			case 'text':{
				colModel[i].edittype = colElt.edittype;
				break;
			}
			case 'fixed':{
				//the grid is set as requireing a column to be fixed
				colModel[i].editable = false;

				//record the name and the values of the column, it will be used to filter and display the grid after:
				if(fixedColumn.name){
					throw 'building grid: only one column can be fixed';
				}
				fixedColumn.name = colElt.name;
				fixedColumn.values = colElt.values;

				break;
			}
		}
	}
	this.colNames = colNames;
	this.colModel = colModel;

	//insert the pager:
	var pagerId = tableElementId + '_pager';
	var $myGridElt = $("#"+tableElementId);
	$myGridElt.after('<div id="' + pagerId + '"/>');

	var response = this;
	var gridOptions = {
		url: "/taoItems/QtiAuthoring/saveResponse",
		editData: {responseId:'aaa'},
		datatype: "local",
		colNames: colNames,
		colModel: colModel,
		rowNum:20,
		height:300,
		width:500,
		pager: '#'+tableElementId+'_pager',
		sortname: 'choice1',
		viewrecords: false,
		sortorder: "asc",
		caption: __("Responses Grid"),
		gridComplete: function(){
			response.resizeGrid();
			$(window).bind('resize', function(e){
				e.preventDefault();
				if(response) response.resizeGrid();
			});
		},
		onSelectRow: function(id){
			response.test = 'test1'
			// CD(response, 'selet row and editing');
			response.editGridRow(id);
			// CD(response, 'after response');
		}
	};


	this.interactionType = serverResponse.interactionType;
	if(serverResponse.interactionType == 'order' || serverResponse.interactionType == 'graphicorder'){
		gridOptions.width = 500;
		gridOptions.shrinkToFit = false;
		gridOptions.autowidth = true;
	}

	try{
		if (colModel.length) { //CHROME crash if empty
			this.myGrid = $myGridElt.jqGrid(gridOptions);
		}
	}catch(err){
		throw 'jgGrid constructor exception: '+err;
	}
	var interactionSerial = this.interactionSerial;



	//configure the navigation bar:
	//afterRefresh
	var navGridParam = {};
	var navGridParamDefault = {
		search: false,
		afterRefresh: function(){
			response.destroyGrid();
			new responseClass(tableElementId, interactionClass.instances[interactionSerial]);
		},
		editfunc: function(rowId){
			response.editGridRow(rowId);
		}
	};

	if(fixedColumn.name && fixedColumn.values){
		//is fixed, so disable the add and delete row
		var navGridParamOptions = {add:false, del:false};
	}else{
		var navGridParamOptions = {
			addfunc: function(){
				var newId = response.getUniqueRowId();
				response.restoreCurrentRow();
				response.myGrid.jqGrid('addRowData', newId, new Object(), 'last');
				response.editGridRow(newId);

				var maxChoices = parseInt(response.maxChoices);
				if((response.interactionType == 'order'|| response.interactionType == 'graphicorder') && maxChoices && response.myGrid.getGridParam("records") >= maxChoices){
					//disable row adding:
					response.disableRowAdding();
				}

				if(response.areaMapping){
					response.bindShapeEventListeners();
				}
			},
			delfunc: function(rowId){
				if(confirm(__("Do you really want to delete the row?"))){
					response.myGrid.jqGrid('delRowData', rowId);
					response.saveResponseGrid();

					var maxChoices = parseInt(response.maxChoices);
					if((response.interactionType == 'order'|| response.interactionType == 'graphicorder') && maxChoices && response.myGrid.getGridParam("records") < maxChoices){
						//enable row adding:
						response.enableRowAdding();
					}
				}
			}
		};
	}
	navGridParam = $.extend(navGridParam, navGridParamOptions, navGridParamDefault);
	try{
		this.myGrid.jqGrid('navGrid', '#'+pagerId, navGridParam);
	}catch(err){
		throw 'jgGrid navigator constructor exception: '+err;
	}

	var interaction = null;

	if(this.areaMapping){
		interaction = interactionClass.instances[response.interactionSerial];

		//detroy all shapes
		if(interaction && interaction.shapeEditor){
			for(shapeId in interaction.shapeEditor.shapes){
				interaction.shapeEditor.removeShapeObj(shapeId);
			}
		}
	}

	try{
		if(fixedColumn.name && fixedColumn.values){
			//there is a column that have fixed values, so only keep rows that has the fixed value:
			for(var i=0; i<fixedColumn.values.length; i++){

				var theValue = fixedColumn.values[i];

				//find the corresponding row with such a value
				var theRow = null;
				for(var j=0; j<serverResponse.data.length; j++){
					var aRow = serverResponse.data[j];
					if(aRow[fixedColumn.name] == theValue){
						theRow = aRow;
						break;
					}
				}

				if(!theRow){
					theRow = new Object();
					//create the default row from the column model:
					for(var k=0; k<serverResponse.colModel.length; k++){
						var colElt = serverResponse.colModel[k];
						var val = null;
						if(colElt.name == fixedColumn.name){
							val = theValue;
						}else if(colElt.values){
							switch(colElt.edittype){
								case 'checkbox':{
									val = colElt.values[1];//take the "false" variable
									break;
								}
								case 'select':{
									for(var key in colElt.values){
										val = colElt.values[key];
										break;
									}
									break;
								}
							}
						}else{
							val = '';
						}
						theRow[colElt.name] = val;
					}

				}

				//add row:
				this.myGrid.jqGrid('addRowData', i, theRow);
			}
		}else{


			//insert all row in it:
			var dataLength = serverResponse.data.length
			for(var j=0; j<dataLength; j++){
				var data = serverResponse.data[j];
				this.myGrid.jqGrid('addRowData', j, data);

				if(this.areaMapping && interaction && data.shape && data.coordinates && interaction.shapeEditor){
					var shapeId = j+'_shape';
					interaction.shapeEditor.createShape(shapeId, 'qti', {data: data.coordinates, shape: data.shape});
					interaction.shapeEditor.exportShapeToCanvas(shapeId);
				}else{
					if(this.areaMapping) throw 'wrong response data format for area mapping';
				}
			}


		}
	}
	catch(err){
		throw 'jgGrid adding row exception: '+err;
	}

	this.fixedColumn = fixedColumn;

	if(this.areaMapping){
		this.bindShapeEventListeners();
	}

	this.resizeGrid();
	$(window).bind('resize', function(e){
		e.preventDefault();
		if(responseClass.grid) responseClass.grid.resizeGrid();
	});

	return this;
}

responseClass.prototype.bindShapeEventListeners = function(){
	if(this.areaMapping){
		var interaction = interactionClass.instances[this.interactionSerial];
		if(interaction){
			this.myGrid.find('tr').unbind('mouseenter mouseleave').hover(function(){
				interaction.shapeEditor.hoverIn($(this).attr('id')+'_shape');
			},function(){
				interaction.shapeEditor.hoverOut($(this).attr('id')+'_shape');
			});
		}
	}


}

responseClass.prototype.resizeGrid = function(){
	if(this.myGrid){
		if(this.myGrid.length && $(this.responseFormContainer).is(":visible")){
			this.myGrid.jqGrid('setGridWidth', $('#qtiAuthoring_responseEditor').width()-10);
		}
	}
}

responseClass.prototype.destroyGrid = function(){

	if(this.myGrid){

		if(this.myGrid.length){
			var selector = this.myGrid.selector;//$myGrid
			$(selector).GridUnload(selector);
		}else{
			throw 'the grid content has not been found';
		}
	}
}

responseClass.prototype.editGridRow = function(rowId){
	var id = parseInt(rowId);
	var $currentRow = this.myGrid.find('tr#'+id);

	if(id>=0 && id!=='' && id!==this.currentRowId){

		// this.myGrid.jqGrid('restoreRow',this.currentRowId);//restore the previously edited row
		this.restoreCurrentRow(this.currentRowId);

		this.currentRowData = this.myGrid.jqGrid('getRowData', id);

		var response = this;
		this.myGrid.jqGrid(
			'editRow',
			id,
			true,
			function(id){
				if(id>=0){
					//for select point and position object interaction only:
					var editingRow = response.myGrid.jqGrid('getRowData', id);
					if(response.areaMapping && response.currentRowData && editingRow.shape && editingRow.coordinates){

						var shapeId = id+'_shape';
						var $shapeElt = response.myGrid.find('#'+shapeId);
						var $coordElt = response.myGrid.find('#'+id+'_coordinates');
						var $correctElt = response.myGrid.find('#'+id+'_correct');
						var $scoreElt = response.myGrid.find('#'+id+'_score');
						var interaction = interactionClass.instances[response.interactionSerial];

						if(interaction && $coordElt.length && $shapeElt.length){

							interaction.shapeEditor.drawn(function(currentId, shapeObject, self){
								//export shapeObject to qti:
								// CD(shapeObject, 'drawn');
								if(currentId && shapeObject){
									var qtiCoords = self.exportShapeToQti(currentId);
									if(qtiCoords){
										//update it!
										$coordElt.val(qtiCoords);
									}
								}
							});

							var shapeDrawingFunction = function(e){
								// $(this).attr('disabled', 'disabled');
								// e.preventDefault();

								var shape = $shapeElt.val();
								if(interaction.shapeEditor && shape){
									interaction.shapeEditor.startDrawing(shapeId, shape);
								}

							}

							var correctCheckedFunction = function(){
								var hideOptionFunction = function($optionElt){
									$optionElt.hide();
									// $optionElt.
								}
								var showOptionFunction = function($optionElt){
									$optionElt.show();
									// $optionElt.
								}

								if($correctElt.is(':checked')){
									$shapeElt.val('point');
									$shapeElt.find('option').each(function(){
										if($(this).val() == 'point'){
											showOptionFunction($(this));
										}else{
											hideOptionFunction($(this));
										}
									});
									$scoreElt.val('').attr('disabled', 'disabled');
								}else{
									$shapeElt.val('circle');
									$shapeElt.find('option').each(function(){
										if($(this).val() == 'point'){
											hideOptionFunction($(this));
										}else{
											showOptionFunction($(this));
										}
									});
									$scoreElt.removeAttr('disabled');
								}

							}

							$correctElt.change(function(){
								$coordElt.val('');
								interaction.shapeEditor.removeShapeObj(shapeId);
								correctCheckedFunction();
								shapeDrawingFunction();
							});

							//execute the function immediately to allow immediate drawing capability
							correctCheckedFunction();
							shapeDrawingFunction();

							$coordElt.bind('focus', shapeDrawingFunction);
							$shapeElt.bind('change', function(){
								$coordElt.val('');
								interaction.shapeEditor.removeShapeObj(shapeId);
								shapeDrawingFunction();
							});

							$coordElt.keyup(function(){
								var shape = $shapeElt.val();
								var qtiCoords = $coordElt.val();
								if(qtiCoords && shape){
									interaction.shapeEditor.createShape(shapeId, 'qti', {data: qtiCoords, shape: shape});
									interaction.shapeEditor.exportShapeToCanvas(shapeId);
								}
							});

						}
					}
				}
			},
			null,
			'clientArray',
			{'optionalData': null},
			function(){

				var repeatedChoice = response.checkRepeatedChoice(response.currentRowId);
				var repeatedRow =  response.checkRepeatedRow(response.currentRowId);
				var maxChoicesRespected = response.checkCardinality(response.currentRowId);
				if(repeatedChoice){
					alert('There cannot be identical choice in a row.');
					response.myGrid.jqGrid('setRowData', response.currentRowId, response.currentRowData);
					response.restoreCurrentRow();
					return false;
				}
				if(repeatedRow){
					alert('There is already a row with the same choices.');
					response.myGrid.jqGrid('setRowData', response.currentRowId, response.currentRowData);
					response.restoreCurrentRow();
					return false;
				}
				if(!maxChoicesRespected){
					alert('Impossible to exceed the maximum number of choices defined in the interaction');
					response.myGrid.jqGrid('setRowData', response.currentRowId, response.currentRowData);
					response.restoreCurrentRow();
					return false;
				}

				response.saveResponseGrid();
				response.restoreCurrentRow();
			},
			null,
			function(rowId){
				// CD(response, 'after response 1');
				response.restoreCurrentRow(rowId);
				// CD(response, 'after response 2');
			}
		);
		response.currentRowId = id;
		this.currentRowId = id;

		var triggerRowSave = function($gridRow){
			var e = jQuery.Event("keydown");
			e.which = 13;
			e.keyCode = 13;//for MSIE...
			$gridRow.trigger(e);
		};

		$currentRow.find('input,select').each(function(){

			var realFocused = false;
			$(this).focus(function(){
				realFocused = true;
			});

			$(this).unbind('blur').blur(function(){
				if(realFocused){
					triggerRowSave($(this));
					realFocused = false;
				}
			});

			//for order intereactions only:
			if(response.interactionType == 'order' || response.interactionType == 'graphicorder'){

				$(this).change(function(){
					var myId = $(this).attr('id');
					var myValue = $(this).val();
					var $allSelectElts = $currentRow.find('select');
					var pickedValues = [];
					pickedValues.push(parseInt(myValue));

					$allSelectElts.each(function(){
						if($(this).attr('id') != myId){

							var otherValue = parseInt($(this).val());
							var newOtherValue = otherValue;
							var i = 0;
							while( util.indexOf(pickedValues, newOtherValue) >= 0 ){
								newOtherValue = newOtherValue + 1;
								if(newOtherValue >= $allSelectElts.length){
									newOtherValue = 0;
								}

								if(i>$allSelectElts.length){
									break;
								}
								i++;
							}

							pickedValues.push(newOtherValue);
							$(this).val(newOtherValue);
						}
					});

				});
			}

		});

	}
}

responseClass.prototype.getUniqueRowId = function(){
	var responseData = this.myGrid.jqGrid('getRowData');
	return responseData.length;
}

responseClass.prototype.restoreCurrentRow = function(rowId){

	if(parseInt(this.currentRowId) >= 0){

		if(!rowId) rowId = this.currentRowId;
		this.myGrid.jqGrid('restoreRow', rowId);

		var currentRowData = this.myGrid.jqGrid('getRowData', rowId);
		//redraw the shape here!
		if(currentRowData.shape && currentRowData.coordinates){
			var interaction = interactionClass.instances[this.interactionSerial];
			if(interaction){
				var shapeId = rowId+'_shape';
				interaction.shapeEditor.createShape(shapeId, 'qti', {data: currentRowData.coordinates, shape: currentRowData.shape});
				interaction.shapeEditor.exportShapeToCanvas(shapeId);
			}
		}

		this.myGrid.jqGrid('resetSelection');
		this.currentRowId = -2;

	}else{
		// CL('restoring failed');
	}
}

responseClass.prototype.saveResponseGrid = function(){
	var responseData = this.myGrid.jqGrid('getRowData');
	var responseDataString = JSON.stringify(responseData);

	//save to server:
	//global processUri value
	$.ajax({
		url: root_url + "/taoItems/QtiAuthoring/saveResponse",
		type: "POST",
		data: {'interactionSerial': this.interactionSerial, "responseDataString": responseDataString},
		dataType: 'json',
		success: function(response){
			if (response.saved){
				qtiEdit.createInfoMessage(__('The responses have been updated'));
			}else{
				createErrorMessage(__('The responses cannot be updated'));
			}
		}
	});
}

responseClass.prototype.checkRepeatedChoice = function(rowId){
	var data = this.myGrid.jqGrid('getRowData', rowId);
	delete data['correct'];
	delete data['score'];

	var choices = []
	for(var columnName in data){
		choices.push(data[columnName]);
	}

	for(var i=0; i<choices.length; i++){
		for(var j=i+1; j<choices.length; j++){
			if(choices[i] == choices[j]){
				return true;
			}
		}
	}
	return false;
}

responseClass.prototype.checkRepeatedRow = function(rowId){
	var thisRowDataObject = this.myGrid.jqGrid('getRowData', rowId);
	var thisRowData = [];
	var thisRowDataLength = 0;
	for(var key in thisRowDataObject){
		if(key != 'correct' && key != 'score'){
			thisRowData[key] = thisRowDataObject[key];
			thisRowDataLength ++;
		}
	}

	var allData = this.myGrid.jqGrid('getRowData');
	for(var i = 0; i<allData.length; i++){
		var count = 0;
		if(i == rowId){
			continue;
		}
		//compare each element:
		var anotherRowData = allData[i];
		for(var columnName in thisRowData){
			if(thisRowData[columnName] == anotherRowData[columnName]){
				count++;
				continue;
			}else{
				break;
			}
		}

		if(count == thisRowDataLength){
			return true;
		}
	}
	return false;
}

responseClass.prototype.checkCardinality = function(rowId){

	if(!this.maxChoices){
		return true;//infinite choice/association by default
	}

	//the number of existing correct response against
	var thisRowData = this.myGrid.jqGrid('getRowData', rowId);
	var mappingMode = false;
	if(thisRowData['correct']){
		if(thisRowData['correct'] == 'no'){
			return true;//ok
		}else{
			//need for checking
			mappingMode = true;
		}
	}
	//need for checking:

	//count the number of existing
	var allData = this.myGrid.jqGrid('getRowData');
	var count = 0;
	for(var i = 0; i<allData.length; i++){
		if(i == rowId){
			continue;
		}

		var anotherRowData = allData[i];
		if(mappingMode){
			if(anotherRowData['correct'] == 'yes'){
				count++;
			}
		}else{
			count++;
		}
	}
	if(count<this.maxChoices){
		return true;
	}else{
		return false;
	}
}

responseClass.prototype.disableRowAdding = function(){
	if(this.myGrid){
		if(this.myGrid.attr){
			$('#add_'+this.myGrid.attr('id')).hide();
		}
	}
}

responseClass.prototype.enableRowAdding = function(){
	if(this.myGrid){
		if(this.myGrid.attr){
			$('#add_'+this.myGrid.attr('id')).show();
		}
	}
}

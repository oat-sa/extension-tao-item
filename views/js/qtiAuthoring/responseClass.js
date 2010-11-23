alert('response edit loaded');

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
	this.currentRowId = null;
	this.maxChoices = null;
	
	if(!responseFormContainer){var responseFormContainer='#qtiAuthoring_response_formContainer';}
	this.responseFormContainer = responseFormContainer;
	
	$.ajax({
		url: "/taoItems/QtiAuthoring/editResponse",
		type: "POST",
		data: {
			'interactionSerial': this.interactionSerial,
			'itemSerial': interaction.getRelatedItem(true).itemSerial
		},
		dataType: 'json',
		success: function(serverResponse){
			if (serverResponse.ok){
				
				//reset the grid:
				$('#'+tableElementId).empty();
				
				//set the response form if needed:
				$(response.responseFormContainer).html(serverResponse.responseForm);
				response.initResponseFormSubmitter();
				
				//set the amximum allowed correct responses, according to the maxChoices attribute defined at the itneraction level.
				if(serverResponse.maxChoices) response.maxChoices = serverResponse.maxChoices;
					
				if(serverResponse.displayGrid){
					response.buildGrid(tableElementId, serverResponse);
				}
			}else{
				throw 'error in loading the response editing data';
			}
		}
	});
	
	
}

responseClass.prototype.initResponseFormSubmitter = function(){
	var self = this;
	$(".response-form-submitter").click(function(){
		
		var $myForm = $(this).parents("form");
		//linearize it and post it:
		$.ajax({
		   type: "POST",
		   url: "/taoItems/QtiAuthoring/saveResponseProperties",
		   data: $myForm.serialize(),
		   dataType: 'json',
		   success: function(r){
				if(r.saved){
					createInfoMessage(__('Modification on response applied'));
					
					if(r.templateHasChanged){
						//reload the grid, just in case the response template has changed:
						if(interactionClass.instances[self.interactionSerial]){
							new responseClass(self.myGrid.attr('id'), interactionClass.instances[self.interactionSerial]);
						}
						
					}
				}
		   }
		});
		
		//check modified choices then send it as well:
		return false;
	});
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
				
			}
		}
	}
	
	// CL('colNames', colNames);
	// CL('colModel', colModel);
	this.colNames = colNames;
	this.colModel = colModel;
	
	//insert the pager:
	var pagerId = tableElementId + '_pager';
	$("#"+tableElementId).after('<div id="' + pagerId + '"/>');
	
	var response = this;
	var gridOptions = {
		url: "/taoItems/QtiAuthoring/saveResponse",
		editData: {responseId:'aaa'},
		datatype: "local", 
		colNames: colNames, 
		colModel: colModel, 
		rowNum:20, 
		height:300, 
		width:'',
		pager: '#'+tableElementId+'_pager', 
		sortname: 'choice1', 
		viewrecords: false, 
		sortorder: "asc", 
		caption: __("Responses Editor"),
		gridComplete: function(){},
		onSelectRow: function(id){
			response.editGridRow(id);
		}
	};
	
	if(serverResponse.interactionType == 'order'){
		gridOptions.width = 500;
		gridOptions.shrinkToFit = false;
		gridOptions.autowidth = true;
		// gridOptions.gridComplete = function(){
			// $(window).unbind('resize').bind('resize', function(){
				// CD(this);
			// }
		// };
	}
	
	this.myGrid = $("#"+tableElementId).jqGrid(gridOptions);
	
	var interactionSerial = this.interactionSerial;
	
	
	
	//configure the navigation bar:
	//afterRefresh
	var navGridParam = {};
	var navGridParamDefault = {
		search: false,
		afterRefresh: function(){
			// CL('refreshed');
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
				response.myGrid.jqGrid('addRowData', newId, new Object(), 'last');
				response.editGridRow(newId);
			},
			delfunc: function(rowId){
				if(confirm(__("Do you really want to delete the row?"))){
					response.myGrid.jqGrid('delRowData', rowId);
					response.saveResponseGrid();
				}
			}
		};
	}
	navGridParam = $.extend(navGridParam, navGridParamOptions, navGridParamDefault);
	
	this.myGrid.jqGrid('navGrid', '#'+pagerId, navGridParam); 
	
	
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
			
			// CL('added row:', theRow)
			//add row:
			this.myGrid.jqGrid('addRowData', i, theRow);	
		}
	}else{
		//insert all row in it:
		for(var j=0; j<serverResponse.data.length; j++){
			this.myGrid.jqGrid('addRowData', j, serverResponse.data[j]);	
		}
	}
	
	this.fixedColumn = fixedColumn;
	
	return this;
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
	var id = rowId;
	var response = this;
	
	if(id && id!=='' && id!==this.currentRowId){
		this.myGrid.jqGrid('restoreRow',this.currentRowId);
		this.currentRowData = this.myGrid.jqGrid('getRowData', id);
		this.myGrid.jqGrid(
			'editRow',
			id,
			true,
			null, 
			null, 
			'clientArray',
			{'optionalData': null},
			function(){
				// responseEdit.grid.myGrid.jqGrid('resetSelection');
				
				var repeatedChoice = response.checkRepeatedChoice(response.currentRowId);
				var repeatedRow =  response.checkRepeatedRow(response.currentRowId);
				var maxChoicesRespected = response.checkCardinality(response.currentRowId);
				if(repeatedChoice){
					alert('There cannot be identical choice in a row.');
					// responseEdit.grid.myGrid.jqGrid('restoreRow',responseEdit.grid.currentRowId);
					// CL('responseEdit.grid.currentRowData', responseEdit.grid.currentRowData);
					response.myGrid.jqGrid('setRowData', response.currentRowId, response.currentRowData);
					return false;
				}
				if(repeatedRow){
					alert('There is already a row with the same choices.');
					response.myGrid.jqGrid('setRowData', response.currentRowId, response.currentRowData);
					return false;
				}
				if(!maxChoicesRespected){
					alert('Impossible to exceed the maximum number of choices defined in the interaction');
					response.myGrid.jqGrid('setRowData', response.currentRowId, response.currentRowData);
					return false;
				}
				
				response.saveResponseGrid();
								
				// responseEdit.grid.myGrid.jqGrid('restoreRow',responseEdit.grid.currentRowId);
				response.myGrid.jqGrid('resetSelection');
			}
		); 
		response.currentRowId = id;
		
		//local edit, then systematic global save:
	}
}

responseClass.prototype.getUniqueRowId = function(){
	var responseData = this.myGrid.jqGrid('getRowData');
	return responseData.length;
}


responseClass.prototype.saveResponseGrid = function(){
	var responseData = this.myGrid.jqGrid('getRowData');
	var responseDataString = JSON.stringify(responseData);
	
	//save to server:
	//global processUri value
	$.ajax({
		url: "/taoItems/QtiAuthoring/saveResponse",
		type: "POST",
		data: {'interactionSerial': this.interactionSerial, "responseDataString": responseDataString},
		dataType: 'json',
		success: function(response){
			if (response.saved){
				createInfoMessage(__('The responses have been updated'));
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
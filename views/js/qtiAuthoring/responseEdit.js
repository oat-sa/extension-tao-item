alert('response edit loaded');

responseEdit = new Object();
responseEdit.grid = null;


responseEdit.destroyGrid = function(tableElementId){
	responseEdit.grid = [];
	if(!tableElementId){
		var selector = '#'+responseEdit.grid.myGrid.attr('id');
	}else{
		var selector = '#'+tableElementId;
	}
	
	$(selector).GridUnload(selector);
	// $('#'+tableElementId).empty();
}

responseEdit.buildGrid = function(tableElementId, interactionSerial){
	
	if(responseEdit.grid){
		if(responseEdit.grid.myGrid){
			CL('destroy grid');
			responseEdit.destroyGrid(tableElementId);
		}
	}
	responseEdit.grid = [];
	responseEdit.grid.interactionSerial = interactionSerial;
	
	$.ajax({
		url: "/taoItems/QtiAuthoring/editResponse",
		type: "POST",
		data: {
			'interactionSerial': responseEdit.grid.interactionSerial,
			'itemSerial': qtiEdit.itemSerial
		},
		dataType: 'json',
		success: function(serverResponse){
			if (serverResponse.ok){
				//reset the grid:
				
				$('#'+tableElementId).empty();
				
				buildGrid(serverResponse);
			}else{
				CL('error in loading the response editing data');
			}
		}
	});
	
	var buildGrid = function(serverResponse){
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
						// if(colElt.values.length){
						
							var value = '';
							for(var k in colElt.values){
								value += k+':'+colElt.values[k]+';';
							}
							value = value.substring(0,value.length-1);
							
							colModel[i].editoptions = {
								value:value
							};
							
						// }
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
		responseEdit.grid.colNames = colNames;
		responseEdit.grid.colModel = colModel;
		
		//insert the pager:
		var pagerId = tableElementId + '_pager';
		$("#"+tableElementId).after('<div id="' + pagerId + '"/>');
		
		
		responseEdit.grid.myGrid = $("#"+tableElementId).jqGrid({
			url: "/taoItems/QtiAuthoring/saveResponse",
			// editurl: "/taoItems/QtiAuthoring/saveResponse",
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
			gridComplete: function(){
				
				// $.each(historyGrid.getDataIDs(), function(index, elt){
					// historyGrid.setRowData(elt, {
						// actions: "<a id='history_deletor_"+elt+"' href='#' class='user_deletor nd' ><img class='icon' src='<?=BASE_WWW?>img/delete.png' alt='<?=__('Delete History')?>' /><?=__('Delete')?></a>"
					// });
				// });
				// $(".user_deletor").click(function(){
					// removeHistory(this.id.replace('history_deletor_', ''));
				// });
			},
			onSelectRow: function(id){
				responseEdit.editGridRow(id);
			}
		});
		//configure the navigation bar:
		//afterRefresh
		var navGridParam = {};
		var navGridParamDefault = {
			search: false,
			afterRefresh: function(){
				CL('refreshed');
				responseEdit.destroyGrid(tableElementId);
				responseEdit.buildGrid(tableElementId, interactionSerial);
			},
			editfunc: function(rowId){
				responseEdit.editGridRow(rowId);
			}
		}
		if(fixedColumn.name && fixedColumn.values){
			//is fixed, so disable the add and delete row
			var navGridParamOptions = {add:false, del:false};
		}else{
			var navGridParamOptions = {
				addfunc: function(){
					var newId = responseEdit.getUniqueRowId();
					responseEdit.grid.myGrid.jqGrid('addRowData', newId, new Object(), 'last');
					responseEdit.editGridRow(newId);
				},
				delfunc: function(rowId){
					if(confirm(__("Do you really want to delete the row?"))){
						responseEdit.grid.myGrid.jqGrid('delRowData', rowId);
						responseEdit.saveResponseGrid();
					}
				}
			};
		}
		navGridParam = $.extend(navGridParam, navGridParamOptions, navGridParamDefault);
		
		responseEdit.grid.myGrid.jqGrid('navGrid', '#'+pagerId, navGridParam); 
		
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
				responseEdit.grid.myGrid.jqGrid('addRowData', i, theRow);	
			}
		}else{
			//insert all row in it:
			for(var j=0; j<serverResponse.data.length; j++){
				responseEdit.grid.myGrid.jqGrid('addRowData', j, serverResponse.data[j]);	
			}
		}
		
		responseEdit.grid.fixedColumn = fixedColumn;
	}
}

responseEdit.editGridRow = function(rowId){
	var id = rowId;
	
	if(id && id!==responseEdit.grid.currentRowId){
		responseEdit.grid.myGrid.jqGrid('restoreRow',responseEdit.grid.currentRowId);
		responseEdit.grid.currentRowData = responseEdit.grid.myGrid.jqGrid('getRowData', id);
		responseEdit.grid.myGrid.jqGrid(
			'editRow',
			id,
			true,
			null, 
			null, 
			'clientArray',
			{'optionalData': null},
			function(){
				// responseEdit.grid.myGrid.jqGrid('resetSelection');
				
				var repeatedChoice = responseEdit.checkRepeatedChoice(responseEdit.grid.currentRowId);
				var repeatedRow =  responseEdit.checkRepeatedRow(responseEdit.grid.currentRowId);
				if(repeatedChoice){
					alert('There cannot be identical choice in a row.');
					// responseEdit.grid.myGrid.jqGrid('restoreRow',responseEdit.grid.currentRowId);
					CL('responseEdit.grid.currentRowData', responseEdit.grid.currentRowData);
					responseEdit.grid.myGrid.jqGrid('setRowData', responseEdit.grid.currentRowId, responseEdit.grid.currentRowData);
					return false;
				}
				if(repeatedRow){
					alert('There is already a row with the same choices.');
					// responseEdit.grid.myGrid.jqGrid('restoreRow',responseEdit.grid.currentRowId);
					responseEdit.grid.myGrid.jqGrid('setRowData', responseEdit.grid.currentRowId, responseEdit.grid.currentRowData);
					return false;
				}
				// CL('repeatedChoice', repeatedChoice);
				// CL('repeatedRow', repeatedRow);
				
				responseEdit.saveResponseGrid();
								
				// responseEdit.grid.myGrid.jqGrid('restoreRow',responseEdit.grid.currentRowId);
				responseEdit.grid.myGrid.jqGrid('resetSelection');
			}
		); 
		responseEdit.grid.currentRowId = id;
		
		//local edit, then systematic global save:
	}
}

responseEdit.getUniqueRowId = function(){
	var responseData = responseEdit.grid.myGrid.jqGrid('getRowData');
	return responseData.length;
}

responseEdit.saveResponseGrid = function(){
	var responseData = responseEdit.grid.myGrid.jqGrid('getRowData');
	var responseDataString = JSON.stringify(responseData);
	
	//save to server:
	//global processUri value
	$.ajax({
		url: "/taoItems/QtiAuthoring/saveResponse",
		type: "POST",
		data: {'interactionSerial': responseEdit.grid.interactionSerial, "responseDataString": responseDataString},
		dataType: 'json',
		success: function(response){
			if (response.saved){
				
			}else{
			
			}
		}
	});
}

responseEdit.checkRepeatedChoice = function(rowId){
	var data = responseEdit.grid.myGrid.jqGrid('getRowData', rowId);
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

responseEdit.checkRepeatedRow = function(rowId){
	var thisRowDataObject = responseEdit.grid.myGrid.jqGrid('getRowData', rowId);
	var thisRowData = [];
	var thisRowDataLength = 0;
	for(var key in thisRowDataObject){
		if(key != 'correct' && key != 'score'){
			thisRowData[key] = thisRowDataObject[key];
			thisRowDataLength ++;
		}
	}
	
	var allData = responseEdit.grid.myGrid.jqGrid('getRowData');
	for(var i = 0; i<allData.length; i++){
		var count = 0;
		// CL('loop', i);
		// CL('thisRowData', thisRowData);
		// CL('anotherRowData', anotherRowData);
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
		// CL('count', count);
		if(count == thisRowDataLength){
			//if the anotherRowData is able to exit the for loop, a identical row has been found!
			// CL('thisRowData', thisRowData);
			// CL('anotherRowData', anotherRowData);
			return true;
		}
	}
	return false;
}
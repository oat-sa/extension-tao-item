alert('response edit loaded');

responseEdit = new Object();
responseEdit.grid = null;
serverResponse = new Object();

responseEdit.buildGrid = function(tableElementId, interactionId){
	
	//reset the grid:
	responseEdit.grid = [];
	responseEdit.grid.interactionId = interactionId;
	
	//firstly, get the column models, from the interactionId:
	//label = columName
	//name = name&index
	//the column model is defined by the interaction + processMatching type:
	serverResponse.colModel = [
		{name:'choice1', label:'choice 1', edittype: 'fixed', values:['r1', 'r2', 'r3']},
		{name:'choice2', label:'choice 2', edittype: 'select', values:{a1_id:'a1', a2_id:'a2', a3_id:'a3', a4_id:'a4'}},
		{name:'correct', label:'correct response', edittype: 'checkbox', values:['yes', 'no']},
		{name:'score', label:'score', edittype: 'text'}
	];
	
	serverResponse.data = [
		{id:'1', choice1:'r3', choice2:'a2', correct:'yes', score:'-2', 'scrap':'yeah'},
		{id:'2', choice1:'r1', 'scrap2':'yeah', choice2:'a3', correct:null}
	];
	
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
			if(id && id!==responseEdit.grid.currentRowId){
				responseEdit.grid.myGrid.jqGrid('restoreRow',responseEdit.grid.currentRowId);
				// responseEdit.grid.myGrid.jqGrid('editRow',id,true, null, null, "/taoItems/QtiAuthoring/saveResponse", {'interactionId': interactionId}); 
				responseEdit.grid.myGrid.jqGrid('editRow',id,true, null, null, 'clientArray', {'interactionId': interactionId}, function(){
					var responseData = responseEdit.grid.myGrid.jqGrid('getRowData');
					CD(responseData);
					var responseDataString = JSON.stringify(responseData);
					
					//save to server:
					//global processUri value
					CL('responseEdit.grid.interactionId', responseEdit.grid.interactionId);
					$.ajax({
						url: "/taoItems/QtiAuthoring/saveResponse",
						type: "POST",
						data: {'interactionId': responseEdit.grid.interactionId, "responseDataString": responseDataString},
						dataType: 'json',
						success: function(response){
							// console.log(response);
							if (response.ok){
								// console.log('diagram saved');
							}else{
								// console.log('error in saving the diagram');
							}
						}
					});
				}); 
				responseEdit.grid.currentRowId = id;
				
				
				
				//local edit, then systematic global save:
				
			}
		}
	});
	//configure the navigation bar:
	var navGridParam = {};
	if(fixedColumn.name && fixedColumn.values){
		//is fixed, so disable the add and delete row
		navGridParam = {add:false, del:false, search:false};
	}else{
		
	}
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
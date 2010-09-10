alert('response edit loaded');

responseEdit = new Object();
responseEdit.grid = null;
serverResponse = new Object();

responseEdit.buildGrid = function(tableElementId, interactionId){
	
	//reset the grid:
	responseEdit.grid = [];
	
	//firstly, get the column models, from the interactionId:
	//label = columName
	//name = name&index
	//the column model is defined by the interaction + processMatching type:
	serverResponse.colModel = [
		{name:'id', label:'id', edittype: 'text'},
		{name:'choice1', label:'choice 1', edittype: 'fixed', values:['r1', 'r2', 'r3']},
		{name:'choice2', label:'choice 2', edittype: 'select', values:['a1', 'a2', 'a3', 'a4']},
		{name:'correct', label:'correct response', edittype: 'checkbox', values:['yes', 'no']},
		{name:'score', label:'score', edittype: 'text'}
	];
	
	serverResponse.data = [
		{id:'1', choice1:'r1', choice2:'a2', correct:'no', score:'-2'},
		{id:'2', choice1:'r2', choice2:'a3', correct:null}
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
		
		if(colElt.edittype == 'select' || colElt.edittype == 'checkbox'){
		
			colModel[i].edittype = colElt.edittype;
			
		}else if(colElt.edittype == 'fixed' ){
		
			//the grid is set as requireing a column to be fixed
			colModel[i].editable = false;
			
			//record the name and the values of the column, it will be used to filter and display the grid after:
			if(fixedColumn.name){
				throw 'building grid: only one column can be fixed';
			}
			fixedColumn.name = colElt.name;
			fixedColumn.values = colElt.values;
		}
		
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
		
	}
	
	
	CL('colNames', colNames);
	CL('colModel', colModel);
	responseEdit.grid.colNames = colNames;
	responseEdit.grid.colModel = colModel;
	
	//insert the pager:
	var pagerId = tableElementId + '_pager';
	$("#"+tableElementId).after('<div id="' + pagerId + '"/>');

	responseEdit.grid.myGrid = $("#"+tableElementId).jqGrid({
		datatype: "local", 
		colNames: colNames, 
		colModel: colModel, 
		rowNum:20, 
		height:300, 
		width:'',
		pager: '#'+tableElementId+'_pager', 
		sortname: 'id', 
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
				responseEdit.grid.myGrid.jqGrid('editRow',id,true); 
				responseEdit.grid.currentRowId = id; 
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
						val = colElt.values[0];
					}else if(colElt.name == 'id'){
						val = i+1;
					}else{
						val = '';
					}
					theRow[colElt.name] = val;
				}
				
			}
			
			CL('added row:', theRow)
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
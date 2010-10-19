TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

TAO_MATCHING.Map = function (data) {
	// Set the value of the variable
	this.setValue (data);
};

TAO_MATCHING.Map.prototype = { 
	map : function (matchingVar) {
		var returnValue = 0.0;
        var mapKeyFound = [];
        
        // for each map element, check if it is represented in the given variable
    	for (var mapKey in this.value) {
    		
    		// If the given var is a collection
    		if ( (matchingVar instanceof TAO_MATCHING.List) || (matchingVar instanceof TAO_MATCHING.Tuple)){
    			if (matchingVar.contain (this.value[mapKey]['key'])!=null){
    				returnValue += this.value[mapKey]['value'];
    			}	
    		}
    		else {
    			if (matchingVar.match (this.value[mapKey]['key'])){
    				returnValue += this.value[mapKey]['value'];
    			}
    			
    		}
	    }
		
        return returnValue;
	}
	
	, setValue : function (data) {
		this.value = [];
	 	for (var i in data){
    		this.value.push ({"value":data[i].value, "key":TAO_MATCHING.VariableFactory.create(data[i].key)});
    	}  
	}
};


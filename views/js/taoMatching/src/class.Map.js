TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

TAO_MATCHING.Map = function (data) {
	// Set the value of the variable
	this.setValue (data);
};

TAO_MATCHING.Map.prototype = {
    /**
     * Short description of method map
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return double
     */
	map : function (matchingVar) {
		var returnValue = 0.0;
        var mapKeyFound = [];
        
        // for each map element, check if it is represented in the given variable
    	for (var mapKey in this.value) {
    		
    		// If the given var is a collection
    		if ( TAO_MATCHING.Variable.isCollection (matchingVar)){
    		    // For each value contained by the matching var to map
                for (var varKey in matchingVar.value) {
                    // If one match the current map value
                    if (matchingVar.value[varKey].match (this.value[mapKey]['key'])) {
                        returnValue += this.value[mapKey]['value'];
                        break;
                    }
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
	
    /**
     * Short description of method setValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return mixed
     */
	, setValue : function (data) {
		this.value = [];
	 	for (var i in data){
    		this.value.push ({"value":data[i].value, "key":TAO_MATCHING.VariableFactory.create(data[i].key)});
    	}
	}
};


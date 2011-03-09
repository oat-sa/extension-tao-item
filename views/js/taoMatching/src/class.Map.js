TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

/**
 * @class
 * 
 * A special class used to create a mapping from a source set of 
 * any baseType to a single float.
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package TAO_MATCHING
 * 
 * @constructor 
 */
TAO_MATCHING.Map = function (data) {
	// Set the value of the variable
	this.setValue (data);
};

TAO_MATCHING.Map.prototype = {
    /**
     * This function looks up the value of a given 
     * Variable and then transforms it using the associated 
     * mapping. The result is a single float. If the given variable 
     * has single cardinality then the value returned is simply the 
     * mapped target value from the map.
     * If the response variable has  multiple cardinality then the 
     * value returned is the sum of the mapped target values.
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
     * Set the value of the map
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


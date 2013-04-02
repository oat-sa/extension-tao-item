/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
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
	this.value = null;
	this.lowerBound = null;
	this.upperBound = null;
	this.defaultValue = 0;
	
	// Set the value of the variable
	this.setValue (data.value);

	// Set default value
	if (typeof data.defaultValue != 'undefined'){
		this.defaultValue = data.defaultValue;
	}
	// Set lower bound
	if (typeof data.lowerBound != 'undefined'){
		this.lowerBound = data.lowerBound;
	}
	// Set upper bound
	if (typeof data.upperBound != 'undefined'){
		this.upperBound = data.upperBound;
	}
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
        var mapEntriesFound = new Array();
    	// for each map element, check if it is represented in the given variable
    	for (var mapKey in this.value){
    		
    		// If the given var is a collection
    		if ( TAO_MATCHING.Variable.isCollection (matchingVar)){

            	var found = false;
    		    // For each value contained by the matching var to map
                for (var varKey in matchingVar.value) {
                	
                    // If one match the current map value
                    if (matchingVar.value[varKey].match(this.value[mapKey]['key'])) {
                        mapEntriesFound.push(varKey);
                        if (!found){ // Stop at the first value found (IMS QTI Standard requirement)
                        	returnValue += this.value[mapKey]['value'];
                        	found = true;
                        }
                    }
                }
            }
    		//If the given var is a pair (also of class taoQTI_models_classes_Matching_Collection)
			try{
				if (matchingVar.match(this.value[mapKey]['key'])){
					mapEntriesFound.push(mapKey);
					returnValue += this.value[mapKey]['value'];
					break;
				}
			}catch(err){
				//if the elements is not of the same type
			}	
			
	    }
    	
    	// If a defaultValue has been set and it is different from zero
    	if (this.defaultValue != 0){
    		// If the given var is a collection
    		if(TAO_MATCHING.Variable.isCollection(matchingVar)){
    			// How many values have not been found * default value
	        	var delta = matchingVar.value.length - mapEntriesFound.length;
				var mapRes = delta * this.defaultValue;
	        	returnValue += mapRes;
    		}else if(!mapEntriesFound.length){
    			returnValue = this.defaultValue;
    		}else{
			}
    	}	
    	
    	if (this.lowerBound != null){
    		if (returnValue < this.lowerBound){
    			returnValue = this.lowerBound;
    		}
    	}
    	
    	if (this.upperBound != null){
    		if (returnValue > this.upperBound){
    			returnValue = this.upperBound;
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


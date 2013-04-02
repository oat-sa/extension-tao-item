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
 * point values to a target set of float values. When mapping 
 * containers the result is the sum of the mapped values from
 * the target set
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package TAO_MATCHING
 * 
 * @constructor 
 */
TAO_MATCHING.AreaMap = function (data) {
    // Set the value of the variable
    this.setValue (data);
};

TAO_MATCHING.AreaMap.prototype = {
    /**
     * This function looks up the value of a given Variable
     * that must be of type point, and transforms it 
     * using the associated areaMapping. The transformation 
     * is similar to map function of the Map class except that the 
     * points are tested against each area in turn. When mapping 
     * containers each area can be mapped once only.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return double
     */
    map : function (matchingVar) {
        var returnValue = 0.0;
        var mapEntriesFound = [];
                
        // for each map element, check if it is represented in the given variable
        for (var mapKey in this.value) {
            
            // Uniq point
            if (matchingVar instanceof TAO_MATCHING.Tuple) {
                if (this.value[mapKey].key.contains (matchingVar)){
                    returnValue = this.value[mapKey].value;
    				mapEntriesFound.push(mapKey);
                }
            } 
            // Collection of points
            else if (matchingVar instanceof TAO_MATCHING.List) {
            	var found = false;
                for (var varKey in matchingVar.value) {
                    // If one match the current map value
                    if (this.value[mapKey].key.contains (matchingVar.value[varKey])) {
                        mapEntriesFound.push(varKey);
                        if (!found){ // Stop at the first value found (IMS QTI Standart requirement)
                        	returnValue += this.value[mapKey]['value'];
                        	found = true;
                        }
                    }
                }
            }
        }
        
        // If a defaultValue has been set and it is different from zero
    	if (this.defaultValue != 0) {    		
    		// If the given var is a collection
    		if ( TAO_MATCHING.Variable.isCollection (matchingVar)){
    			// How many values have not been found * default value
	        	var delta = matchingVar.value.length - mapEntriesFound.length;
	        	returnValue += delta * this.defaultValue;
    		} else if (!mapEntriesFound.length) {
    			returnValue = this.defaultValue;
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
            this.value.push ({
                "value":data[i].value
                , "key":TAO_MATCHING.VariableFactory.create(data[i].key)
            });
        }
    }
};


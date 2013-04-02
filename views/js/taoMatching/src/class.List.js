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
 * List represents the collection list as managed by the the
 * tao matching api
 * 
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package TAO_MATCHING
 * @extends TAO_MATCHING.Collection
 * 
 * @constructor 
 * @param  array data data used to construct the tuple
 */
TAO_MATCHING.List = function (data) {
	// Call the parent constructor
	TAO_MATCHING.Collection.call (this);
	// Set the value of the variable
	this.setValue (data);
};

TAO_MATCHING.List.prototype = { 

    /**
     * Get the type of the variable
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    getType : function ()
    {
        return 'list';
    }

    /**
     * Get an element by its index. Return null if the element does not exist.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  int key
     * @return TAO_MATCHING.Variable
     */
    , getElement : function (key)
    {
        if (typeof this.value[key] != 'undefined'){
        	return this.value[key];
        }
        return null;
    }

    /**
     * Get the length of the list
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Tuple tuple
     * @return boolean
     */
    , length : function ()
    {
        return this.value.length;
    }

    /**
     * Match a list with another
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  List list
     * @return boolean
     */
    , match : function (list)
    {
		
		if (! (list instanceof TAO_MATCHING.List))
			throw new Error ('TAO_MATCHING.List.match an error occured : first argument expected type TAO_MATCHING.List, given : '+(typeof list));

        var returnValue = true;
        		
        // If the cardinality is not the same return false
        if (this.length() != list.length()){
        	return false;	
        }
        
        // Test if the both lists have the same content
		var checkedElts = [];
        for (var i=0; i<this.length(); i++) {
	        var tempResult = false;
			
	        for (var j=0; j<list.length(); j++) {
	        	if (this.getElement(i).getType () != list.getElement(j).getType()){
	        		throw 'TAO_MATCHING.List::match an error occured : types of the elements to match are not the same ['+ this.getElement(i).getType () +'] and ['+ list.getElement(j).getType() +']';
	        		returnValue = false;
	        	} 
	        	else if (this.getElement(i).match(list.getElement(j)) && $.inArray(j, checkedElts)===-1) {
					checkedElts.push(j);
	            	tempResult = true;
	                break;
	            } 
				else {
					// DOES NOT MATCH
				}
	        }
	        if (!tempResult){
	        	returnValue = false;
				break;	
			}
        }

        return returnValue;
    }

    /**
     * Set value of the list from an array of data. The array of data could be
     * array of Variables or an array of "base type" variables
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return mixed
     */
    , setValue : function (data)
    {
    	this.value = [];
    	for (var i in data ){
    	    // IF the variable the content value is yet a MatchingVariable, add it to the list
    	    if (TAO_MATCHING.VariableFactory.isMatchingVariable (data[i])){
    	        this.value.push(data[i]);
    	    }
    	    // ELSE if the value content is BaseTypeVariable valid value, create it and add it to the list
    	    else {
    	        //console.log (data[i]);
                //if (TAO_MATCHING.BaseTypeVariable.isValidValue (data[i])){
                    this.value.push (TAO_MATCHING.VariableFactory.create (data[i]));
                //} else {
                //    throw new Error ('TAO_MATCHING.List::setValue an error occured : types of the element is not allowed');
                //}
    	    }
    	}
    }
    
    /**
     * Export the variable in jSon format.
     * {
     *     "identifier":"myVariableIdentifier",
     *     "value": [
     *         "myVar1"
     *         "myVar2"
     *     ]
     * }
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    , toJSon : function ()
    {
        var returnValue = Array ();
        
        for (var i in this.value) {
            var tmpValue = this.value[i];
            if (TAO_MATCHING.VariableFactory.isMatchingVariable(tmpValue)){
                tmpValue = tmpValue.toJSon();
            }
            returnValue [i] = tmpValue
        }
        
        return returnValue;
    }
};

// Extend the class with its parent properties
TAO_MATCHING.List.prototype = $.extend ({}, TAO_MATCHING.Collection.prototype, TAO_MATCHING.List.prototype);

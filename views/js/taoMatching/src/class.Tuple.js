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
 * tuple represents the collection tuple as managed by the the
 * tao matching api
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package TAO_MATCHING
 * @extends TAO_MATCHING.Collection
 * 
 * @constructor 
 * @param array data data used to construct the tuple
 */
TAO_MATCHING.Tuple = function (data) {
	// Call the parent constructor
	TAO_MATCHING.Collection.call (this);
    // The length of the tuple
    this.count = 0;
    // The tuple's values
    this.value = {};
	// Set the value of the variable
	this.setValue (data);
};

TAO_MATCHING.Tuple.prototype = { 

    /**
     * Get the type of the variable
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    getType : function ()
    {
        return 'tuple';
    }

	/**
	 * Get an element by its key. Return null if the element does not exist.
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  string key
	 * @return {TAO_MATCHING.Variable}
	 */
    , getElement : function (key)
    {
        if (typeof this.value[key] != 'undefined'){
        	return this.value[key];
        }
        return null;
    }

    /**
     * Get the length of the tuple
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return integer
     */
    , length : function ()
    {
        return this.count;
    }

    /**
     * Match a tuple with another
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  {TAO_MATCHING.Tuple} tuple
     * @return boolean
     */
    , match : function (tuple)
    {
		
		if (! (tuple instanceof TAO_MATCHING.Tuple))
			throw new Error ('TAO_MATCHING.Tuple.match an error occured : first argument expected type TAO_MATCHING.Tuple, given : '+(typeof matchingVar));

        var returnValue = true;
        		
        // If the cardinality is not the same return false
        if (this.length() != tuple.length()){
        	return false;	
        }
        
        // Test if the both lists have the same content
        for (var key in this.value) {
	        var compareElt = tuple.getElement (key);
			
        	if (compareElt == null){
        		returnValue = false;
        		break;
        	} else if (this.value[key].getType () != compareElt.getType()){
        		throw new Error ('TAO_MATCHING.Tuple::match an error occured : types of the elements to match are not the same ['+ elt.getType () +'] and ['+ compareElt.getType() +']');
        		returnValue = false;
        		break;
        	} else if (!this.value[key].match (compareElt)){
        		returnValue = false;
        		break;
        	} else {
        		returnValue = true;
        	}
        }

        return returnValue;
    }

    /**
     * Set value of the tuple from an array of data. The array of data could be
     * array of Variables or an array of "base type" variables
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     */
    , setValue : function (data)
    {
    	this.value = {};
    	for (var key in data) {
    	    // IF the variable the content value is yet a MatchingVariable, add it to the tuple
            if (TAO_MATCHING.VariableFactory.isMatchingVariable (data[key])){
                this.value[key] = data[key];
            // ELSE if the value content is BaseTypeVariable valid value, create it and add it to the tuple
            } else {
                //if (TAO_MATCHING.BaseTypeVariable.isValidValue (data[key])){
                    this.value[key] = TAO_MATCHING.VariableFactory.create (data[key]);
                //} else {
                //    throw new Error ('TAO_MATCHING.Tuple::setValue an error occured : types of the element is not allowed');
                //}
            }
            this.count ++;
    	}
    }
    
    /**
     * Export the variable in jSon format.
     * <pre>
     * {
     *     "identifier":"myVariableIdentifier",
     *     "value": {
     *         "0" : "myVar1"
     *         , "1" : "myVar2"
     *     }
     * }
     * </pre>
     *
     * @return jSon
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    , toJSon : function ()
    {        
        var returnValue = {};
        
        for (var i in this.value) {
            var tmpValue = this.value[i];
            
            if (TAO_MATCHING.VariableFactory.isMatchingVariable(tmpValue)){
                tmpValue = tmpValue.toJSon();
            }
            returnValue[i] = tmpValue;
        }
        
        return returnValue;
    }
};

// Extend the class with its parent properties
TAO_MATCHING.Tuple.prototype = $.extend ({}, TAO_MATCHING.Collection.prototype, TAO_MATCHING.Tuple.prototype);

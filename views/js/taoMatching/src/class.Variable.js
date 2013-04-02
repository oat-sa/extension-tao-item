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
 * Variable is an abstract class which is the representation 
 * of all the variables managed by the system
 *
 * @abstract
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @todo is* function are still usefull ? Implementation are not coherent between the functions
 * @package TAO_MATCHING
 * 
 * @constructor 
 */
TAO_MATCHING.Variable = function () {
	 /**
     * Short description of attribute value
     *
     * @access protected
     * @var object
     */
    this.value = null;
};

TAO_MATCHING.Variable.prototype = {
    /**
     * Get the type of the variable
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    getType : function ()
    {
		// @abstract
    }

	/**
	 * Get the value of the variable
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 */
    , getValue : function ()
    {
        return this.value;
    }

    /**
     * Check if the variable is equal to another
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var The variable to compare
     * @return boolean
     */
    , equal : function (matchingVar)
    {
		// @abstract
    }
	
    /**
     * check if the variable is null
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    , isNull : function ()
    {
		// @abstract
	}
	
    /**
     * Match a variable to another. This function does not match a 
     * strict equality. In the case of array the match function will 
     * check it the two arrays have the same value.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return boolean
     * @abstract
     */
    , match : function (matchingVar)
    {
		// @abstract
    }
	
    /**
     * Set the value of the variable
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data The value to set
     * @return mixed
     */
    , setValue : function (data)
    {
        // @abstract
    }
	
    /**
     * Export the variable in jSon format.
     * <pre>
     * {
     *     "identifier":"myVariableIdentifier",
     *     "value":true
     * }
     * </pre>
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    , toJSon : function () { 
        // abstract        
    }
};

/**
 * Check if a value is a scalar value
 * @param {object} data Data to check
 * @return boolean
 * @static
 */
TAO_MATCHING.Variable.isScalar = function (data) {
    var returnValue = false;
    switch (typeof data){
        case "boolean":
        case "number":
        case "string":
            returnValue = true;
    }
    return returnValue;
};

/**
 * Check if a value is a collection value
 * @param {object} data Data to check
 * @return boolean
 * @static
 */
TAO_MATCHING.Variable.isCollection = function (data) {
    var returnValue = false;
    
    if (typeof data.getType != 'undefined') {
        switch (data.getType()){
            case "list":
            case "tuple":
                returnValue = true;
        }
    }
    
    return returnValue;
};

/**
 * Check if a value is a list value
 * @param {object} data Data to check
 * @return boolean
 * @static
 */
TAO_MATCHING.Variable.isList = function (data) {
    var returnValue = false;
    
    if (typeof data.getType != 'undefined') {
        if (data.getType() == "list") {
            returnValue = true;
        }
    }
    
    return returnValue;
};

/**
 * Check if a value is a tuple value
 * @param {object} data Data to check
 * @return boolean
 * @static
 */
TAO_MATCHING.Variable.isTuple = function (data) {
    var returnValue = false;
    
    if (typeof data.getType != 'undefined') {
        if (data.getType() == "tuple") {
            returnValue = true;
        }
    }
    
    return returnValue;
};


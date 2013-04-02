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
 * BaseTypeVariable represents the scalar variables
 * managed by the system.
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package TAO_MATCHING
 * @extends TAO_MATCHING.Variable
 * 
 * @constructor 
 * @param object value Value of the variable
 */
TAO_MATCHING.BaseTypeVariable = function(data){
	// Call the parent constructor
	TAO_MATCHING.Variable.call (this);
	// Set the value of the variable
	this.setValue (data);
}

TAO_MATCHING.BaseTypeVariable.prototype = {
	    /**
	     * Get the type of the variable
	     *
	     * @access public
	     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	     * @return string
	     */
    getType : function ()
    {
        return (typeof this.value);
    }

    /**
     * Compare the variable with an other
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var Variable to compare
     * @return boolean
     */
    , equal : function (matchingVar)
    {
		if (! (matchingVar instanceof TAO_MATCHING.BaseTypeVariable))
			throw new Error ('TAO_MATCHING.BaseTypeVariable.equal an error occured : first argument expected type TAO_MATCHING.BaseTypeVariable, given : '+(typeof matchingVar));
		
        var returnValue = false;

        if (this.getType() != matchingVar.getType()){
        	returnValue = false;
        } else {
        	returnValue = (this.getValue() == matchingVar.getValue());	
        }
        		
        return returnValue;
    }

    /**
     * Short description of method isNull
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     * @see Variable
     */
    , isNull : function ()
    {
        return (this.value == null);
    }    
	
	/**
     * Short description of method match
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return boolean
     */
    , match : function (matchingVar)
    {
		if (! (matchingVar instanceof TAO_MATCHING.BaseTypeVariable))
			throw new Error ('TAO_MATCHING.BaseTypeVariable.match an error occured : first argument expected type TAO_MATCHING.BaseTypeVariable, given : '+(typeof matchingVar));
		
		return this.equal(matchingVar);
    }

    /**
     * Short description of method setValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  value
     * @return mixed
     */
    , setValue : function (data)
    {
        this.value = data;
    }
    
    /**
     * Short description of method toJSon
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    , toJSon : function ()
    {
        return this.getValue();
    }
       
    /**
     * Short description of method isNumerical
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    , isNumerical : function ()
    {
        var returnValue = false;
        
        if (this.getValue() == null){
            returnValue = true;
        } else {
            switch (this.getType()){
                case 'number':
                    returnValue = true;
            }
        }        

        return returnValue;
    }
    
    /**
     * Short description of method isBoolean
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    , isBoolean : function ()
    {
        var returnValue = false;
        
        if (this.getValue() == null){
            returnValue = true;
        } else {
            switch (this.getType()){
                case 'boolean':
                    returnValue = true;
            }
        }        

        return returnValue;
    }
    
};

/**
 * isValidValue function provide a way to test if a value could be BaseTypeVariable value type compliant
 *
 * @access public
 * @static
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @param  data
 * @return boolean
 */
TAO_MATCHING.BaseTypeVariable.isValidValue = function (data)
{
    var returnValue = false;
    
    // If the data is null
    if (data == null){
        returnValue = true;
    }
    else {
        // If the data has a valid type
        switch (typeof (data)){
            case 'boolean':
            case 'number':
            case 'string':
                returnValue = true;
        }    
    }

    return returnValue;
};

// Extend the class with its parent properties
TAO_MATCHING.BaseTypeVariable.prototype = $.extend ({}, TAO_MATCHING.Variable.prototype, TAO_MATCHING.BaseTypeVariable.prototype);

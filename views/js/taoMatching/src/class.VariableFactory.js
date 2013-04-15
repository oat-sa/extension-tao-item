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
 * The class variable factory provide to developpers a set of usefull function
 * the variable creation process
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package TAO_MATCHING
 */
TAO_MATCHING.VariableFactory = {

    /**
     * Create a variable functions of the given data.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data Data of the variable (the value)
     * @param  string type The type is optional, if it is not defined the data will
define the type of the variable
     * @return TAO_MATCHING.Variable
     */
	create : function (data, pType) {
		var returnValue = null;
		var type = null;
		
		// The type has been defined manually
		if (typeof pType != 'undefined' && pType != null) {
            type = pType;
		}
		else {
            type = TAO_MATCHING.VariableFactory.getType(data);
		}

		// Create the variable according to its type
		switch (type) {
			// Collection Tuple : our standard defines an JSON Object as a tuple
			case 'tuple':
			case 'point':
				returnValue = new TAO_MATCHING.Tuple (data);
				break;
				
			// Collection List : our standard defines a JSON Array as a list
			case 'list':
				returnValue = new TAO_MATCHING.List (data);
				break;
			
			// Native language variable types			
			case 'boolean':
			case 'number':
			case 'string':
			case 'NULL':
				returnValue = new TAO_MATCHING.BaseTypeVariable (data);
				break;

            // Shape       
            case 'circle':
            case 'ellipse':
                returnValue = new TAO_MATCHING.Ellipse (data);
                break;
            case 'rect':
            case 'poly':
                returnValue = new TAO_MATCHING.Poly (data);
                break;
		
		    // The type is not supported by the matching API
			default:
				throw new Error ('TAO_MATCHING.VariableFactory::create variable type unknown '+type+' for '+data);
		}
		
        return returnValue;
	}
	
	/** 
	 * Define the type of data from the data value
	 * @params {object} data Get the type of these data
	 * @return string
	 * @private
	 */
	, getType : function(data){
	    var returnValue = null;
	    
	    // We get an object as data
        if (typeof data == 'object') {
            // If the data is null, we create a basic type variable with null value
            if (data == null) {
                returnValue = 'NULL';
            }
            // If a type as been declared
            else if (data.type!=undefined){
                returnValue  = data.type;
            }
            // If the data is an array we create a list variable
            else if (($.isArray(data))) {
                returnValue = 'list';
            } 
            // If the data is an object, our format define this data as a tuple
            else {
                returnValue = 'tuple';
            }
        }
        else {
            returnValue = typeof data; 
        }
        
        return returnValue;
	}

    /**
     * Check if the variable is a Matching Variable (deal with javascript)
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param object data Check if the given data is a variable of the matching api 
     * @return boolean
     */	
	, isMatchingVariable : function (data){
	    var returnValue = false;
	    
	    if (data instanceof TAO_MATCHING.BaseTypeVariable
            || data instanceof TAO_MATCHING.Tuple
            || data instanceof TAO_MATCHING.List)
        {
            returnValue = true;
        }
	    
	    return returnValue;
	}
    
    /**
     * Convert data in numeric BaseTypeVariable.
     * If the data is not a valid base type value return null.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data Data to convert
     * @return TAO_MATCHING.BaseTypeVariable
     */
     , toNumericBaseType : function (data) {
        var returnValue = null;
        
        // IF the first expression is not a BaseTypeVariable try to create it
        if (!(data instanceof TAO_MATCHING.BaseTypeVariable)){
            if (TAO_MATCHING.BaseTypeVariable.isValidValue (data)) {
                var matchingVar = new TAO_MATCHING.BaseTypeVariable (data);
                if (matchingVar.isNumerical ()){
                    returnValue = matchingVar;
                }
            }
        } else {
            if (data.isNumerical()){
                returnValue = data;
            }
        }
        
        return returnValue;
     }
    
     /**
      * Convert data in boolean BaseTypeVariable.
      * If the data is not a valid base type value return null.
      *
      * @access public
      * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
      * @param  data Data to convert
      * @return  TAO_MATCHING.BaseTypeVariable
      */
     , toBooleanBaseType : function (data) {
        var returnValue = null;
        
        // IF the first expression is not a BaseTypeVariable try to create it
        if (!(data instanceof TAO_MATCHING.BaseTypeVariable)){
            if (TAO_MATCHING.BaseTypeVariable.isValidValue (data)) {
                var matchingVar = new TAO_MATCHING.BaseTypeVariable (data);
                if (matchingVar.isBoolean ()){
                    returnValue = matchingVar;
                }
            }
        } else {
            if (data.isBoolean()){
                returnValue = data;
            }
        }
        
        return returnValue;
     }
}

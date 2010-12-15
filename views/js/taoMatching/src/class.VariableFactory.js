TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

TAO_MATCHING.VariableFactory = {
    /**
     * Short description of method create
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @param  string type
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
            type = TAO_MATCHING.VariableFactory.getType (data);
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
	 * Define the type of a given JSON Variable Object
	 * @params {}
	 * @return
	 */
	, getType : function (data){
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
     * @param  data
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
     * @param  data
     * @return taoItems_models_classes_Matching_BaseTypeVariable
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
     * Short description of method toBooleanBaseType
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return taoItems_models_classes_Matching_BaseTypeVariable
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

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
				returnValue = new TAO_MATCHING.Tuple (data);
				break;
				
			// Collection List : our standard defines a JSON Array as a list
			case 'list':
				returnValue = new TAO_MATCHING.List (data);
				break;
			
			// NAtive language variable types			
			case 'boolean':
			case 'number':
			case 'string':
			case 'NULL':
				returnValue = new TAO_MATCHING.BaseTypeVariable (data);
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
     * Short description of method isValidBaseType
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return boolean
     */
	, isValidBaseType : function(elt){
	    var returnValue = false;
	    
	    switch (typeof (elt)){
            case 'boolean':
            case 'number':
            case 'string':
                returnValue = true;
	    }
	    
	    return returnValue;
	}
}

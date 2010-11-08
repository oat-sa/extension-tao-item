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
	    console.log (data);
		var returnValue = null;
		var type = null;
		
		// The type has been defined manually
		if (typeof pType == 'undefined' || pType == null) {
			// We get an object as data
			if (typeof data == 'object') {
				// If the data is null, we create a basic type variable with null value
				if (data == null) {
					type = 'NULL';
				}
				// If the data is an array we create a list variable
				else if (($.isArray(data))) {
					type = 'list';
				} 
				// If the data is an object, our format define this data as a tuple
				else {
					type = 'tuple';
				}
			}
			else {
				type = typeof data;	
			}	
		}
		else {
			type = pType;
		}
		
		// Create the variable according to its type
		switch (type) {
			//Collection Tuple : our standard define an object as a tuple
			case 'tuple':
				returnValue = new TAO_MATCHING.Tuple (data);
				break;
				
			//Collection List
			case 'list':
				returnValue = new TAO_MATCHING.List (data);
				break;
						
			case 'boolean':
			case 'number':
			case 'string':
			case 'NULL':
				returnValue = new TAO_MATCHING.BaseTypeVariable (data);
				break;
				
			default:
				throw new Error ('TAO_MATCHING.VariableFactory::create variable type unknown '+type+' for '+data);
		}
		
        return returnValue;
	}
	
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

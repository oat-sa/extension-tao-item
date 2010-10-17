TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};
TAO_MATCHING.VARIABLE = typeof TAO_MATCHING.VARIABLE != 'undefined' ? TAO_MATCHING.VARIABLE : {};

TAO_MATCHING.VARIABLE.VariableFactory = function(){
}

TAO_MATCHING.VARIABLE.VariableFactory.prototype = {
    /**
     * Short description of method create
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @param  string type
     * @return TAO_MATCHING.VARIABLE.Variable
     */	
	create : function (data, pType) {
		var returnValue = null;
    	var varValue = data;
		var type = null;
		
		// The type has been defined manually
		if (typeof pType == 'undefined'){	
			type = typeof data;	
		}
		
		// Create the variable according to its type
		switch (type) {
			//Collection Tuple : our standard define an object as a tuple
			case 'object':
				returnValue = new TAO_MATCHING.VARIABLE.Tuple (data);
				break;
				
			//Collection List
			case 'array':
				returnValue = new TAO_MATCHING.VARIABLE.List (data);
				break;
						
			case 'boolean':
			case 'numeric':
			case 'string':
			case 'NULL':
				returnValue = new TAO_MATCHING.VARIABLE.BaseTypeVariable (data);
				break;
				
			default:
				throw new Exception ('TAO_MATCHING.VARIABLE.VariableFactory::create variable type unknown '+type+' for '+varValue);
		}
		
        return returnValue;
	}
}

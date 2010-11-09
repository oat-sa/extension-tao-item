TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

TAO_MATCHING.Tuple = function (data) {
	// Call the parent constructor
	TAO_MATCHING.Collection.call (this);
	// Set the value of the variable
	this.setValue (data);
};

TAO_MATCHING.Tuple.prototype = { 

    /**
     * Short description of method getType
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
     * Get an element by its index. Return null if the element does not exist.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  int key
     * @return taoItems_models_classes_Matching_Variable
     */
    , getElement : function (key)
    {
        if (typeof this.value[key] != 'undefined'){
        	return this.value[key];
        }
        return null;
    }

    /**
     * Short description of method match
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Tuple tuple
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
     * Short description of method setValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return mixed
     */
    , setValue : function (data)
    {
    	this.value = [];
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
    	}
    }
};

// Extend the class with its parent properties
TAO_MATCHING.Tuple.prototype = $.extend ({}, TAO_MATCHING.Collection.prototype, TAO_MATCHING.Tuple.prototype);

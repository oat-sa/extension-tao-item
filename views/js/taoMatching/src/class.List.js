TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

TAO_MATCHING.List = function (data) {
	// Call the parent constructor
	TAO_MATCHING.Collection.call (this);
	// Set the value of the variable
	this.setValue (data);
};

TAO_MATCHING.List.prototype = { 

    /**
     * Short description of method getType
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
        for (var i=0; i<this.length(); i++) {
	        var tempResult = false;
			
	        for (var j=0; j<list.length(); j++) {
	        	if (this.getElement(i).getType () != list.getElement(j).getType()){
	        		throw new Exception ('TAO_MATCHING.List::match an error occured : types of the elements to match are not the same ['+ this.getElement(i).getType () +'] and ['+ list.getElement(j).getType() +']');
	        		returnValue = false;
	        	} 
	        	else if (this.getElement(i).match(list.getElement(j))) {
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
};

// Extend the class with its parent properties
TAO_MATCHING.List.prototype = $.extend ({}, TAO_MATCHING.Collection.prototype, TAO_MATCHING.List.prototype);

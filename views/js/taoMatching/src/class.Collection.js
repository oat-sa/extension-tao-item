TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

TAO_MATCHING.Collection = function (data) {
	// Call the parent constructor
	TAO_MATCHING.Variable.call (this);
};

TAO_MATCHING.Collection.prototype = {

	/**
     * Short description of method contain
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return string
     */
    contain : function (matchingVar)
    {
		var returnValue = null;
		
//		if (! (matchingVar instanceof TAO_MATCHING.List) || ! (matchingVar instanceof TAO_MATCHING.Tuple) || ! (matchingVar instanceof TAO_MATCHING.BaseTypeVariable))
//			throw new Error ('TAO_MATCHING.Collection.contain an error occured : first argument expected type TAO_MATCHING.Variable, given : '+(typeof matchingVar));

     	for (var key in this.value) {
			// Different type
			if (matchingVar.getType() != this.value[key].getType()){
				returnValue = null;
				break;
			}
			
			if (matchingVar.match (this.value[key])){
				returnValue = key;
				break;
			}
        }

        return returnValue;
    }

    /**
     * Short description of method isNull
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    , isNull : function ()
    {
		if (this.value == null)
			return true;
		if (this.value.length == 0)
        	return true;
		return false;
    }

    /**
     * Short description of method length
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_Session_int
     */
    , length : function ()
    {
        if (this.isNull())
			return 0;
		else
			return this.value.length;
    }   

};

// Extend the class with its parent properties
TAO_MATCHING.Collection.prototype = $.extend ({}, TAO_MATCHING.Variable.prototype, TAO_MATCHING.Collection.prototype);

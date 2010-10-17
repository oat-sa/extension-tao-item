TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};
TAO_MATCHING.VARIABLE = typeof TAO_MATCHING.VARIABLE != 'undefined' ? TAO_MATCHING.VARIABLE : {};

TAO_MATCHING.VARIABLE.Variable = function () {
	 /**
     * Short description of attribute value
     *
     * @access protected
     * @var object
     */
    this.value = null;
};

TAO_MATCHING.VARIABLE.Variable.prototype = {
    /**
     * Short description of method getType
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    getType : function ()
    {
		// @abstract
    }

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    , getValue : function ()
    {
        return this.value;
    }

    /**
     * Short description of method equal
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return boolean
     */
    , equal : function (matchingVar)
    {
		if (! (matchingVar instanceof TAO_MATCHING.VARIABLE.Variable))
			throw new Error ('TAO_MATCHING.VARIABLE.Variable.equal an error occured : first argument expected type TAO_MATCHING.VARIABLE.Variable, given : '+(typeof matchingVar));
		
        var returnValue = false;

        if (this.getType() != matchingVar.getType()){
        	returnValue = false;
        } else {
        	returnValue = this.getValue() == matchingVar.getValue();	
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
		// @abstract
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
		if (! (matchingVar instanceof TAO_MATCHING.VARIABLE.Variable))
			throw new Error ('TAO_MATCHING.VARIABLE.Variable.match an error occured : first argument expected type TAO_MATCHING.VARIABLE.Variable, given : '+(typeof matchingVar));
		
		return this.match(matchingVar);
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
        // @abstract
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
};

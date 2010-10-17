TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};
TAO_MATCHING.VARIABLE = typeof TAO_MATCHING.VARIABLE != 'undefined' ? TAO_MATCHING.VARIABLE : {};

TAO_MATCHING.VARIABLE.BaseTypeVariable = function(data){
	 /**
     * Short description of attribute value
     *
     * @access protected
     * @var object
     */
    this.value = null;
	
	this.setValue (data);
}

TAO_MATCHING.VARIABLE.BaseTypeVariable.prototype = {
	/**
     * Short description of method getType
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
     * Short description of method isNull
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    , isNull : function ()
    {
        return (this.value == null);
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
};

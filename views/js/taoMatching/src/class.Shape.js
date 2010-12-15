TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

TAO_MATCHING.Shape = function(type){
    /**
     * Type of the shape
     * @type string
     * @protected
     */
    this.type = null;
    
    this.type = type;
}

TAO_MATCHING.Shape.prototype = {
    /**
     * Short description of method getType
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    getType : function ()
    {
        return (this.type);
    }

    /**
     * Short description of method isNull
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     * @abstract
     */
    , isNull : function () { }
    
};

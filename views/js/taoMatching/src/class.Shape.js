TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

/**
 * @class
 * 
 * Shape represents the different shapres managed by the
 * system.
 *
 * @abstract
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package TAO_MATCHING
 * @todo Check if the function setTypes is deprecated
 * 
 * @constructor 
 * @param {array} object Array of points
 */
TAO_MATCHING.Shape = function(type){
    /**
     * Type of the shape
     *
     * @access protected
     * @var string
     */
    this.type = null;
    
    // treat param type
    this.type = type;
}

TAO_MATCHING.Shape.prototype = {
    /**
     * Get the type of the shape
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
     * @todo check if the function is deprecated
     * @abstract
     */
    , isNull : function () { }
    
};

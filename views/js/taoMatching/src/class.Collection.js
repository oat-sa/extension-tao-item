TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

/**
 * @class
 * 
 * Collection is an abstract class which represents
 * the variables "collection".
 * 
 * @abstract
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package TAO_MATCHING
 * @extends TAO_MATCHING.Variable
 * 
 * @constructor 
 */
TAO_MATCHING.Collection = function (data) {
	// Call the parent constructor
	TAO_MATCHING.Variable.call (this);
};

TAO_MATCHING.Collection.prototype = {
    /**
     * Check if the collection contains the given element.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @param  array options options.needleType {String} Define if the script has to go through the needle or it must treat it as  scalar variable
     * @return boolean
     */
    contains : function (matchingVar, options)
    {    
		var returnValue = null;
		var needleType = 'collection';

		if (typeof options != 'undefined'){
		    if (typeof options.needleType != 'undefined'){
                needleType = options.needleType;
            }
		}
		
		// If the needle is a Tuple
        if (TAO_MATCHING.Variable.isTuple (matchingVar) && needleType == 'collection') {
            returnValue = false;
            for (var key in matchingVar.value) {
                if (this.value[key].match(matchingVar.value[key])){
                    returnValue = true;
                }else{
                    returnValue = false;
                    break;
                }
            }
        }
        // Else if the needle is a List
        else if (TAO_MATCHING.Variable.isList (matchingVar) && needleType == 'collection') {
            returnValue = false;
            for (var key in matchingVar.value) {
                if (this.contains (matchingVar.value[key])){
                    returnValue = true;
                } else {
                    returnValue = false;
                    break;
                }
            }
        } 
        // Else we check if the value is include is the current collection
        else {
            returnValue = false;
            
            for (var key in this.value) {
                // If the needle is not of the same type that an item of the collection (escape)
                if (matchingVar.getType() != this.value[key].getType()){
                    returnValue = false;
                    break;
                } 
                // Else we check if the needle match the current item
                else if (matchingVar.match (this.value[key])) {
                    returnValue = true;
                    break;
                } else {
                    returnValue = false;
                }
            }
        }
        
        
        return returnValue;
    }
	
	/**
	 * check if the variable is null
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @return boolean
	 */
    , isNull : function ()
    {
		if (this.value == null)
			return true;
		if (this.length() == 0)
        	return true;
		return false;
    }

};

// Extend the class with its parent properties
TAO_MATCHING.Collection.prototype = $.extend ({}, TAO_MATCHING.Variable.prototype, TAO_MATCHING.Collection.prototype);

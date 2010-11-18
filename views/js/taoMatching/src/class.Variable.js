TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

/**
 * Short description of class taoItems_models_classes_Matching_Variable
 *
 * @abstract
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
TAO_MATCHING.Variable = function () {
	 /**
     * Short description of attribute value
     *
     * @access protected
     * @var object
     */
    this.value = null;
};

TAO_MATCHING.Variable.prototype = {
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
		// @abstract
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
		// @abstract
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
    , toJSon : function () { 
        // abstract        
    }
};

/**
 * Check if a value is a scalar variable
 */
TAO_MATCHING.Variable.isScalar = function (data) {
    var returnValue = false;
    switch (typeof data){
        case "boolean":
        case "number":
        case "string":
            returnValue = true;
    }
    return returnValue;
};

/**
 * Check if a data is a collection
 */
TAO_MATCHING.Variable.isCollection = function (data) {
    var returnValue = false;
    
    if (typeof data.getType != 'undefined') {
        switch (data.getType()){
            case "list":
            case "tuple":
                returnValue = true;
        }
    }
    
    return returnValue;
};

/**
 * Check if a data is a list
 */
TAO_MATCHING.Variable.isList = function (data) {
    var returnValue = false;
    
    if (typeof data.getType != 'undefined') {
        if (data.getType() == "list") {
            returnValue = true;
        }
    }
    
    return returnValue;
};

/**
 * Check if a data is a tuple
 */
TAO_MATCHING.Variable.isTuple = function (data) {
    var returnValue = false;
    
    if (typeof data.getType != 'undefined') {
        if (data.getType() == "tuple") {
            returnValue = true;
        }
    }
    
    return returnValue;
};


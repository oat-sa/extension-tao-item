TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

TAO_MATCHING.Matching = function () {
    /**
     * Short description of attribute corrects
     *
     * @access protected
     * @var Variable
     */
    this.corrects = [];
	
    /**
     * Short description of attribute maps
     *
     * @access protected
     * @var Map
     */
    this.maps = [];

    /**
     * Short description of attribute outcomes
     *
     * @access protected
     * @var Variable
     */
    this.outcomes = [];

    /**
     * Short description of attribute responses
     *
     * @access protected
     * @var Variable
     */
    this.responses = [];

    /**
     * Short description of attribute rule
     *
     * @access protected
     * @var string
     */
    this.rule = '';

    /**
     * Short description of attribute whiteFunctionsList
     *
     * @access public
     * @var array
     */
    this.whiteFunctionsList = {
		'and'				:{'mappedFunction':'andExpression'}
		, 'equal'			:{}
		, 'if'				:{'jsFunction' : true}
		, 'isNull'			:{}
		, 'getCorrect'		:{}
		, 'getMap'			:{}
		, 'getResponse'		:{}
		, 'mapResponse'		:{}
		, 'match'			:{}
		, 'setOutcomeValue'	:{}	
	};	
}

TAO_MATCHING.Matching.prototype = {
    /**
     * Eval the stored response processing rule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    evaluate : function ()
    {		
		with (this){
			eval (getRule());	
		}
    }

	/**
     * Get a correct variable, return null if the variable does not exist
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_Matching_Variable
     */
    , getCorrect : function (id)
    {
        var returnValue = null;

        if (typeof (this.corrects[id]) != 'undefined')
        	returnValue = this.corrects[id];

        return returnValue;
    }
	
    /**
     * Short description of method getJSonOutcomes
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    , getJSonOutcomes : function ()
    {
        var returnValue = Array ();
        
        for (var key in this.outcomes){
        	returnValue[key] = [];
        	returnValue[key]["identifier"] = key;
        	returnValue[key]["value"] = this.outcomes[key].toJSon();
        }

        return returnValue;
    }
    /**
     * Short description of method getMap
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_Matching_Map
     */
    , getMap : function (id)
    {
        var returnValue = null;

        if (typeof (this.maps[id]) != 'undefined')
        	returnValue = this.maps[id];

        return returnValue;
    }

    /**
     * Short description of method getOutcome
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_Matching_Variable
     */
    , getOutcome : function (id)
    {
        var returnValue = null;

        if (typeof (this.outcomes[id]) != 'undefined')
        	returnValue = this.outcomes[id];

        return returnValue;
    }

    /**
     * Short description of method getResponse
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_Matching_Variable
     */
    , getResponse : function (id)
    {
        var returnValue = null;

        if (typeof (this.responses[id]) != 'undefined')
        	returnValue = this.responses[id];

        return returnValue;
    }

	/**
     * Short description of method getRule
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    , getRule : function ()
    {
        return this.rule;
    }

    /**
     * Short description of method isNull
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return boolean
     */
    , isNull : function (matchingVar)
    {
        return matchingVar.isNull();
    }

 	/**
     * Short description of method mapResponse
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Map map
     * @param  Variable expr
     * @return double
     */
    , mapResponse : function (map, matchingVar)
    {
		if (! (map instanceof TAO_MATCHING.Map) )
			throw new Error ('TAO_MATCHING.Matching::mapResponse an error occured : first argument expected type TAO_MATCHING.Map, given : '+(typeof map));

        return map.map (matchingVar);
    }

    /**
     * The match operator takes two sub-expressions which must both have the
     * type and cardinality. The result is a single boolean with a value of true
     * the two expressions represent the same value and false if they do not.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    , match : function (expr1, expr2)
    {
        var returnValue = false;
                
        if (typeof (expr1) == 'undefined')
        	throw new Exception ("TAO_MATCHING.Matching::match error : the first argument does not exist");
        if (typeof (expr2) == 'undefined')
        	throw new Exception ("TAO_MATCHING.Matching::match error : the second argument does not exist");

        if (expr1.getType() != expr2.getType()) { 
        	returnValue = false;
    	} else {
        	returnValue = expr1.match(expr2);
        }
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000291D end

        return returnValue;
    }

	/**
     * Set the correct variables of the item
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @return mixed
     */
    , setCorrects : function (data)
    {
    	if (! $.isArray (data))
    		throw new Error ('TAO_MATCHING.Matching::setCorrects is waiting on an array, a '+ (typeof data) +' is given');

		for (var key in data) {
			try {
				if (typeof this.corrects[data[key].identifier] != 'undefined')
					throw new Error ('TAO_MATCHING.Matching::setCorrects a correct variable with the identifier '+ data[key].identifier +' exists yet');
				var matchingVar = TAO_MATCHING.VariableFactory.create (data[key].value);
				this.corrects[data[key].identifier] = matchingVar;
			} 
			catch (e) {
				throw e;
			}
		}
    }
	
    /**
     * Set the map variables of the item
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return mixed
     */
    , setMaps : function (data)
    {
		if (! $.isArray (data))
    		throw new Error ('TAO_MATCHING.Matching::setMaps is waiting on an array, a '+ (typeof data) +' is given');

		for (var key in data) {
			try {
				if (typeof this.maps[data[key].identifier] != 'undefined')
					throw new Error ('TAO_MATCHING.Matching::setMaps a correct variable with the identifier '+ data[key].identifier +' exists yet');
				var matchingVar = new TAO_MATCHING.Map (data[key].value);
				this.maps[data[key].identifier] = matchingVar;
			} 
			catch (e) {
				throw e;
			}
		}
    }
	
	/**
     * Set the outcome variables of the item
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @return mixed
     */
    , setOutcomes : function (data)
    {
    	if (! $.isArray (data))
    		throw new Error ('TAO_MATCHING.Matching::setOutcomes is waiting on an array, a '+ (typeof data) +' is given');

		for (var key in data) {
			try {
				if (typeof this.outcomes[data[key].identifier] != 'undefined')
					throw new Error ('TAO_MATCHING.Matching::setOutcomes a correct variable with the identifier '+ data[key].identifier +' exists yet');
				
				var outcomeDefaultValue = typeof (data[key].value) != 'undefined' ? data[key].value : null;
				var matchingVar = TAO_MATCHING.VariableFactory.create (outcomeDefaultValue);
				this.outcomes[data[key].identifier] = matchingVar;
			} 
			catch (e) {
				throw e;
			}
		}
    }

    /**
     * Set the value of an outcome variable
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @param  value
     * @return mixed
     */
    , setOutcomeValue : function (id, value)
	{
        var outcome = this.getOutcome (id);
        if(outcome == null)
        	throw new Exception ('TAO_MATCHING.Matching::setOutcomeValue error : the outcome value '+id+' does not exist');
        outcome.setValue (value);
    }

	/**
     * Set the response variables of the item
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @return mixed
     */
    , setResponses : function (data)
    {
    	if (! $.isArray (data))
    		throw new Error ('TAO_MATCHING.Matching::setResponses is waiting on an array, a '+ (typeof data) +' is given');

		for (var key in data) {
			try {
				if (typeof this.responses[data[key].identifier] != 'undefined')
					throw new Error ('TAO_MATCHING.Matching::setResponses a correct variable with the identifier '+ data[key].identifier +' exists yet');
				var matchingVar = TAO_MATCHING.VariableFactory.create (data[key].value);
				this.responses[data[key].identifier] = matchingVar;
			} 
			catch (e) {
				throw e;
			}
		}
    }
	
    /**
     * Short description of method setRule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string rule
     * @return mixed
     */
    , setRule : function (rule)
	{
		var whiteFunctionsList = this.whiteFunctionsList;
		var self = this;
		
		// ohlala tmp tmp tmp
		var rule2 = rule.replace(/([a-zA-Z_\-1-9]*)[\s]*\(/g 
    		, function (str, funcName) {
				if (typeof (whiteFunctionsList[funcName]) == 'undefined')
					throw new Error ('TAO_MATCHING.Matching::setRule an error occured, the following expression is unknown '+ funcName);
				else if (typeof self[funcName] == 'undefined' 
					&& ! ( typeof whiteFunctionsList[funcName]['jsFunction'] != 'undefined' 
						&& whiteFunctionsList[funcName]['jsFunction']) )
					throw new Error ('TAO_MATCHING.Matching::setRule an error occured, the following expression is not yet supported '+ funcName);		
				return funcName+' ('; 
    	});
		
		this.rule = rule;
	}
};

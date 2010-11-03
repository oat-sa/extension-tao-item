TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

/**
 * 
 */
TAO_MATCHING.Matching = function (pData, pOptions) {
    var data = {
		"outcomes" 		: null
		, "corrects" 	: null
		, "maps" 		: null
		, "rule" 		: null
	}; if (typeof(pData) != 'undefined') $.extend(data, pData);
    var options = {
		"evaluateCallback" : null
	}; if (typeof(pOptions) != 'undefined') $.extend(options, pOptions);
		
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
     * Short description of attribute options
     *
     * @access public
     * @var array
     */
	this.options = options;

    /**
     * Short description of attribute whiteFunctionsList
     *
     * @access public
     * @var array
     */
    this.whiteFunctionsList = {
		'and'				:{'mappedFunction':'andExpression'}
		, 'equal'			:{}
		, 'if'				:{'jsFunction':true}
		, 'isNull'			:{}
		, 'getCorrect'		:{}
		, 'getMap'			:{}
		, 'getResponse'		:{}
		, 'mapResponse'		:{}
		, 'match'			:{}
		, 'setOutcomeValue'	:{}
        , 'variable'        :{'mappedFunction' : 'getResponse'}
        , 'correct'         :{'mappedFunction' : 'getCorrect'}
	};
	
	if (data.corrects != null) {
		this.setCorrects (data.corrects);
	}
	
	if (data.outcomes != null) {
		this.setOutcomes (data.outcomes);
	}
	
	if (data.maps != null) {
		this.setMaps (data.maps);
	}
	
	if (data.rule != null){
		this.setRule (data.rule);
	}
}

TAO_MATCHING.Matching.prototype = {
    /**
     * Evaluate the matching rule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    evaluate : function ()
    {	
        console.log (this.getRule());
		with (this){
			eval (getRule());	
		}
		
		if (this.options.evaluateCallback!=null)
			this.options.evaluateCallback ();
    }

    /**
     * Get the outcome in the defined JSON format
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    , outcomesToJSON : function ()
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
     * Set the corrects
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
     * Set the mappings
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
     * Set the outcomes
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
     * Set the responses
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
    /**
     * Set the matching rule
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
		var rule = rule.replace(/([a-zA-Z_\-1-9]*)[\s]*\(/g 
    		, function (str, funcName) {
                // Check if the function is in the white list
				if (typeof (whiteFunctionsList[funcName]) == 'undefined'){
    				throw new Error ('TAO_MATCHING.Matching::setRule an error occured, the expression ['+ funcName +'] is unknown ');
				}
				// Check if the function has been instantiated by the matching engine
				else if (typeof self[funcName] == 'undefined') {
				    // Check if the function has been mapped
				    if (typeof whiteFunctionsList[funcName]['mappedFunction'] != 'undefined'){
                        if (typeof self[whiteFunctionsList[funcName]['mappedFunction']] == 'undefined') {
                            throw new Error ('TAO_MATCHING.Matching::setRule an error occured, the expression ['+ funcName +'] has been mapped to ['+ whiteFunctionsList[funcName]['mappedFunction'] +'] but is not yet instantiated');
                        }
				        funcName = whiteFunctionsList[funcName]['mappedFunction'];
				    } 
				    // Check if the function is not a native javascript function
				    else if (typeof whiteFunctionsList[funcName]['jsFunction'] == 'undefined'){
				        throw new Error ('TAO_MATCHING.Matching::setRule an error occured, the expression ['+ funcName +'] is not yet instantiated');
				    }
				}
				return funcName+' ('; 
    	});
		
		this.rule = rule;
	}

    /* ************************************************************
     * OPERATOR OPEN SPACE BAR
     ************************************************************ */

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
     * Get a correct variable from its identifier
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
     * Get a mapping variable from its identifier
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
     * Get an outcome variable from its identifier
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
     * Get a response variable from its identifier
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
     * Get the matching rule
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
     * The and operator takes one or more sub-expressions each with a base-type
     * boolean and single cardinality. The result is a single boolean which is
     * if all sub-expressions are true and false if any of them are false.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    , and : function(){
        var result = true;
        var paramCount = this.and.arguments.length;
        
        for (var i = 0; i < paramCount; i++) {
            var subExp = this.and.arguments[i];
            subExpValue = null;
            
            // QTIVariable sub-expression
            if (subExp instanceof TAO_MATCHING.BaseTypeVariable) {
                if (subExp.Type() != 'boolean') {
                    throw new Error('AND operator requires sub-expressions with single cardinality and boolean baseType');
                }
                subExpValue = subExp.getValue();
            }
                
            // ! Basic Boolean sub-expression
            else 
                if (typeof subExp != 'boolean') {
                    throw new Error('AND operator requires sub-expressions with single cardinality and boolean baseType');
                }
                // Basic Boolean sub-expression
                else {
                    subExpValue = subExp;
                }
            
            result = result && subExpValue;
        }
        
        return result;
    }

    /**
     * The equal operator takes two sub-expressions which must both have single
     * and have a numerical base-type. The result is a single boolean with a
     * of true if the two expressions are numerically equal and false if they
     * not.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    , equal : function(subExp1, subExp2){
        var result = null;
        var allowedQTITypes = ['float', 'integer', 'duration'];
        var allowedBasicTypes = ['number'];
        var value1 = null;
        var value2 = null;
        
        // The first sub-expression is QTIVariable
        if (subExp1 instanceof TAO_MATCHING.BaseTypeVariable) {
            if (allowedQTITypes.indexOf(subExp1.getType()) < 0) 
                throw new Error('EQUAL operator error : the first argument must be numerical baseType');
            value1 = subExp1.getValue();
        }
        // The first expression is not an allowed basic type
        else 
            if (allowedBasicTypes.indexOf(typeof subExp1) < 0) {
            //throw new Error('EQUAL operator error : the first argument must be numerical baseType');
            }
            
            else {
                value1 = subExp1;
            }
        
        // The second sub-expression is QTIVariable
        if (subExp2 instanceof QTIVariable) {
            if (allowedQTITypes.indexOf(subExp2.getBaseType()) < 0) 
                throw new Error('EQUAL operator error : the second argument must be numerical baseType');
            //          else if (subExp2.getCardinality() != 'single')
            //              throw new Error ('EQUAL operator error : the second argument must have a single cardinality');
            value2 = subExp2.values[0];
        }
        // The second expression is not an allowed basic type
        else 
            if (allowedBasicTypes.indexOf(typeof subExp2) < 0) {
                throw new Error('EQUAL operator error : the second argument must be numerical baseType');
            }
            
            else {
                value2 = subExp2;
            }
        
        if (value1 != null && value2 != null) 
            result = value1 == value2;
        
        return result;
    } 
 
    /**
     * The isNull operator takes a sub-expression with any base-type and
     * The result is a single boolean with a value of true if the sub-expression
     * NULL and false otherwise.
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
     * This expression looks up the value of a responseVariable and then
     * it using the associated mapping, which must have been declared. The
     * is a single float. If the response variable has single cardinality then
     * value returned is simply the mapped target value from the map. If the
     * variable has single or multiple cardinality then the value returned is
     * sum of the mapped target values. This expression cannot be applied to
     * of record cardinality.
     *
     * For example, if a mapping associates the identifiers {A,B,C,D} with the
     * {0,1,0.5,0} respectively then mapResponse will map the single value 'C'
     * the numeric value 0.5 and the set of values {C,B} to the value 1.5.
     *
     * If a container contains multiple instances of the same value then that
     * is counted once only. To continue the example above {B,B,C} would still
     * to 1.5 and not 2.5.
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

        return returnValue;
    }
    
};

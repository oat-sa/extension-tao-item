TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

/**
 * 
 */
TAO_MATCHING.Matching = function(pData, pOptions) {
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
	 * Turn the matching engine into debug mode
	 * @type boolean
	 */
    this.DEBUG_MODE = false;

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
        , 'createVariable'  :{}
        , 'console'         :{}
        , 'contains'        :{}
        , 'divide'          :{}
        , 'equal'           :{}
		, 'if'				:{'jsFunction':true}
		, 'isNull'			:{}
		, 'getCorrect'		:{}
		, 'getMap'			:{}
        , 'getResponse'     :{}
        , 'getVariable'     :{}
        , 'gt'              :{}
        , 'gte'             :{}
        , 'integerDivide'   :{}
        , 'lt'              :{}
        , 'lte'             :{}
        , 'mapResponse'     :{}
        , 'match'           :{}
        , 'not'             :{}
        , 'or'              :{}
        , 'product'         :{}
        , 'randomFloat'     :{}
        , 'randomInteger'   :{}
        , 'round'           :{}
        , 'setOutcomeValue' :{}
        , 'subtract'        :{}
        , 'sum'             :{}
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
     * Debug the response processing
     */
    debugTrace : function (fctName) {
        var fctArguments = [];
        
        var paramCount = this.debugTrace.arguments.length;
        for (var i = 1; i < paramCount; i++) {
            fctArguments.push(this.debugTrace.arguments[i]);
        }
        
        console.log ('TRACE');
        console.log (fctName);
        console.log (fctArguments);
        
        return this[fctName].apply (this, fctArguments);
        //debugStack.push ({name:fctName, arguments:fctArguments});
    }
    
    /**
     * Check if optional paramaters are well formated
     * @param {string|array|object} options Object to check
     * @return {array|object} the converted options in the right format
     */
    , checkOptions : function (options){
        // Decode the options, if it has been "json string encoded"
        if (typeof options == 'string') options = eval ('('+options+')');
        else if (options == null) options = {};
        return options;
    }
    
    /**
     * DEVELOPEMENT FUNCTION
     * Debug function to show in the console an object
     * This function does not work if the document is validated
     * @todo to remove in production mode
     */
    , console : function (options, obj) {
        if (this.DEBUG_MODE)
            console.log(obj);
        return true;
    }
    
    /**
     * Evaluate the matching rule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    , evaluate : function()
    {
        if (this.getRule() == null){
            throw new Error ('TAO_MATCHING.Matching::evaluate : an error occured : the rule has not been defined');
        }
        
	    try {
            with (this){
                eval (getRule());
            }
        } catch (e) {
            if (this.DEBUG_MODE){
                this.console (null, e);
            }
        }
		
		if (this.options.evaluateCallback!=null){
            this.options.evaluateCallback (this.outcomes);
		}
    }

    /**
     * Get the matching rule
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    , getRule : function()
    {
        return this.rule;
    }

    /**
     * Get the outcome in the defined JSON format
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @attributes {JSON} poptions
     * @attributes {string} poptions.format Define the format of the return [JSON|MatchingRessource]
     * @return {array}
     */
    , getOutcomes : function (pOptions)
    {
        var returnValue = Array ();
        var options = {
            format : null
        }; if (pOptions != undefined) $.extend(options, pOptions);
        
        switch (options.format){
            case 'JSON':
                for (var key in this.outcomes) {
                    returnValue[key] = [];
                    returnValue[key]["identifier"] = key;
                    returnValue[key]["value"] = this.outcomes[key].toJSon();
                }
                break;
            default:
                returnValue = this.outcomes;
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
    , setCorrects : function(data)
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
    , setMaps : function(data)
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
    , setOutcomes : function(data)
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
     * Set the collected responses
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @return mixed
     */
    , setResponses : function(data)
    {        
    	if (! $.isArray (data))
    		throw new Error ('TAO_MATCHING.Matching::setResponses is waiting on an array, a '+ (typeof data) +' is given');

		for (var key in data) {
			try {
				if (typeof this.responses[data[key].identifier] != 'undefined')
					throw new Error ('TAO_MATCHING.Matching::setResponses a response variable with the identifier '+ data[key].identifier +' exists yet');
				var matchingVar = TAO_MATCHING.VariableFactory.create (data[key].value);
				this.responses[data[key].identifier] = matchingVar;
			} 
			catch (e) {
				throw e;
			}
		}
    }
    
    /**
     * get the collected responses
     *
     * @access public
     * @attribute {JSON} poptions
     * @attributes {JSON} poptions.format Define the format of the return [JSON|MatchingRessource]
     * @return {Object}
     */
    , getResponses : function(pOptions)
    {        
        var options = {
            format : null
        }; if (pOptions != undefined) $.extend(options, pOptions);
        
        switch (options.format){
            case 'JSON':
                for (var key in this.responses) {
                    returnValue[key] = [];
                    returnValue[key]["identifier"] = key;
                    returnValue[key]["value"] = this.responses[key].toJSon();
                }
                break;
            default:
                returnValue = this.outcomes;
        }
    	return this.responses;
    }
	
    
    /**
     * Set the matching rule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string rule
     * @return mixed
     */
    , setRule : function(rule)
	{
		var whiteFunctionsList = this.whiteFunctionsList;
		var self = this;
		
		// ohlala tmp tmp tmp
		var rule = rule.replace(/([a-zA-Z_\-1-9]*)[\s]*\(/g 
    		, function(str, funcName) {
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
				
				//if (self.DEBUG_MODE && typeof whiteFunctionsList[funcName]['jsFunction'] == 'undefined'){
				//    return 'debugTrace ("' + funcName + '" ,';
				//} else {
				    return funcName + '(';
				//}
    	});
		
		this.rule = rule;
	}

    /* ************************************************************
     * OPERATOR OPEN SPACE BAR
     ************************************************************ */

    /**
     * Short description of method createVariable
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @return taoItems_models_classes_Matching_Tuple
     */
    , createVariable : function (options, type) {
        var returnValue = null;
        options = this.checkOptions(options);
                
        // Type undefined, we are in the case of baseTypeVariable creation (cardinality single)
        if (typeof(options.type) == 'undefined' ) {
            returnValue = TAO_MATCHING.VariableFactory.create (this.createVariable.arguments[1]);
        }
        else
        {
            switch (options.type){
                case 'integer':
                case 'float':
                case 'string':
                case 'boolean':
                    // In all the base type cases create a variable with the first found argument
                    returnValue = TAO_MATCHING.VariableFactory.create (this.createVariable.arguments[1]);
                    break;
                    
                case 'tuple':
                    var values = {};
                    var a = 0;
                    for (var i = 1; i < this.createVariable.arguments.length; ++i, ++a) {
                        values[a] = this.createVariable.arguments[i];
                    }
                    //var tuple = new TAO_MATCHING.Tuple (values);
                    //console.log (tuple);
                    
                    returnValue = TAO_MATCHING.VariableFactory.create (values);
                    break;
                    
                case 'list':
                    var values = [];
                    var a = 0;
                    for (var i = 1; i < this.createVariable.arguments.length; ++i, ++a) {
                        values.push (this.createVariable.arguments[i]);
                    }
                    returnValue = TAO_MATCHING.VariableFactory.create (values);
                    break;
                    
                case 'default':
                    throw new Error ('TAO_MATCHING.createVariable : type unknown ['+options.type+']');
            }
        }
        
        //console.log(returnValue.toJSon());
        
        return returnValue;
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
    , setOutcomeValue : function(identifier, value)
    {
        var outcome = this.getOutcome (identifier);
        if(outcome == null){
            throw new Error ('TAO_MATCHING.Matching::setOutcomeValue error : the outcome value '+identifier+' does not exist');
        }
        
        if (value instanceof TAO_MATCHING.BaseTypeVariable){
            outcome.setValue (value.getValue());
        }
        else {
            //if (TAO_MATCHING.BaseTypeVariable.isValidValue (value)){
                outcome.setValue (value);
            //}else{
            //    throw new Error ('taoItems_models_classes_Matching_Matching::setOutcomeValue error : unable to set a value of this type ['+typeof(value)+']');
            //}
        }
    }
    
    /**
     * Get a correct variable from its identifier
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_Matching_Variable
     */
    , getCorrect : function(identifier)
    {
        var returnValue = null;

        if (typeof (this.corrects[identifier]) != 'undefined')
            returnValue = this.corrects[identifier];
        else
            throw new Error ('TAO_MATCHING.Matching::getCorrect error : try to reach an unknown correct variable ['+identifier+']');

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
    , getMap : function(identifier)
    {
        var returnValue = null;

        if (typeof (this.maps[identifier]) != 'undefined')
            returnValue = this.maps[identifier];
        else
            throw new Error ('TAO_MATCHING.Matching::getMap error : try to reach an unknown mapping variable ['+identifier+']');

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
    , getOutcome : function(identifier)
    {
        var returnValue = null;
        
        if (typeof (this.outcomes[identifier]) != 'undefined')
            returnValue = this.outcomes[identifier];
        else
            throw new Error ('TAO_MATCHING.Matching::getOutcome error : try to reach an unknown outcome variable ['+identifier+']');

        return returnValue;
    }
    
    /**
     * Get a variab from its identifier
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_Matching_Variable
     */
    , getVariable : function(identifier)
    {
        var returnValue = null;

        if (typeof (this.responses[identifier]) != 'undefined')
            returnValue = this.responses[identifier];
        else if (typeof (this.outcomes[identifier]) != 'undefined')
            returnValue = this.outcomes[identifier];
        else
            throw new Error ('TAO_MATCHING.Matching::getVariable error : try to reach an unknown variable ['+identifier+']');

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
    , getResponse : function(identifier)
    {
        var returnValue = null;

        if (typeof (this.responses[identifier]) != 'undefined')
            returnValue = this.responses[identifier];
        else
            throw new Error ('TAO_MATCHING.Matching::getResponse error : try to reach an unknown outcome variable ['+identifier+']');

        return returnValue;
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
    , and : function(options){
        var returnValue = null;
        options = this.checkOptions(options);
        
        var paramCount = this.and.arguments.length;

        for (var i = 1; i < paramCount; i++) {
            var subExp = this.and.arguments[i];
            var matchingSubExp = TAO_MATCHING.VariableFactory.toBooleanBaseType (subExp);
            
            if (matchingSubExp == null) {
                throw new Error ("TtaoItems_models_classes_Matching_Matching::and an error occured : The ["+i+"]expression passed ["+matchingSubExp+"] to the operator has to be a valid boolean expression with single cardinality");
            } else {
                if (matchingSubExp.isNull()){
                    returnValue = null;
                    break;
                }else{
                    if (returnValue === null){
                        returnValue = matchingSubExp.getValue();
                    } else {
                        returnValue = returnValue && matchingSubExp.getValue();
                    }
                }
            }
        }
        
        return returnValue;
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
    , equal : function(options, expr1, expr2){
        var result = null;
        options = this.checkOptions(options);

        var value1 = null;
        var value2 = null;
        
        // The first expression is a Matching BaseTypeVariable
        if (expr1 instanceof TAO_MATCHING.BaseTypeVariable) {
            value1 = expr1.getValue();
        }
        // The first expression is not a Matching BaseTypeVariable
        else {
            if (!TAO_MATCHING.Variable.isScalar(expr1)) {
                throw new Error('TAO_MATCHING.matching::equal an error occured : the first argument ['+expr1+'] must be a scalar');
            }
            else {
                value1 = expr1;
            }
        }
        
        // The second expression is a Matching BaseTypeVariable
        if (expr2 instanceof TAO_MATCHING.BaseTypeVariable) {
            value2 = expr2.getValue();
        }
        // The second expression is not a Matching BaseTypeVariable
        else {
            if (!TAO_MATCHING.Variable.isScalar(expr2)) {
                throw new Error('TAO_MATCHING.matching::equal an error occured : the second argument ['+expr2+'] must be a scalar');
            }
            else {
                value2 = expr2;
            }    
        }
        
        if (value1 != null && value2 != null) {
            result = value1 === value2;
        }
        
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
    , isNull : function(options, matchingVar)
    {
        options = this.checkOptions(options);
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
    , mapResponse : function(options, mappingVar, matchingVar)
    {        
        options = this.checkOptions(options);
        
        if (! (mappingVar instanceof TAO_MATCHING.Map) )
            throw new Error ('TAO_MATCHING.Matching::mapResponse an error occured : first argument expected type TAO_MATCHING.mappingVar, given : '+(typeof mappingVar));

        return mappingVar.map (matchingVar);
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
    , match : function(options, expr1, expr2)
    {
        var returnValue = false;
        options = this.checkOptions(options);
                
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
    
    /**
     * Short description of method gt
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    , gt : function (options, expr1, expr2)
    {
        var returnValue = false;
        
        // IF the first expression is not a numerical base type
        var matchingExpr1 = TAO_MATCHING.VariableFactory.toNumericBaseType (expr1);
        if (matchingExpr1 == null) {
            throw new Error ("TAO_MATCHING.Matching::gt an error occured : The first expression passed ["+expr1+"] to the operator has to be a valid numerical expression with single cardinality");
        }

        // IF the first expression is not a numerical base type
        var matchingExpr2 = TAO_MATCHING.VariableFactory.toNumericBaseType (expr2);
        if (matchingExpr2 == null) {
            throw new Error ("TAO_MATCHING.Matching::gt an error occured : The second expression passed ["+expr2+"] to the operator has to be a valid numerical expression with single cardinality");
        }
        
        if (matchingExpr1.getValue() > matchingExpr2.getValue()) {
            returnValue = true;
        }

        return returnValue;
    }

    /**
     * Short description of method lt
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    , lt : function (options, expr1, expr2)
    {
        var returnValue = false;
        
        // IF the first expression is not a numerical base type
        var matchingExpr1 = TAO_MATCHING.VariableFactory.toNumericBaseType (expr1);
        if (matchingExpr1 == null) {
            throw new Error ("TAO_MATCHING.Matching::gt an error occured : The first expression passed ["+expr1+"] to the operator has to be a valid numerical expression with single cardinality");
        }

        // IF the first expression is not a numerical base type
        var matchingExpr2 = TAO_MATCHING.VariableFactory.toNumericBaseType (expr2);
        if (matchingExpr2 == null) {
            throw new Error ("TAO_MATCHING.Matching::gt an error occured : The second expression passed ["+expr2+"] to the operator has to be a valid numerical expression with single cardinality");
        }
        
        if (matchingExpr1.getValue() < matchingExpr2.getValue()) {
            returnValue = true;
        }

        return returnValue;
    }
    
    /**
     * Short description of method lt
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    , lte : function (options, expr1, expr2)
    {
        var returnValue = false;
        
        // IF the first expression is not a numerical base type
        var matchingExpr1 = TAO_MATCHING.VariableFactory.toNumericBaseType (expr1);
        if (matchingExpr1 == null) {
            throw new Error ("TAO_MATCHING.Matching::gt an error occured : The first expression passed ["+expr1+"] to the operator has to be a valid numerical expression with single cardinality");
        }

        // IF the first expression is not a numerical base type
        var matchingExpr2 = TAO_MATCHING.VariableFactory.toNumericBaseType (expr2);
        if (matchingExpr2 == null) {
            throw new Error ("TAO_MATCHING.Matching::gt an error occured : The second expression passed ["+expr2+"] to the operator has to be a valid numerical expression with single cardinality");
        }
        
        if (matchingExpr1.getValue() <= matchingExpr2.getValue()) {
            returnValue = true;
        }

        return returnValue;
    }
    
    /**
     * Short description of method lt
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    , gte : function (options, expr1, expr2)
    {
        var returnValue = false;
        
        // IF the first expression is not a numerical base type
        var matchingExpr1 = TAO_MATCHING.VariableFactory.toNumericBaseType (expr1);
        if (matchingExpr1 == null) {
            throw new Error ("TAO_MATCHING.Matching::gt an error occured : The first expression passed ["+expr1+"] to the operator has to be a valid numerical expression with single cardinality");
        }

        // IF the first expression is not a numerical base type
        var matchingExpr2 = TAO_MATCHING.VariableFactory.toNumericBaseType (expr2);
        if (matchingExpr2 == null) {
            throw new Error ("TAO_MATCHING.Matching::gt an error occured : The second expression passed ["+expr2+"] to the operator has to be a valid numerical expression with single cardinality");
        }
        
        if (matchingExpr1.getValue() >= matchingExpr2.getValue()) {
            returnValue = true;
        }

        return returnValue;
    }
    
    , sum : function (options){
        var returnValue = 0;
        
        options = this.checkOptions(options);
        var paramCount = this.sum.arguments.length;
        
        for (var i = 1; i < paramCount; i++) {
            var subExp = this.sum.arguments[i];
            var matchingSubExp = TAO_MATCHING.VariableFactory.toNumericBaseType (subExp);
            if (matchingSubExp == null){
                throw new Error ("TAO_MATCHING.Matching::sum an error occured : The ["+i+"] expression passed ["+subExp+"] to the operator has to be a valid numerical expression with single cardinality");
            } else if (matchingSubExp.isNull()){
                returnValue = null;
                break;
            } else {
                returnValue += matchingSubExp.getValue();
            }
        }
        
        return returnValue;
    }
    
    , subtract : function (options, expr1, expr2){
        var returnValue = null;
        
        // IF the first expression is not a numerical base type
        var matchingExpr1 = TAO_MATCHING.VariableFactory.toNumericBaseType (expr1);
        if (matchingExpr1 == null) {
            throw new Error ("TAO_MATCHING.Matching::substract an error occured : The first expression passed ["+expr1+"] to the operator has to be a valid numerical expression with single cardinality");
        }

        // IF the first expression is not a numerical base type
        var matchingExpr2 = TAO_MATCHING.VariableFactory.toNumericBaseType (expr2);
        if (matchingExpr2 == null) {
            throw new Error ("TAO_MATCHING.Matching::substract an error occured : The second expression passed ["+expr2+"] to the operator has to be a valid numerical expression with single cardinality");
        }
        
        if (matchingExpr1.getValue() == null || matchingExpr2.getValue() == null){
            returnValue = null;
        } else {
            returnValue = matchingExpr1.getValue() - matchingExpr2.getValue();
        }
        
        return returnValue;
    }
    
    , product : function (options){
        var returnValue = null;
        
        options = this.checkOptions(options);
        var paramCount = this.product.arguments.length;
        
        for (var i = 1; i < paramCount; i++) {
            var subExp = this.product.arguments[i];
            var matchingSubExp = TAO_MATCHING.VariableFactory.toNumericBaseType (subExp);
            // If the sub expression is not a numerical base type variable
            if (matchingSubExp == null){
                throw new Error ("TAO_MATCHING.Matching::product an error occured : The ["+i+"] expression passed ["+subExp+"] to the operator has to be a valid numerical expression with single cardinality");
            } 
            // If the sub expression value is null
            else if (matchingSubExp.isNull()){
                returnValue = null;
                break;
            // Else compute
            } else {
                // first pass
                if (returnValue==null){
                    returnValue = matchingSubExp.getValue();
                } 
                else {
                    returnValue *= matchingSubExp.getValue();
                }
            }
        }
        
        return returnValue;
    }
    
    , divide : function (options, expr1, expr2){
        var returnValue = null;
        
        // IF the first expression is not a numerical base type
        var matchingExpr1 = TAO_MATCHING.VariableFactory.toNumericBaseType (expr1);
        if (matchingExpr1 == null) {
            throw new Error ("TAO_MATCHING.Matching::substract an error occured : The first expression passed ["+expr1+"] to the operator has to be a valid numerical expression with single cardinality");
        }

        // IF the first expression is not a numerical base type
        var matchingExpr2 = TAO_MATCHING.VariableFactory.toNumericBaseType (expr2);
        if (matchingExpr2 == null) {
            throw new Error ("TAO_MATCHING.Matching::substract an error occured : The second expression passed ["+expr2+"] to the operator has to be a valid numerical expression with single cardinality");
        }
        
        if (matchingExpr1.getValue() == null || matchingExpr2.getValue() == null || matchingExpr2.getValue() == 0){
            returnValue = null;
        } else {
            returnValue = matchingExpr1.getValue() / matchingExpr2.getValue();
        }
        
        return returnValue;
    }
    
    , round : function (options, expr){
        var returnValue = null;
        options = this.checkOptions (options);
        
        if (expr == null){
            returnValue = null;
        } else {
            // IF the first expression is not a numerical base type
            var matchingExpr = TAO_MATCHING.VariableFactory.toNumericBaseType (expr);
            if (matchingExpr == null) {
                throw new Error ("TAO_MATCHING.Matching::round an error occured : The expression passed ["+expr+"] to the operator has to be a valid numerical expression with single cardinality");
            } else {
                if (matchingExpr.isNull()) {
                    returnValue = null;
                } else {
                    var precision = 0;
                    if (typeof options.precision != 'undefined') {
                        precision = options.precision;
                    }
                    returnValue = Math.round (matchingExpr.getValue ()*Math.pow(10,precision))/Math.pow(10,precision);
                }
            }
            
        }
                
        return returnValue;
    }
    
    , integerDivide : function (options, expr1, expr2){
        var returnValue = null;
        returnValue = this.round(null, this.divide(null, expr1, expr2));                
        return returnValue;
    }
    
    /**
     * Short description of method not
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr
     * @return boolean
     */
    , not : function (options, expr)
    {
        var returnValue = null;
        
        var matchingExpr = TAO_MATCHING.VariableFactory.toBooleanBaseType (expr);
        if (matchingExpr == null){
            throw new Error ("TtaoItems_models_classes_Matching_Matching::not an error occured : The expression passed ["+expr+"] to the operator has to be a valid boolean expression with single cardinality");
        } else {
            var matchingExprValue = matchingExpr.getValue();
            if (matchingExprValue != null) {
                returnValue = ! matchingExprValue;
            }
        }

        return returnValue;
    }
    
    /**
     * Short description of method or
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @return boolean
     */
    , or : function(options){
        var returnValue = null;
        options = this.checkOptions(options);
        
        var paramCount = this.or.arguments.length;
        
        for (var i = 1; i < paramCount; i++) {
            var subExp = this.or.arguments[i];
            var matchingSubExp = TAO_MATCHING.VariableFactory.toBooleanBaseType (subExp);
            
            if (matchingSubExp == null) {
                throw new Error ("TtaoItems_models_classes_Matching_Matching::or an error occured : The ["+i+"]expression passed ["+matchingSubExp+"] to the operator has to be a valid boolean expression with single cardinality");
            } else {
                if (matchingSubExp.isNull()){
                    returnValue = null;
                    break;
                }else{
                    if (returnValue === null){
                        returnValue = matchingSubExp.getValue();
                    } else {
                        returnValue = returnValue || matchingSubExp.getValue();
                    }
                }
            }
        }
        
        return returnValue;
    }
    
    /**
     * Selects a random integer from the specified range [min,max] satisfying
     * + step * n for some integer n. For example, with min=2, max=11 and step=3
     * values {2,5,8,11} are possible.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @return int
     */
    , randomInteger : function (options){
        var returnValue = null;
        options = this.checkOptions (options);
        
        var randomNum = Math.random() * (options.max-options.min);
        // Round to the closest integer and return it
        returnValue = Math.round(randomNum) + options.min; 
                
        return returnValue;
    }
 
    /**
     * Selects a random float from the specified range [min,max].
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @return double
     */
    , randomFloat : function (options){
        var returnValue = null;
        var precision = 2;
        
        options = this.checkOptions (options);
        if (typeof options.precision != 'undefined'){
            precision = options.precisions
        }
        
        returnValue = parseFloat(
            Math.min(options.min + (Math.random() * (options.max - options.min)), options.max).toFixed(precision));
                
        return returnValue;
    }
    
    /**
     * The contains function takes two sub-expressions. The first one has a
     * - either list or tuple. The second one could have any base type and could
     * the same cardinality than the first expression or it could have a single
     * The result is a single boolean with a value of true if the container
     * by the first sub-expression contains the value given by the second
     * and false if it doesn't. Note that the contains operator works
     * depending on the cardinality of the two sub-expressions. For unordered
     * the values are compared without regard for ordering, for example, [A,B,C]
     * [C,A]. Note that [A,B,C] does not contain [B,B] but that [A,B,B,C] does.
     * ordered containers the second sub-expression must be a strict
     * within the first. In other words, [A,B,C] does not contain [C,A] but it
     * contain [B,C].
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     * @return taoItems_models_classes_Matching_bool
     */
    , contains : function (options, expr1, expr2){
        var returnValue = null;
        options = this.checkOptions (options);
                
        if (!TAO_MATCHING.Variable.isCollection(expr1)){
            throw new Error ("TtaoItems_models_classes_Matching_Matching::contains \
            an error occured : The operator contains as first argument an expression of type Collection");
        }
        
        returnValue = expr1.contains(expr2, options);
        
        return returnValue;
    }
};

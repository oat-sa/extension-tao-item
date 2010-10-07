/** QTI Matching API */
var QTIMatching = Class.extend (
/** @lends QTIMatching.prototype */
{
	
	/** Constructor like */ 
	init : function () { }

	/** Eval the stored response processing rule 
	 * @return {Object} The rule return */
	, evalResponseProcessing : function (){
		with (this) {
			return eval(rule);
		}
	}
	
	/** Set the correct variables of the item
	 * @param {QTIVariable} corrects */
	, setCorrects : function (corrects){
		this.corrects = corrects;
	}
	
	/** Set the maps associated to the response processing
	 * @param {QTIMap} maps */
	, setMaps: function (maps){
		this.maps = maps;
	}
	
	/** Set the outcome variables of the item
	 * @param {Object} outcomes */
	, setOutcomes : function (outcomes){
		this.outcomes = outcomes;
	}

	/** Set a Rule
	 * @param {String} rule Rule formated in the defined format
	 */	
	, setRule : function (rule){
		this.rule = rule;
	}
	
	/** Set the variables of the item (User's variables)
	 * @param {QTIVariable} corrects */
	, setVariables : function (variables){
		this.variables = variables;
	}
	
/* ************************************************************************* *
 * QTI Operator API
 * ************************************************************************* */
	
	/** AND operator
	 * @param {Boolean|QTIVariable} AND operator can get 1 or * arguments
	 * @return {Boolean} If the and operator is verified return true, else return false. 
	 * If a sub-expression is null return null
	 * @todo [cardinality implementation] if a subExpr has not a single cardinality throw an exception (and return false) 
	 * @todo check if no sub-expressions 
	 * @todo check the rule about a null sub-expression 
	 * @todo what to do if a variable has no value ? */
	, and : function(){
		var result = true;
		var paramCount = this.and.arguments.length;
	
	    for (var i=0; i<paramCount; i++){
			var subExp = this.and.arguments[i];
			subExpValue = null;
			
			// QTIVariable sub-expression
			if (subExp instanceof QTIVariable){
				if (subExp.getType() != 'boolean'/* || subExpr.getcardinality != 'single'*/) {
					throw new Error('AND operator requires sub-expressions with single cardinality and boolean type');
				}
				subExpValue = subExp.values[0];
			
			// ! Basic Boolean sub-expression
			}else if (typeof subExp != 'boolean'){
				throw new Error ('AND operator requires sub-expressions with single cardinality and boolean type');
				console.log (subExp)
			// Basic Boolean sub-expression
			}else{
				subExpValue = subExp;
			}
			
			result = result && subExpValue;
		}
		
	    return result;
	}
	
	/** The equal operator takes two sub-expressions which must both have single cardinality and have 
	 * a numerical base-type. The result is a single boolean with a value of true if the two expressions 
	 * are numerically equal and false if they are not. 
	 * @param {QTIVariable|float|integer} subExp1
	 * @param {QTIVariable|float|integer} subExp2
	 * @return {boolean} true if the sub-expressions are egal, false else 
	 * @todo If either sub-expression is NULL then the operator results in NULL. 
	 * @todo Support duration 
	 * @todo Cardinality support */
	, equal : function(subExp1, subExp2){
		var result = null;
		var allowedQTITypes = ['float', 'integer', 'duration'];
		var allowedBasicTypes = ['number'];
		var value1 = null;
		var value2 = null
		
		// The first sub-expression is QTIVariable
		if (subExp1 instanceof QTIVariable){
			if (allowedQTITypes.indexOf(subExp1.getType())<0)
				 throw new Error('EQUAL operator error : the first argument must be numerical type');
//			else if (subExp1.getCardinality() != 'single')
//				throw new Error ('EQUAL operator error : the first argument must have a single cardinality');
			value1 = subExp1.values[0];
		}
		// The first expression is not allowed basic type
		else if (allowedBasicTypes.indexOf(typeof subExp1)<0) {
			//throw new Error('EQUAL operator error : the first argument must be numerical type');
		}
		
		else {
			value1 = subExp1;
		}
		
		// The second sub-expression is QTIVariable
		if (subExp2 instanceof QTIVariable){
			if (allowedQTITypes.indexOf(subExp2.getType())<0)
				 throw new Error('EQUAL operator error : the second argument must be numerical type');
//			else if (subExp2.getCardinality() != 'single')
//				throw new Error ('EQUAL operator error : the second argument must have a single cardinality');
			value2 = subExp2.values[0];
		}
		// The second expression is not allowed basic type
		else if (allowedBasicTypes.indexOf(typeof subExp2)<0) {
			throw new Error('EQUAL operator error : the second argument must be numerical type');
		}
		
		else {
			value2 = subExp2;
		}
		
		if (value1!=null && value2!=null)			
			result = value1 == value2;
	    
		return result;
	}
	
	/** The isNull operator takes a sub-expression with any base-type and cardinality. The result is 
	 * a single boolean with a value of true if the sub-expression is NULL and false otherwise. 
	 * Note that empty containers and empty strings are both treated as NULL.
	 * @param {Object} qtiVar
	 * @return {boolean} */
	, isNull : function (qtiVar) {
		return qtiVar.isNull ();
	}
	
	/** Map a variable with a given map and return the evaluated score
	 * @param {QTIVariable} qtiVar
	 * @param {QTIMap} qtiMap
	 * @return {float} the score */
	, mapResponse : function(qtiVar, qtiMap){
		if (! qtiVar instanceof QTIVariable)
			throw new Error ('MAPRESPONSE operator works "only" with QTIVariable as first argument');
		if (! qtiMap instanceof QTIMap)
			throw new Error ('MAPRESPONSE operator works "only" with QTIMap as second argument');
		
	    return qtiVar.map (qtiMap);
	}
	
	/** The match operator takes two sub-expressions which must both have the same base-type and 
	 * cardinality. The result is a single boolean with a value of true if the two expressions represent 
	 * the same value and false if they do not.
	 * @param {QTIVariable} subExp1
	 * @param {QTIVariable} subExp2 
	 * @todo The match functions works "only" with QTIVariable for now (extend required ?)
	 * @todo If either sub-expression is NULL then the operator results in NULL. */
	, match : function(subExp1, subExp2){
		var result = null;
		
		if (subExp1 instanceof QTIVariable && subExp2 instanceof QTIVariable)
	    	result = subExp1.match(subExp2);
		else
			throw new Error ('MATCH operator works "only" with QTIVariable for now');
			
		return result;
	}
	
/* ************************************************************************* *
 * ::!ùlpù$Ù!:!*Ù
 * ************************************************************************* */
	
	/** Set the value of an outcome variable
	 * @param {String} identifier Identifier of the outcome variable
	 * @param {Object[]|Object} values Mix variable which could be an array of value to set to the outcome variable 
	 */
	, setOutcomeValue : function(identifier, values){
		var outcome = this.getOutcome(identifier);
		if (!(values instanceof Array)) {
			values = [values];
		}
		outcome.setValues (values);
	}
	
	, getVariable : function(name){
	    return this.variables[name];
	}
	
	, getCorrect : function(name){
	    return this.corrects[name];
	}
	
	, getMap : function(name){
	    return this.maps[name];
	}
	
	, getOutcome : function(name){
		return this.outcomes[name];
	}
	
});

/** QTI Variables Factory
 * @param {Object} identifier
 * @param {Object} type
 * @param {Object} values
 * @param {Object} options
 */
var QTIVariableFactory = function(identifier, type, cardinality, values, options){
    var variable = null;
    
    // If the container type is a map
    if (typeof(options) != 'undefined' && options.containerType == 'map') {
        variable = new QTIMap(identifier, type, cardinality, values);
        
        // If the container type is a variable
    }
    else {
        switch (type) {
            case 'directedpair':
                variable = new QTIPair(identifier, type, cardinality, values, true);
                break;
            case 'pair':
                variable = new QTIPair(identifier, type, cardinality, values);
                break;
            default:
                variable = new QTIString(identifier, type, cardinality, values);
        }
    }
    
    return variable;
};

/** Unserialized a json QTI formated variable in QTIVariable(s)
 * @param {String} serialized
 * @param {JSon} options Collection of options
 * @return {QTIVariable|QTIVariable[]} Functions of the serialized argument, return a QTIVariable or a collection of QTIVariable 
 * @todo options ?*/
var unserializedQTIVariables = function(serialized, options){
    var result = null;

	var json = eval('(' + serialized + ')');

	if (json instanceof Array){
	    result = new Array();
	    for (var i in json) 
			result[json[i].identifier] = new QTIVariableFactory(json[i].identifier, json[i].type, json[i].cardinality, json[i].values, options);
	}
	else if (typeof json == 'object') {
		result = new QTIVariableFactory(json.identifier, json.type, json.cardinality, json.values, options);
	}

	return result;
};

var QTIMatching = Class.extend ({
	init : function () {
		
	}
	, setRule : function (rule){
		this.rule = rule;
	}
	, setCorrects : function (corrects){
		this.corrects = corrects;
	}
	, setVariables : function (variables){
		this.variables = variables;
	}
	, setOutcomes : function (outcomes){
		this.outcomes = outcomes;
	}
	, and : function(){
		var result = true;
		var paramCount = and.arguments.length;
	    for (var i=0; i<paramCount; i++)
			result = result && and.arguments[i];
	    return result;
	}
	, match : function(qtiVar1, qtiVar2){
	    return qtiVar1.match(qtiVar2);
	}
	, mapResponse : function(qtiVar, qtiMap){
	    return qtiVar.map (qtiMap);
	}
	, getOutcomeValue : function(name){
		var outcome = this.getOutcome(name);
		return this.outcome.values;
	}
	, setOutcomeValue : function(name, values){
		var outcome = this.getOutcome(name);
		this.outcome.setValues (values);
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
	, evalResponseProcessing : function (){
		with (this) {
			return eval(rule);
		}
	}
});

/* ************************************************************************* *
 * QTI Operator (Expresion) API
 * ************************************************************************* */
//var and = function(){
//	var result = true;
//	var paramCount = and.arguments.length;
//    for (var i=0; i<paramCount; i++)
//		result = result && and.arguments[i];
//    return result;
//};
//var match = function(qtiVar1, qtiVar2){
//    return qtiVar1.match(qtiVar2);
//};
//var mapResponse = function(qtiVar, qtiMap){
//    return qtiVar.map (qtiMap);
//};
//var getOutcomeValue = function(name){
//	var outcome = getOutcome(name);
//	return outcome.values;
//}
//var setOutcomeValue = function(name, values){
//	var outcome = getOutcome(name);
//	outcome.setValues (values);
//}
//
//// Deal with variables
//var getVariable = function(name){
//    return variables[name];
//};
//var getCorrect = function(name){
//    return corrects[name];
//};
//var getMap = function(name){
//    return maps[name];
//};
//var getOutcome = function(name){
//	return outcomes[name];
//};

/* ************************************************************************* *
 * QTI Variables API
 * ************************************************************************* */
var QTIVariableFactory = function ( identifier, type, values, options ){
    var variable = null;
    
	// If the container type is a map
	if (typeof (options) != 'undefined' && options.containerType == 'map') {
		variable = new QTIMap (identifier, type, values);
	
	// If the container type is a variable
	} else {
	    switch (type) {
	        case 'directedpair':
	            variable = new QTIPair (identifier, type, values, true);
	            break;
	        case 'pair':
	            variable = new QTIPair (identifier, type, values);
	            break;
	        default:
	            variable = new QTIString (identifier, type, values);
	    }	
	}
    
    return variable;
};

var QTIExpression = Class.extend({
    init: function () {
    }
});

var QTIVariable = QTIExpression.extend({
    init: function ( identifier, type ) {
		this._super ();
		this.setIdentifier (identifier);
        this.setType(type);
        this.values = [];
    }
	// Get the QtiVariable type
	, getType: function(){
        return this.type;
    }
	// Set the QtiVariable type
	, setType: function(type){
        this.type = type;
    } 
	// Set the QtiVariable identifier
	, getIdentifier: function(){
        return (this.identifier);
    } 
	// Set the QtiVariable identifier
	, setIdentifier: function(identifier){
        this.identifier = identifier;
    } 
	// Match the QtiVar with another
	// != type -> return false
	// != cardinality -> return false
    , match: function (qtiVar) {
        // Check if the vars to match have the same type
        if (this.type != qtiVar.type) 
            return false;
        // Check if the vars to match have the same cardinality
        else 
            if (this.cardinality != qtiVar.cardinality) 
                return false;

//	    console.log('Match the QtiVar with another');
//	    console.log(this);
//	    console.log(qtiVar);
		
        return _match(this.values, qtiVar.values);
    }
	// Map the QtiVar with a QtiMap
	, map : function ( qtiMap ) {
        // Check if the vars to map and the qtiMap have the same type
        if (this.type != qtiMap.type) 
            return false;
		// Check that the Qti var is well a QTIMap type
		if (!qtiMap instanceof QTIMap)
			return false;

//	    console.log('Map the QtiVar with a QtiMap');
//	    console.log(this);
//	    console.log(qtiMap);
		
		return qtiMap.map (this);
	}
	// Set values of the QtiVariable
	, setValues: function(values){
        for (var i in values) {
            //this.values [i] = values[i];
            this.values = values;
        }
    }
	// To JSON
	, toJson : function () {
		var str = "{ \
			identifier:'"+this.identifier+"' \
			, type:'"+this.type+"' \
			, cardinality:'' \
			, values:[";
		var strValues = '';
		for (var i in this.values){
			strValues += strValues.length>0?',':'';
			strValues += "'"+this.values[i]+"'";	
		}
		str += strValues + "] \
		}";
		return str;
	}
});

var QTIMap = QTIVariable.extend ({
	init: function ( identifier, type, values) {
		this._super (identifier, type);
		this.setValues (values);
	}
	, map : function ( qtiVar) {
		var score = 0;
	    var found = [];

//	    console.log('Map the qtiPair with the map');
//	    console.log(this);
//	    console.log(qtiVar);

	    for (var i in this.values) {
	        for (var j in qtiVar.values) {
	            var resMatch = _match(this.values[i][0], qtiVar.values[j]);
	            if (resMatch && typeof found[i] == 'undefined') {
	                found[i] = true;
	                score +=this.values[i][1];
	            }
	        }
	    }
	    return score;
	}
});

var QTIPair = QTIVariable.extend({
    init: function ( identifier, type, values, directed ) {
        this._super (identifier, type);
        this.setValues (values);
		this.directed = typeof (directed) != undefined ? directed : false;
    }
	// Match the QtiPair with another
	// != type -> return false
	// != cardinality -> return false
	// != length -> return false
    , match: function ( qtiPair ) {
        var result = true;

//	    console.log('Match the qtiPair with another');
//	    console.log(this);
//	    console.log(qtiPair);
		
		// Check if the vars to match have the same type
        if (this.type != qtiPair.type) 
            return false;
        // Check if the vars to match have the same cardinality
        else 
            if (this.cardinality != qtiPair.cardinality) 
                return false;
		else
			if (this.values.length != qtiPair.values.length)
				return false;
		
		// match QtiPair
        for (var i in this.values) {
			var result2 = false;
			for (var j in qtiPair.values) {
				if (_match(this.values[i], qtiPair.values[j], this.directed)) {
					result2 = true;
					break; // Element found, we can stop the script
				}
			} 
			if (!result2) {
				result = false; // Element not found, we can stop the script
				break;	
			}
        }
        return result;
    }
});

var QTIString = QTIVariable.extend({
    init: function ( identifier, type, values ) {
        this._super (identifier, type);
        this.setValues (values);
    }
});

/* ************************************************************************* *
 * Tools
 * ************************************************************************* */

var unserializedQTIVariables = function (serialized, options){
    var json = eval('(' + serialized + ')');
    var qtiVariables = [];
    for (var i in json) {
        qtiVariables[json[i].identifier] = new QTIVariableFactory(json[i].identifier, json[i].type, json[i].values, options);
    }
    return qtiVariables;
};

// Array match
// != vars' length -> return false
var _match = function(var1, var2, ordered){
    var result = true;
    ordered = typeof ordered != 'undefined' ? ordered : false;        
	
    // expression 1 & expression 2 must have the same cardinality
    if (var1.length != var2.length) 
        return false;
    
    // expression 1 & expression 2 must have the same type
    
    // match var1 & var2
    if (ordered) {
        for (var i in var1) {
            if (var1[i] != var2[i]) 
                result = false;
        }
    }
    else {
        for (var i in var1) {
            var result2 = false;
            for (var j in var1) {
                if (var1[i] == var2[j]) {
                    result2 = true;
                    break;
                }
            }
            if (!result2){
                result = false;
				break;	
			}            
        }
    }
    return result;
};

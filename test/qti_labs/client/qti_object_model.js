/** QTIExpression */
var QTIExpression = Class.extend(
/** @lends QTIExpression.prototype */
{
	/** Constructor like */
    init: function () {
    }
});

/** QTI Variable represents
 * @param {Object} identifier
 * @param {Object} type
 * @param {Object} cardinality */
var QTIVariable = QTIExpression.extend(
/** @lends QTIVariable.prototype */
{
	/** Constructor like */
    init: function ( identifier, type, cardinality ) {
		this._super ();
		this.setIdentifier (identifier);
        this.setType(type);
        this.setCardinality(cardinality);
        this.values = []; // init empty values
    }
	
	/** Get the QtiVariable cardinality 
	 * @return {String} */
	, getCardinality: function(){
        return this.cardinality;
    }
	
	/** Get the QtiVariable type
	 * @return {String} */
	, getType: function(){
        return this.type;
    }
	
	/** Set the QtiVariable identifier
	 * @return {String} */
	, getIdentifier: function(){
        return (this.identifier);
    } 
	
	/** Check if the variable is null
	 * @return {boolean} */
	, isNull: function () {
		if (this.values.length == 0){
			return true;
		} else {
			return false;
		} 
	}
	
	/** Set the QTIVariable cardinality
	 * @param {String} cardinality */
	, setCardinality: function(cardinality){
        this.cardinality = cardinality;
    } 
	
	/** Set the QtiVariable identifier 
	 * @param {String} identifier */
	, setIdentifier: function(identifier){
        this.identifier = identifier;
    } 
	
	/** Set the QtiVariable type 
	 * @param {String} type */
	, setType: function(type){
        this.type = type;
    } 
	
	/** Match the QTIVariable with another QTIVariable
	 * @param {QTIVariable} qtiVar 
	 * @return {boolean} If the two variable are the same return true, else false.
	 * If the type or the cardinality of the both variables are different return false */
    , match: function (qtiVar) {
        // Check if the vars to match have the same type
        if (this.getType() != qtiVar.getType()) 
            return false;
        // Check if the vars to match have the same cardinality
		else if (this.getCardinality() != qtiVar.getCardinality ())
			return false;

        return _match(this.values, qtiVar.values, (this.getCardinality()=='ordered'?true:false));
    }
	
	/** Map the QTIVariable with a QTIMap and return a score
	 * @param {QTIMap} qtiMap 
	 * @return {float|integer} return a score according to the QTIMap type, return null if the QTIMap 
	 * and this QTIVar have not the same type or not the same cardinality */
	, map : function ( qtiMap ) {
        // Check if the vars to map and the qtiMap have the same type
        if (this.type != qtiMap.type) 
            return null;
		// Check that the Qti var is well a QTIMap type
		if (!qtiMap instanceof QTIMap)
			return null;
		
		return qtiMap.map (this);
	}
	/** Set values of the QtiVariable
	 * @param {Object} values */
	, setValues: function(values){
		this.values = values;
    }
	/** Serialize the QTIVariable in JSON string
	 * @return {String} */
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

/** QTI Map represents
 * @param {Object} identifier
 * @param {Object} type
 * @param {Object} cardinality */
var QTIMap = QTIVariable.extend ({
	init: function ( identifier, type, cardinality, values) {
		this._super (identifier, type, cardinality);
		this.setValues (values);
	}
	
	/** Map the QTIMap with a QTIVariable
	 * @param {Object} qtiVar
	 * @todo the map function in the qtiVariable object should disappear
	 * @todo make a better comment */
	, map : function ( qtiVar) {
		var score = 0;
	    var varFound = [];

	    for (var i in this.values) {
	        for (var j in qtiVar.values) {
				var resMatch = null;

				if (qtiVar.getType() == 'pair' || qtiVar.getType() == 'directedPair' || qtiVar.getType() == 'point')
	            	resMatch = _match(this.values[i][0], qtiVar.values[j]);
				else
					resMatch = this.values[i][0] == qtiVar.values[j];
					
//				console.log (i+' _match('+this.values[i][0]+', '+qtiVar.values[j]+') ' + varFound[i]+' '+resMatch);
				if (resMatch && (typeof varFound[i] == 'undefined')) {
	                varFound[i] = true;
	                score +=this.values[i][1];
	            }
	        }
	    }
		
	    return score;
	}
});

/** QTIPair */
var QTIPair = QTIVariable.extend(
/** @lends QTIPair.prototype */
{
    
	/** Constructor like */
	init: function ( identifier, type, cardinality, values, directed ) {
        this._super (identifier, type, cardinality);
        this.setValues (values);
		this.directed = typeof (directed) != undefined ? directed : false;
    }
	/** Match a QTIPair with another QTIPair
	 * @param {QTIPair} qtiPair
	 * @return {boolean} true if the both QTIPair are similar, else false.
	 * Return false if the both QTIPair have not the same type or have not the same cardinality */
    , match: function ( qtiPair ) {
        var result = true;
		
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

/** QTIString */
var QTIString = QTIVariable.extend(
/** @lends QTIString.prototype */
{
	/** Constructor like */
    init: function ( identifier, type, cardinality, values ) {
        this._super (identifier, type, cardinality);
        this.setValues (values);
    }
});

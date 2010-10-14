/** TAOMatching_Variable represents a variable manipulated by the matching engine
 * @param {Object} value The value of the variable
 * @abstract */
var TAOMatching_BaseTypeVariable = function (value) {
	
    this.value = null;
	this.value = value;
	
	/** Get the type of the variable
	 * @return {String} 
	 * @public */
	this.getType: function(){
        return typeof this.value;
    }
	
	/** Check if the variable is null
	 * @return {boolean} */
	, isNull: function () {
		return this.value == null ? true : false;
	}
	
	/** 
	 */
	
	/** Match the QTIVariable with another QTIVariable
	 * @param {QTIVariable} qtiVar 
	 * @return {boolean} If the two variable are the same return true, else false.
	 * If the baseType or the cardinality of the both variables are different return false */
    , match: function (qtiVar) {
        // Check if the vars to match have the same baseType
        if (this.getBaseType() != qtiVar.getBaseType()) 
            return false;
        // Check if the vars to match have the same cardinality
		else if (this.getCardinality() != qtiVar.getCardinality ())
			return false;

        return _match(this.values, qtiVar.values, (this.getCardinality()=='ordered'?true:false));
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
			identifier:'"+this.getIdentifier()+"' \
			, baseType:'"+this.getBaseType()+"' \
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
 * @param {String} identifier
 * @param {String} baseType
 * @param {String} cardinality */
var QTIMap = QTIVariable.extend ({
	init: function ( identifier, baseType, cardinality, values) {
		this._super (identifier, baseType, cardinality);
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

				if (qtiVar.getBaseType() == 'pair' || qtiVar.getBaseType() == 'directedPair' || qtiVar.getBaseType() == 'point')
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
	init: function ( identifier, baseType, cardinality, values, directed ) {
        this._super (identifier, baseType, cardinality);
        this.setValues (values);
		this.directed = typeof (directed) != undefined ? directed : false;
    }
	/** Match a QTIPair with another QTIPair
	 * @param {QTIPair} qtiPair
	 * @return {boolean} true if the both QTIPair are similar, else false.
	 * Return false if the both QTIPair have not the same baseType or have not the same cardinality */
    , match: function ( qtiPair ) {
        var result = true;
		
		// Check if the vars to match have the same baseType
        if (this.getBaseType() != qtiPair.getBaseType()) 
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
var QTIBasicType = QTIVariable.extend(
/** @lends QTIBasicType.prototype */
{
	/** Constructor like */
    init: function ( identifier, baseType, cardinality, values ) {
        this._super (identifier, baseType, cardinality);
        this.setValues (values);
    }
});

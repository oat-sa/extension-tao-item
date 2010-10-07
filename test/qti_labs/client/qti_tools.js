// Array match
// != vars' length -> return false
// last argument : Ordered match or not
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
            if (!result2) {
                result = false;
                break;
            }
        }
    }
	
    return result;
};

Array.prototype.indexOf = function(elt /*, from*/){
    var len = this.length;
    
    var from = Number(arguments[1]) || 0;
    from = (from < 0) ? Math.ceil(from) : Math.floor(from);
    if (from < 0) 
        from += len;
    
    for (; from < len; from++) {
        if (from in this &&
        this[from] === elt) 
            return from;
    }
    return -1;
};

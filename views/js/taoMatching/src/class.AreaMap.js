TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

TAO_MATCHING.AreaMap = function (data) {
    // Set the value of the variable
    this.setValue (data);
};

TAO_MATCHING.AreaMap.prototype = {
    /**
     * Short description of method map
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return double
     */
    map : function (matchingVar) {
        var returnValue = 0.0;
        var mapKeyFound = [];
                
        // for each map element, check if it is represented in the given variable
        for (var mapKey in this.value) {
            
            // Uniq point
            if (matchingVar instanceof TAO_MATCHING.Tuple) {
                if (this.value[mapKey].key.contains (matchingVar)){
                    returnValue = this.value[mapKey].value;
                }
            } 
            // Collection of points
            else if (matchingVar instanceof TAO_MATCHING.List) {
                for (var varKey in matchingVar.value) {
                    // If one match the current map value
                    if (this.value[mapKey].key.contains (matchingVar.value[varKey])){
                        returnValue += this.value[mapKey].value;
                    }
                }
            }
        }
        
        return returnValue;
    }
    
    /**
     * Short description of method setValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return mixed
     */
    , setValue : function (data) {
        this.value = [];
        for (var i in data){
            this.value.push ({
                "value":data[i].value
                , "key":TAO_MATCHING.VariableFactory.create(data[i].key)
            });
        }
    }
};


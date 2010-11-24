TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

/**
 * 
 */
TAO_MATCHING.MatchingRemote = function (url, params, pOptions) {
	var options = {
		"evaluateCallback" : null
	}; if (typeof (pOptions) != 'undefined') $.extend (options, pOptions);
	
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
     * Short description of attribute url
     *
     * @access protected
     * @var Variable
     */
    this.url = url;
	
   	/**
     * Short description of attribute params
     *
     * @access protected
     * @var Variable
     */
    this.params = params;
	
   	/**
     * Short description of attribute options
     *
     * @access protected
     * @var Variable
     */
    this.options = options;
}

TAO_MATCHING.MatchingRemote.prototype = {
    /**
     * Eval the rule with the remote maching engine
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    evaluate : function ()
    {
		var self = this;
		var responses = this.responses;
		
		//console.log (this.responses);
		//console.dir (JSON.stringify(this.responses));
		var requestParams = $.extend({}, this.params, {data: JSON.stringify(this.responses) });
	
		$.ajax ({
			url          : this.url
			, type       : 'POST'
			, async      : true
			, dataType   : 'json'
			, data       : requestParams
			, success    : function (data){
				self.outcomes = data;
				if (self.options.evaluateCallback!=null){
                    self.options.evaluateCallback (data);   
				}
			}
		});
    }
	
    /**
     * Short description of method getJSonOutcomes
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    , outcomesToJSON : function ()
    {
        return this.outcomes;
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
                    throw new Error ('TAO_MATCHING.Matching::setResponses a response variable with the identifier '+ data[key].identifier +' exists yet');
                this.responses.push (data[key]);
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
     * @return {Array}
     */
    , getResponses : function()
    {        
    	return this.responses;
    }
};

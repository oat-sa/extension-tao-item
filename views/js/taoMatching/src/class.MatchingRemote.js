TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

/**
 * @class
 * 
 * Matching class provides a bridge from the client to the server side Matching engine.
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package TAO_MATCHING
 * 
 * @constructor 
 * @param string url Url of the remote matching engine.
 * @params array params Array of parameters to pass to the remote engine [optional]
 * @params array pOptions Optional parameters
 * @params function pOptions.evaluateCallback Fire this callback when the evaluation has been done
 */
TAO_MATCHING.MatchingRemote = function (url, params, pOptions) {
	var options = {
		"evaluateCallback" : null
	}; if (typeof (pOptions) != 'undefined') $.extend (options, pOptions);
	
    /**
     * Outcomes will be used to store the defined outcome variables
     *
     * @access protected
     * @var Variable
     */
    this.outcomes = [];

    /**
     * Responses will store the user's response variables
     *
     * @access protected
     * @var Variable
     */
    this.responses = [];
	
   	/**
     * Url of the remote matching engine
     *
     * @access protected
     * @var Variable
     */
    this.url = url;
	
   	/**
     * Parameters to pass to remote matching engine
     *
     * @access protected
     * @var Variable
     */
    this.params = params;
	
   	/**
     * Options of the client bridge
     *
     * @access private
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
     * Get the outcome variables 
     *
     * @access public
     * @param {string} options (not used here but usefull in the local matching engine)
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    , getOutcomes : function (options)
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
		console.log('a', data);
    }
    
    /**
     * Get the collected responses
     *
     * @access public
     * @return {Array}
     */
    , getResponses : function()
    {        
    	return this.responses;
    }
};

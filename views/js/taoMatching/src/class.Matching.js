TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};
TAO_MATCHING.VARIABLE = typeof TAO_MATCHING.VARIABLE != 'undefined' ? TAO_MATCHING.VARIABLE : {};

TAO_MATCHING.VARIABLE.Matching = function () {
    /**
     * Short description of attribute corrects
     *
     * @access protected
     * @var Variable
     */
    this.corrects = null;

    /**
     * Short description of attribute outcomes
     *
     * @access protected
     * @var Variable
     */
    this.outcomes = null;

    /**
     * Short description of attribute responses
     *
     * @access protected
     * @var Variable
     */
    this.responses = null;

    /**
     * Short description of attribute rule
     *
     * @access protected
     * @var string
     */
    this.rule = '';

    /**
     * Short description of attribute whiteFunctionsList
     *
     * @access public
     * @var array
     */
    this.whiteFunctionsList = {
		'and'				:{'mappedFunction':'andExpression'}
		, 'equal'			:{}
		, 'if'				:{'prefix':false}
		, 'isNull'			:{}
		, 'getCorrect'		:{}
		, 'getMap'			:{}
		, 'getResponse'		:{}
		, 'mapResponse'		:{}
		, 'match'			:{}
		, 'setOutcomeValue'	:{}	
	};	
}

TAO_MATCHING.VARIABLE.Matching.prototype = {
    /**
     * Eval the stored response processing rule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    evaluate : function ()
    {
        try {
			eval (this.getRule());
		} catch (e) {
			throw new Error ('an error occured during the evaluation of the rule : '.e);
		}
    }
	
	/**
     * Set the correct variables of the item
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @return mixed
     */
    , setCorrects : function (data)
    {
    	if ( typeof data != 'array')
    		throw new Error ('TAO_MATCHING.VARIABLE.Matching::setCorrects is waiting on an array, a '+ (typeof data) +' is given');

		for (var key in correct) {
			try {
				if (typeof this.corrects[correct.identifier] != 'undefined')
					throw new Error ('TAO_MATCHING.VARIABLE.Matching::setCorrects a correct variable with the identifier '+ correct.identifier +' exists yet');
				var matchingVar = TAO_MATCHING.VARIABLE.VariableFactory.create (correct.value);
				this.corrects[correct.identifier] = matchingVar;
			} 
			catch (e) {
				throw e;
			}
		}
    }
};

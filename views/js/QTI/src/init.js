/**
 * TAO QTI API
 * 
 * This script provides the initialization function to build QTI component
 * from an XHTML source
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @requires jquery {@link http://www.jquery.com}
 */

/**
 * The qti_initParam var is used everywhere in the QTI document to collect the interactions parameters
 * @example <code>qti_initParam['interaction_serial_1234'] = {id : 'interaction_1', type : 'qti_order_interaction',  responseIdentifier : 'RESPONSE'}</code> 
 * @type {Object}
 */
var qti_initParam  	= new Object();

/**
 * Initialize the QTI environment
 * 
 * @param {Object} qti_initParam the parameters of ALL item's interaction
 * @return void
 */
function qti_init(qti_initParam){
	for (var a in qti_initParam){
		qti_init_interaction(qti_initParam[a]);
	}
}

/**
 * Initialize the widget and the result collection for an interaction
 * 
 * @param {Object} initObj the params of the interaction (parts of qti_initParam identified by the interaction id)
 */
function qti_init_interaction(initObj){
	
	//instantiate the widget class with the given interaction parameters
	var myQTIWidgetFactory = new QTIWidgetFactory(initObj);
	
	//instantiate the result class with the given interaction parameters
	var myResultCollector = new QTIResultCollector(initObj);
	
	//get the interaction type to identify the method 
	var typeName = initObj["type"].replace('qti_', '').replace('_interaction', '');
	
	//call the widget initialization method
	myQTIWidgetFactory.build(typeName);
	
	// validation process
	$("#qti_validate").bind("click",function(e){
		e.preventDefault();
		
		$('body').css('cursor', 'wait');
		
		// Get user's data
		var result = myResultCollector[typeName].apply();
		// Set the matching engine with the user's data	
		if(typeof(matchingSetResponses) == 'function'){
			matchingSetResponses ([result]);
		}
	});
}

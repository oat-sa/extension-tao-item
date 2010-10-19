TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

/**
 * Matching API interface.
 * Provides functions to manage the communication with a the TAO matching engine from an XHTML item.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 *
 */

/**
 * The tao matching object
 */
TAO_MATCHING.taoMatching = null;

/////////////////////
// TAO Matching //
///////////////////

/**
 * Init the TAO Matching Engine
 * @param {JSON} params Set of optional parameters used to init the matching engine
 * @param {Array} params.corrects Collection of correct responses
 * @param {Array} params.maps Collection of maps
 * @param {string} params.rule The rule to use to evaluate the testee
 */
function matching_init (params) {
	var options = {
		mode 		:null
		, outcomes	:[]
		, corrects 	:[]
		, maps 		:[]
		, rule 		:""
	}; $.extend (options, params);

	// Test the mode
	if ($.inArray(options.mode, ["client", "server"]) < 0) {
		throw new Error("matching_init an error occured : the mode is not well defined. Allowed modes [client/server], " + options.mode + " given");
	}
	
	// Init the maching engine
	TAO_MATCHING.taoMatching = new TAO_MATCHING.Matching ();

	// Set the rule
	if ($.trim(options.rule) != "") {
		matching_setRule($.trim(options.rule));
	}
	else {
		throw new Error("matching_init an error occured : the rule is empty");
	}

	// Set outcomes variables
	if (options.outcomes.length){
		matching_setOutcomes (options.outcomes);
	}

	// Set correct variables
	if (options.corrects.length){
		matching_setCorrects (options.corrects);
	}

	// Set mapping variables
	if (options.maps.length){
		matching_setMaps (options.maps);
	}
}

/**
 * Evaluate the rule
 */
function matching_evaluate () {
	return TAO_MATCHING.taoMatching.evaluate ();
}

/**
 * Get the outcomes generated after the rule evaluation
 * @return {JSON}
 */
function matching_getOutcomes () {
	return TAO_MATCHING.taoMatching.getJSonOutcomes ();
}

/**
 * Set the correct responses of the item
 * @param {JSON} data The correct responses
 */
function matching_setCorrects (data) {
	return TAO_MATCHING.taoMatching.setCorrects (data);
}

/**
 * Set the mapping of the item
 * @param {JSON} data The map
 */
function matching_setMaps (data) {
	return TAO_MATCHING.taoMatching.setMaps (data);
}

/**
 * Set the outcome variables of the item
 * @param {JSON} data The outcome variables
 */
function matching_setOutcomes (data) {
	return TAO_MATCHING.taoMatching.setOutcomes (data);
}

/**
 * Set the user' responses
 * @param {JSON} data The response variables
 */
function matching_setResponses (data) {
	return TAO_MATCHING.taoMatching.setResponses (data);
}

/**
 * Set the rule of the item
 * @param {string} rule The rule
 */
function matching_setRule (rule) {
	TAO_MATCHING.taoMatching.setRule (rule);
}

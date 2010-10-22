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
 * The tao matching engine instance
 */
TAO_MATCHING.engine = null;

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
		"url" : null
		, "params" : null
		, "data" : null
		, "format" : "json"
		, "options" : null
	}; if (typeof (params) != 'undefined') $.extend (options, params);

	// If the matching will be make with a remote engine
	if (options.url != null) {
		TAO_MATCHING.engine = new TAO_MATCHING.MatchingRemote (options.url, options.params, options.options);
	}
	
	// If the matching will be make locally
	else if (options.data != null){
		TAO_MATCHING.engine = new TAO_MATCHING.Matching (options.data, options.options);
	}
	
	// Else options are not well formed
	else {
		throw new Error("matching_init an error occured : the options are not well formed, data or url have to be defined");
	}
}

/**
 * Evaluate the rule
 */
function matching_evaluate () {
	TAO_MATCHING.engine.evaluate ();
}

/**
 * Get the outcomes generated after the rule evaluation
 * @return {JSON}
 */
function matching_getOutcomes () {
	return TAO_MATCHING.engine.outcomesToJSON ();
}

/** Set the correct responses of the item
 * @param {JSON} data The correct responses
 */
function matching_setCorrects (data) {
	TAO_MATCHING.engine.setCorrects (data);
}

/**
 * Set the mapping of the item
 * @param {JSON} data The map
 */
function matching_setMaps (data) {
	TAO_MATCHING.engine.setMaps (data);
}

/**
 * Set the outcome variables of the item
 * @param {JSON} data The outcome variables
 */
function matching_setOutcomes (data) {
	TAO_MATCHING.engine.setOutcomes (data);
}

/**
 * Set the user' responses
 * @param {JSON} data The response variables
 */
function matching_setResponses (data) {
	TAO_MATCHING.engine.setResponses (data);
}

/**
 * Set the rule of the item
 * @param {string} rule The rule
 */
function matching_setRule (rule) {
	TAO_MATCHING.engine.setRule (rule);
}

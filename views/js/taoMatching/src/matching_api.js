/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
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
function matchingInit (pParams) {
	var params = {
		"url" : null
		, "params" : null
		, "data" : null
		, "format" : "json"
		, "options" : null
	}; if (typeof (pParams) != 'undefined') $.extend (params, pParams);

	// If the matching will be make with a remote engine
	if (params.url != null) {
		TAO_MATCHING.engine = new TAO_MATCHING.MatchingRemote (params.url, params.params, params.options);
	}
	
	// If the matching will be make locally
	else if (params.data != null){
		TAO_MATCHING.engine = new TAO_MATCHING.Matching (params.data, params.options);
	}
	
	// Else options are not well formed
	else {
		throw new Error("matching_init an error occured : the options are not well formed, data or url have to be defined");
	}
}

/**
 * Evaluate the rule
 */
function matchingEvaluate () {
	TAO_MATCHING.engine.evaluate ();
}

/**
 * Get the outcomes generated after the rule evaluation
 * @return {JSON}
 */
function matchingGetOutcomes () {
	return TAO_MATCHING.engine.getOutcomes({format:'JSON'});
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
function matchingSetMaps (data) {
	TAO_MATCHING.engine.setMaps (data);
}

/**
 * Set the outcome variables of the item
 * @param {JSON} data The outcome variables
 */
function matchingSetOutcomes (data) {
	TAO_MATCHING.engine.setOutcomes (data);
}

/**
 * Set the user' responses
 * @param {JSON} data The response variables
 */
function matchingSetResponses (data) {
	TAO_MATCHING.engine.setResponses(data);
}

/**
 * Get the user' responses
 * @return {Object} The response variables
 */
function matchingGetResponses () {
	return TAO_MATCHING.engine.getResponses({format:'JSON'});
}

/**
 * Set the rule of the item
 * @param {string} rule The rule
 */
function matchingSetRule (rule) {
	TAO_MATCHING.engine.setRule (rule);
}

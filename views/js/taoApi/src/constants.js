/**
 * TAO API constants file.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @requires jquery >= 1.4.0 {@link http://www.jquery.com}
 */

/**
 * This object is used for a convenience translation of URIS
 * <i>the ##NAMESPACE# tag means the local namespace</i>
 *  
 * @type {Object}
 * @constant 
 */
const URI = {
	'LABEL'				: 'http://www.w3.org/2000/01/rdf-schema#label',
	'ENDORSMENT' 		: '##NAMESPACE#ENDORSMENT',
	'SCORE' 			: '##NAMESPACE#SCORE',
	'SCORE_MIN' 		: '##NAMESPACE#SCORE_MIN',
	'SCORE_MAX' 		: '##NAMESPACE#SCORE_MAX',
	'ANSWERED_VALUES'	: '##NAMESPACE#ANSWERED_VALUES',
	'SUBJECT'			: 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject',
	'SUBJETC_LOGIN'		: 'http://www.tao.lu/Ontologies/generis.rdf#login',
	'SUBJETC_FIRSTNAME'	: 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName',
	'SUBJETC_LASTNAME'	: 'http://www.tao.lu/Ontologies/generis.rdf#userLastName',
	'ITEM'				: 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
	'PROCESS'			: 'http://www.tao.lu/middleware/taoqual.rdf#i119010455660544',
	'TEST'				: 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test',
	'DELIVERY'			: 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery'
};

/**
 * This object is used for a convenience translation of the differents states
 *  
 * @type {Object}
 * @constant 
 */
const STATE = {
	'ITEM' : {
		'PRE_FINISHED' 	: 'pre_item_finished',
		'FINISHED' 		: 'item_finished',
		'POST_FINISHED' : 'post_item_finished'
	}
};

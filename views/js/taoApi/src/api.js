/**
 * TAO API interface.
 * Provides functions to manage the communication with a TAO context from an XHTML item.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 *
 */

/**
 * instanciate the TaoStack object
 * @see core.js 
 */
var taoStack = new TaoStack();

/////////////////////
// TAO  variables //
///////////////////

/**
 * @return {bool}
 */
function getEndorsement(){
	return taoStack.getTaoVar(URI.ENDORSMENT);
}

/**
 * @param {bool} endorsement
 */
function setEndorsement(endorsement){
	taoStack.setTaoVar(URI.ENDORSMENT, (endorsement == true));
}

/**
 * @return {Object} subject
 */
function getSubject(){
	return taoStack.getTaoVar(URI.SUBJECT);
}

/**
 * @return {Object} subject
 */
function getSubjectLogin(){
	var subject = getSubject();
	return (subject) ? subject[URI.SUBJETC_LOGIN] : false;
}

/**
 * @return {Object} subject
 */
function getSubjectName(){
	var subject = getSubject();
	return (subject) ? subject[URI.SUBJETC_FIRSTNAME] + ' ' + subject[URI.SUBJETC_LASTNAME] : false;
}

/**
 * @return {Object} subject
 */
function getItem(){
	return taoStack.getTaoVar(URI.ITEM);
}

/**
 * @return {Object} test
 */
function getTest(){
	return taoStack.getTaoVar(URI.TEST);
}

/**
 * @return {Object} delivery
 */
function getDelivery(){
	return taoStack.getTaoVar(URI.DELIVERY);
}

/**
 * @return {Object} process execution
 */
function getProcessExecution(){
	return taoStack.getTaoVar(URI.PROCESS);
}

/////////////////////
// user variables //
///////////////////

/**
 * @param {String} key
 * @return {String|int|float|bool}
 */
function getUserVar(key){
	return taoStack.getUserVar(key);
}

/**
 * @param {String} key
 * @param {String|int|float|bool} value
 */
function setUserVar(key, value){
	taoStack.setUserVar(key, value);
}


////////////////////////////
// EVENTS to be defined  //
//////////////////////////

/**
 * @param {Event} e
 */
function setEvent(e){}


/////////////////////////////
// GENERIS to be defined  //
///////////////////////////


function createVar(){}


/////////////////////////////
// INTERFACE COMMUNICATION//
///////////////////////////


/**
 * @param {Object} environment
 * @param {Object} settings
 */
function initDataSource(environment, settings){
	taoStack.initDataSource(environment, settings, null);
}

/**
 * @param {Object} environment
 * @param {Object} settings
 */
function initManualDataSource(source){
	taoStack.initDataSource({type: 'manual'}, null, source);
}


/**
 * @param {Object} environment
 * @param {Object} settings
 */
function initPush(environment, settings){
	taoStack.initPush(environment, settings);
}


/**
 * @return {bool}
 */
function push(){
	taoStack.push();
}
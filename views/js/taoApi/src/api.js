/**
 * TAO API interface.
 * Provides functions to manage the communication with a TAO context from an XHTML item.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @requires jquery >= 1.4.0 {@link http://www.jquery.com}
 */


/**
 * instanciate the TaoStack object
 * @see TaoStack
 */
var taoStack = new TaoStack();

  ////////////////////
 // TAO  variables //
////////////////////


/**
 * Get the endorsment of the item
 * 
 * @namespace taoApi
 * @returns {boolean}
 */
function getEndorsement(){
	return taoStack.getTaoVar(URI.ENDORSMENT);
}

/**
 * Set the endorsment of the item
 * 
 * @namespace taoApi
 * @param {boolean} endorsement
 */
function setEndorsement(endorsement){
	taoStack.setTaoVar(URI.ENDORSMENT, (endorsement == true));
}

/**
 * Get the data of the user currently doing the item  (the subject)
 * 
 * @namespace taoApi
 * @returns {Object} all the data related to the subject
 */
function getSubject(){
	return taoStack.getTaoVar(URI.SUBJECT);
}

/**
 * Get the login of the subject
 * 
 * @namespace taoApi
 * @returns {String} the subject's login
 */
function getSubjectLogin(){
	var subject = getSubject();
	return (subject) ? subject[URI.SUBJETC_LOGIN] : '';
}

/**
 * Get the name of the subject (firstname and lastname)
 * 
 * @namespace taoApi
 * @returns {Object} the subject's name
 */
function getSubjectName(){
	var subject = getSubject();
	return (subject) ? subject[URI.SUBJETC_FIRSTNAME] + ' ' + subject[URI.SUBJETC_LASTNAME] : '';
}

/**
 * Get the current item's informations 
 * 
 * @namespace taoApi
 * @returns {Object} the item's data (uri, label)
 */
function getItem(){
	return taoStack.getTaoVar(URI.ITEM);
}


/**
 * Get the informations of the currently running test 
 * 
 * @namespace taoApi
 * @returns {Object} the test's data (uri, label)
 */
function getTest(){
	return taoStack.getTaoVar(URI.TEST);
}

/**
 * Get the informations of the current delivery
 * 
 * @namespace taoApi
 * @returns {Object} the delivery's data (uri, label)
 */
function getDelivery(){
	return taoStack.getTaoVar(URI.DELIVERY);
}


  //////////////////////
 // User's variables //
//////////////////////

/**
 * This function enables you to create and edit custom variables: the <i>user's variables</i>
 * The variable is identified by a key you have chosen.
 * This variable will be saved temporarly into the taoApi.
 * When you call the <code>push()</code> function, the <i>user's variables</i> are sent to the server.
 * It's a way to record some data other than the results and the events.
 * 
 * @namespace taoApi
 * @param {String} key to identify of the variable
 * @param {String|number|boolean} the value of the variable
 */
function setUserVar(key, value){
	taoStack.setUserVar(key, value);
}

/**
 * Get a previously defined user's variable.
 * 
 * @namespace taoApi
 * @param {String} key the key of the variable you want to retrieve
 * @returns {String|number|boolean}
 */
function getUserVar(key){
	return taoStack.getUserVar(key);
}




  ///////////////////////////
 // EVENTS to be defined  //
///////////////////////////

/**
 * @namespace taoApi
 * @param {Event} e
 */
function setEvent(e){}


  ////////////////////////////
 // GENERIS to be defined  //
////////////////////////////


function createVar(){}


  //////////////////////////////
 // INTERFACE COMMUNICATION  //
//////////////////////////////

/**
 * Get the communication token (this token is sent at each communication)
 * 
 * @namespace taoApi
 * @returns {String} the token
 */
function getToken(){
	return taoStack.getTaoVar('token');
}

/**
 * This fuction enables you to set up the data the item need.
 * You can retrieve this data from either a remote or a manual source.
 * If you don't need to change the default values, don't call this function. 
 * 
 * @namespace taoApi
 * 
 * @param {Object} environment <i>set to null if you want to keep all the default values</i>
 * @param {String] [environment.type = "async"] the datasource type <b>(manual|sync|async)</b> 
 * @param {String} [environment.url = "/tao/Api/getContext"] the url of the server where the data are sent 
 * @param {Object} [environment.params] the additional parameters to send with the data
 * 
 * @param {Object} settings <i>set to null if you want to keep all the default values</i>
 * @param {String} [settings.format = "json"] the data format. <i>Only json is supported in the current version</i> 
 * @param {String} [settings.method = "post"] HTTP method to push the data <b>(get|post)</b>
 */
function initDataSource(environment, settings){
	taoStack.initDataSource(environment, settings, null);
}

/**
 * This function is a convenience method to add directly the datasource 
 * by writing the data in the source object (JSON) 
 *   
 * @namespace taoApi
 * @param {Object} source
 */
function initManualDataSource(source){
	taoStack.initDataSource({type: 'manual'}, null, source);
}


/**
 * Initialize the push communication.
 * If you don't need to change the default values, don't call this function. 
 * 
 * @namespace taoApi
 * 
 * @param {Object} environment <i>set to null if you want to keep all the default values</i>
 * @param {String} [environment.url = "/tao/Api/save"] the url of the server where the data are sent 
 * @param {Object} [environment.params] the additional parameters to send with the data
 * 
 * @param {Object} settings <i>set to null if you want to keep all the default values</i>
 * @param {String} [settings.format = "json"] the data format. <i>Only json is supported in the current version</i> 
 * @param {String} [settings.method = "post"] HTTP method to push the data <b>(get|post)</b>
 * @param {boolean}[settings.async = true]	if the request is asynchronous
 * @param {boolean}[settings.clearAfter= true]	if the variables stacks are cleared once the push is done
 */
function initPush(environment, settings){
	taoStack.initPush(environment, settings);
}


/**
 * This method enables you to push the data to the server.
 * 
 * 
 * @namespace taoApi
 * @returns {bool}
 */
function push(){
	taoStack.push();
}

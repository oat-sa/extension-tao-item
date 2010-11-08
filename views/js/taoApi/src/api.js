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
 * @function
 * @namespace taoApi
 * @returns {boolean}
 */
function getEndorsment(){
	return taoStack.getTaoVar(URI.ENDORSMENT);
}

/**
 * Set the endorsment of the item
 * 
 * @function
 * @namespace taoApi
 * @param {boolean} endorsment
 */
function setEndorsment(endorsment){
	taoStack.setTaoVar(URI.ENDORSMENT, (endorsment === true));
}

/**
 * Get the score of the item 
 * 
 * @function
 * @namespace taoApi
 * @returns {String|Number}
 */
function getScore(){
	return taoStack.getTaoVar(URI.SCORE);
}

/**
 * Set the final score of the item
 * 
 * @function
 * @namespace taoApi
 * @param {String|Number} score
 */
function setScore(score){
	if(isScalar(score)){
		if(typeof(score) == 'boolean'){
			(score === true) ? score = 1 : score = 0;
		}
		taoStack.setTaoVar(URI.SCORE, score);
	}
}

/**
 * Get the values answered by the subject 
 * 
 * @function
 * @namespace taoApi
 * @returns {boolean}
 */
function getAnsweredValues(){
	return taoStack.getTaoVar(URI.LISTNERVALUE);
}

/**
 * Set the values answered by the subject.
 * If the item contains a free text field, 
 * you can record here the complete response. 
 * 
 * @function
 * @namespace taoApi
 * @param {boolean} endorsment
 */
function setAnsweredValues(values){
	taoStack.setTaoVar(URI.LISTNERVALUE, values);
}

/**
 * Get the data of the user currently doing the item  (the subject)
 * 
 * @function
 * @namespace taoApi
 * @returns {Object} all the data related to the subject
 */
function getSubject(){
	return taoStack.getTaoVar(URI.SUBJECT);
}

/**
 * Get the login of the subject
 * 
 * @function
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
 * @function
 * @namespace taoApi
 * @returns {Object} the subject's name
 */
function getSubjectName(){
	var subject = getSubject();
	if(subject){
		return subject[URI.SUBJETC_FIRSTNAME] + ' ' + subject[URI.SUBJETC_LASTNAME];
	}
	return '';
}

/**
 * Get the current item's informations 
 * 
 * @function
 * @namespace taoApi
 * @returns {Object} the item's data (uri, label)
 */
function getItem(){
	return taoStack.getTaoVar(URI.ITEM);
}


/**
 * Get the informations of the currently running test 
 * 
 * @function
 * @namespace taoApi
 * @returns {Object} the test's data (uri, label)
 */
function getTest(){
	return taoStack.getTaoVar(URI.TEST);
}

/**
 * Get the informations of the current delivery
 * 
 * @function
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
 * @function
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
 * @function
 * @namespace taoApi
 * @param {String} key the key of the variable you want to retrieve
 * @returns {String|number|boolean}
 */
function getUserVar(key){
	return taoStack.getUserVar(key);
}


  /////////////
 // STATES  //
/////////////

if(typeof(finish) != 'function'){

	//get the highest context 
	var _stateContext =  window;
	
	/**
	 * Define the item's state as finished.
	 * This state can have some consequences.
	 * 
	 * @function
	 * @namespace taoApi
	 */
	function finish(){
		$(_stateContext).trigger(STATE.ITEM.PRE_FINISHED);
		$(_stateContext).trigger(STATE.ITEM.FINISHED);
		$(_stateContext).trigger(STATE.ITEM.POST_FINISHED);
	}
	
	/**
	 * Add a callback that will be executed on finish state.
	 * 
	 * @function
	 * @namespace taoApi
	 * @param {function} callback
	 */
	function onFinish(callback){
		$(_stateContext).bind(STATE.ITEM.FINISHED, callback);
	}
	
	/**
	 * Add a callback that will be executed on finish but before the other callbacks  
	 * 
	 * @function
	 * @namespace taoApi
	 * @param {function} callback
	 */
	function beforeFinish(callback){
		$(_stateContext).bind(STATE.ITEM.PRE_FINISHED, callback);
	}
	
	/**
	 * Add a callback that will be executed on finish but after the other callbacks  
	 * 
	 * @function
	 * @namespace taoApi
	 * @param {function} callback
	 */
	function afterFinish(callback){
		$(_stateContext).bind(STATE.ITEM.POST_FINISHED, callback);
	}

}

  //////////////////////////////
 // INTERFACE COMMUNICATION  //
//////////////////////////////

/**
 * Get the communication token (this token is sent at each communication)
 * 
 * @function
 * @namespace taoApi
 * @returns {String} the token
 */
function getToken(){
	return taoStack.dataStore.token;
}

/**
 * This fuction enables you to set up the data the item need.
 * You can retrieve this data from either a remote or a manual source.
 * <b>If you don't need to change the default values, don't call this function.</b>
 * 
 * @function
 * @namespace taoApi
 * 
 * @param {Object} environment <i>set to null if you want to keep all the default values</i>
 * @param {String} [environment.type = "async"] the datasource type <b>(manual|sync|async)</b> 
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
 * by writing the data in the source object (JSON) .
 *   
 * @function
 * @namespace taoApi
 * @param {Object} source
 */
function initManualDataSource(source){
	taoStack.initDataSource({type: 'manual'}, null, source);
}


/**
 * Initialize the push communication.
 * <b>If you don't need to change the default values, don't call this function.</b>
 * 
 * @function
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
 * @function
 * @namespace taoApi
 */
function push(){
	taoStack.push();
}

/*
 * By default, the variables are pushed when the item is finished
 */
beforeFinish(push);



/////////////
// EVENTS  //
/////////////

/**
* instanciate the EventTracer object
* @type EventTracer
*/
var eventTracer = new EventTracer();

/**
* Log the an <i>eventType</i> bound on <i>elementName</i> by sending the <i>data</i>.
* 
* @function
* @namespace taoApi
* @param {String} elementName an HTML tag name
* @param {String} eventType a JS User Events
* @param {mixed} data any data strucuture you want to trace
*/
function logEvent(elementName, eventType, data){
	eventTracer.feedTrace(elementName, eventType, new Date().getTime(), data);
}

/**
* Log the a <i>eventName</i> by sending the <i>data</i>
* 
* @function
* @namespace taoApi
* @param {String} eventName the name of the custom event
* @param {mixed} data 
*/
function logCustomEvent(eventName, data){
	eventTracer.feedTrace('BUSINESS', eventName, new Date().getTime(), data);
}

/**
 * Initialize the interfaces communication for the events logging.
 * The source service defines where and how we retrieve the list of events to catch
 * The destination service defines where and how we send the catched events 
 * 
 * @function
 * @namespace taoApi
 * 
 * @param {Object} source 
 * @param {String} [source.type = "sync"] the type of source <b>(sync|manual)</b>
 * @param {Object} [source.data] For the <i>manual</i> source type, set direclty the events list in the data 
 * @param {String} [source.url = "/taoDelivery/ItemDelivery/getEvents"] For the <i>sync</i> source type, the URL of the remote service
 * @param {Object} [source.params] the parameters to send to the sync service
 * @param {String} [source.format = "json"] the data format. <i>Only json is supported in the current version</i> 
 * @param {String} [source.method = "post"] HTTP method of the sync service <b>(get|post)</b>
 * 
 * @param {Object} destination
 * @param {String} [destination.url = "/taoResults/Server/traceEvents"] the URL of the remote service
 * @param {Object} [destination.params] the common parameters to send to the service
 * @param {String} [destination.format = "json"] the data format. <i>Only json is supported in the current version</i> 
 * @param {String} [destination.method = "post"] HTTP method of the service <b>(get|post)</b>
 */
function initEventServices(source, destination){
	
	extendedParams = {params: {token: getToken()}}; 
	
	eventTracer.initSourceService($.extend(true, source, extendedParams));
	
	eventTracer.initDestinationService($.extend(true, destination, extendedParams));
}

/*
 * By default, all the events are sent  when the item is finished
 */
beforeFinish(function(){
	eventTracer.sendAllFeedTrace_now();
});

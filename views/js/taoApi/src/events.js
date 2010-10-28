/**
 * NewarX core
 * 
 * 
 * @author CRP Henri Tudor - TAO Team 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package NewarXCore
 *
 */
// specific function about events


var eventPool = Array();// array of events arrays
var eventsToBeSend = Array();//array of strings
var POOL_SIZE = 500;// number of events to cache before sending

var eventsToBeSendCursor = -1;

var ctrlPressed = false;
var altPressed = false;


var time_limit_for_ajax_request = 2000;
var MIN_POOL_SIZE = 200;
var MAX_POOL_SIZE = 5000;

/**
* @description record events of interaction between interviewee and the test
* @function
* @param {Object} data event type list
*/
function setEventsToCatch(data)
{
	// retreive the list of events to catch or not to catch
	ATTRIBUTES_TO_CATCH = Array();
	if (data.type.length > 0)
	{
		EVENTS_TO_CATCH = {bubbling:[],nonBubbling:[]};
		if (data.type == 'catch')
		{
			for (i in data.list)
			{
				if (in_array(i,['click', 'dblclick', 'change', 'submit', 'select', 'mousedown', 'mouseup', 'mouseenter', 'mousemove', 'mouseout']))//if is bubbling event
				{
					EVENTS_TO_CATCH.bubbling.push(i);
				}
				else
				{
					EVENTS_TO_CATCH.nonBubbling.push(i);// else non bubbling event
				}
				ATTRIBUTES_TO_CATCH[i] = data.list[i];
			}
		}
		else
		{
			// no catch
			EVENTS_TO_CATCH = {bubbling:['click', 'dblclick', 'change', 'submit', 'select', 'mousedown', 'mouseup', 'mouseenter', 'mousemove', 'mouseout'], nonBubbling:['blur', 'focus', 'load', 'resize', 'scroll', 'keyup', 'keydown', 'keypress', 'unload', 'beforeunload', 'select', 'submit']};
			for (i in data.list)
			{
				remove_array(data.list[i].event,EVENTS_TO_CATCH.bubbling);
				remove_array(data.list[i].event,EVENTS_TO_CATCH.nonBubbling);
			}
		}
	}
	else
	{
		EVENTS_TO_CATCH = {bubbling:['click', 'dblclick', 'change', 'submit', 'select', 'mousedown', 'mouseup', 'mouseenter', 'mousemove', 'mouseout'], nonBubbling:['blur', 'focus', 'load', 'resize', 'scroll', 'keyup', 'keydown', 'keypress', 'unload', 'beforeunload', 'select', 'submit']};
	}
}


/*
function bindDom
recursive function
browse all children elements to bind them to the non-bubbling events
*/
/**
* @description bind every non bubbling events to dom elements.
* @function
*/
jQuery.fn.bindDom = function()
{
	$(this).bind( getEventsList(EVENTS_TO_CATCH.nonBubbling) ,eventStation);
	var dd = this;
	var childrens = $(this).children();
	if (childrens.length)// stop condition
	{
		childrens.bindDom();
	}
}

/**
* @description bind platform events
* @function
*/
function bind_platform()
{
	// for non bubbling events, link them to all the listened element
	// it is still useful to use delegation since it will remains much less listeners in the memory (just 1 instead of #numberOfElements)
	$('body').bindDom();

	// for bubbling events
	$('body').bind( getEventsList(EVENTS_TO_CATCH.bubbling) ,eventStation);
}



/**
* @description unbind platform events
* @function
*/
jQuery.fn.unBindDom = function()
{
	if (! $(this).hasClass('dialog_box_PForm'))
	{
		$(this).unbind( getEventsList(EVENTS_TO_CATCH.nonBubbling) ,eventStation);
	}
	var childrens = $(this).children();
	if (childrens.length)// stop condition
	{
		childrens.unBindDom();
	}
}

/**
* @description unbind platform events
* @function
*/
function unbind_platform()
{
	$('body').unbind(getEventsList(EVENTS_TO_CATCH.bubbling) ,eventStation);
	$('body').unBindDom();
}



/**
* @function
* @description set all information from the event to the pLoad
* @param {event} e dom event triggered
* @param {Object} pload callback function called when 'ok' clicked
*/
function describe_event(e,pload)
{
	if (e.target && (typeof(e.target['value']) != 'undefined') && (e.target['value'] != -1) && (e.target['value'] != ''))
	{
		pload['value'] = e.target['value'];
	}
	// get everything about the event
	for (var i in e)
	{
		if ((typeof(e[i]) != 'undefined') && (typeof(e[i]) != 'object') && (typeof(e[i]) != 'function') && (e[i] != ''))
		{
			if ((i != 'cancelable') && (i != 'contentEditable') && (i != 'cancelable') && (i != 'bubbles') && (i.substr(0,6) != 'jQuery'))
			{
				pload[i] = e[i];
			}
		}
	}
}

/**
* @function
* @description set unit info to the pLoad
* @param {Object} pload callback function called when 'ok' clicked
*/
function add_item_info(pLoad)
{
	pLoad['ACTIVITYID'] = ACTIVITYID;
	pLoad['ITEMID'] = ITEMID;
	pLoad['PROCESSURI'] = PROCESSURI;
	pLoad['LAYOUTDIRECTION'] = LAYOUTDIRECTION;
	pLoad['LANGID'] = LANGID;
}

/**
* @function
* @description set all information from the target dom element to the pLoad
* @param {event} e dom event triggered
* @param {Object} pload callback function called when 'ok' clicked
*/
function describe_element(e,pload)
{
	try
	{
		// take everything except useless attributes
		for (var i in e.target)
		{
			if ((typeof(e.target[i]) != 'undefined') && (typeof(e.target[i]) != 'object') && (typeof(e.target[i]) != 'function') && (e.target[i] != ''))
			{
				if ((i != 'textContent') && (i != 'namespaceURI') && (i != 'baseURI') && (i != 'innerHTML') && (i != 'defaultStatus')
				&& (i != 'fullScreen') && (i != 'UNITSMAP') && (i != 'PROCESSURI') && (i != 'LANGID') && (i != 'ITEMID') && (i != 'ACTIVITYID') && (i != 'DURATION')
				&& (i!='ELEMENT_NODE') && (i!='ATTRIBUTE_NODE')&& (i!='TEXT_NODE')&& (i!='CDATA_SECTION_NODE')&& (i!='ENTITY_REFERENCE_NODE')&& (i!='ENTITY_NODE')&& (i!='PROCESSING_INSTRUCTION_NODE')
				&& (i!='COMMENT_NODE') && (i!='DOCUMENT_NODE')&& (i!='DOCUMENT_TYPE_NODE')&& (i!='DOCUMENT_FRAGMENT_NODE')&& (i!='NOTATION_NODE')&& (i!='ENTITY_NODE')&& (i!='PROCESSING_INSTRUCTION_NODE')
				&& (i!='DOCUMENT_POSITION_PRECEDING') && (i!='DOCUMENT_POSITION_FOLLOWING')&& (i!='DOCUMENT_POSITION_CONTAINS')&& (i!='DOCUMENT_POSITION_CONTAINED_BY')&& (i!='DOCUMENT_POSITION_IMPLEMENTATION_SPECIFIC')
				&& (i!='DOCUMENT_POSITION_DISCONNECTED') && (i!='childElementCount') && (i!='LAYOUTDIRECTION') && (i!='CURRENTSTIMULUS')&& (i!='innerText') && (i!='outerText')
				&& (i!='outerHTML') && (i!='text'))
				{
					pload[i] = e.target[i];
				}
			}
		}
	}
	catch(e){}

	if (typeof(e.target.nodeName) != 'undefined')
	{
		switch(e.target.nodeName.toLowerCase())
		{
			case 'select':
			{
				pload['value'] = $(e.target).val();
				if (typeof(pload['value']) == 'array')
				{
					pload['value'] = pload['value'].join('|');
				}
				break;
			}
			case 'textarea':
			{
				pload['text'] = $(e.target).text();
				break;
			}
		}
	}
}

/**
* @function
* @description set wanted information from the event to the pLoad
* @param {event} e dom event triggered
* @param {Object} pload callback function called when 'ok' clicked
*/
function retreive_events_parameters(e,pload)
{
	for (var i in ATTRIBUTES_TO_CATCH[e.type])
	{
		if (typeof(e[ATTRIBUTES_TO_CATCH[e.type][i]]) != 'undefined')
		{
			pload[ATTRIBUTES_TO_CATCH[e.type][i]] = e[ATTRIBUTES_TO_CATCH[e.type][i]];
		}
		else
		{
			if (typeof(e.target[ATTRIBUTES_TO_CATCH[e.type][i]]) != 'undefined')
			{
				pload[ATTRIBUTES_TO_CATCH[e.type][i]] = e.target[ATTRIBUTES_TO_CATCH[e.type][i]];
			}
		}
	}
}


/*
return true to order send to server as soon as catched
*/
/**
* @function
* @description return true if the event passed is a business event
* @param {event} e dom event triggered
* @type bool
*/
function hooks(e)
{
	if (e.name == 'BUSINESS')
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
* @function
* @description controler that send events to feedtrace
* @param {event} e dom event triggered
*/
function eventStation(e)
{
	var keyCode = e.keyCode ? e.keyCode : e.charCode;
	if ((keyCode == 116) | (keyCode == 115))// kill the f5 and f4
	{
		e.preventDefault();
		return false;
	}

	var target_tag = e.target.nodeName ? e.target.nodeName.toLowerCase():e.target.type;
	var idElement;

	if ((e.target.id) && (e.target.id.length > 0))
	{
		idElement = e.target.id;
	}
	else
	{
		idElement = 'noID';
	}
	var pload = {'id' : idElement};

	if ((typeof(ATTRIBUTES_TO_CATCH)!= 'undefined') && (typeof(ATTRIBUTES_TO_CATCH[e.type])!= 'undefined') && (ATTRIBUTES_TO_CATCH[e.type].length > 0))
	{
		retreive_events_parameters(e,pload);
	}
	else
	{
		if (typeof(describe_event) != 'undefined')
		{
			describe_event(e,pload);
		}
		if (typeof(describe_element) != 'undefined')
		{
			describe_element(e,pload);
		}
	}
	if (typeof(add_item_info) != 'undefined')
	{
		add_item_info(pload);
	}

	var time = 0;
	if (typeof(TIMER) != 'undefined')
	{
		time = Math.floor(TIMER.global.elapsed);
	}
	else
	{
		time = e.timeStamp;
	}
	feedTrace(target_tag, e.type, time, pload);
}


/**
* @function
* @description in the API to allow the unit creator to send events himself to the event log record events of interaction between interviewee and the test
* @param {String} target_tag element type receiving the event.
* @param {String} event_type type of event being catched
* @param {Object} pLoad object containing various information about the event. you may put whatever you need in it.
*/
function feedTrace(target_tag,event_type,time, pLoad)
{
	var send_right_now = false;
	var event = '{"name":"'+target_tag+'","type":"'+event_type+'","time":"'+time+'"';

	if (typeof(hooks) != "undefined")
	{
		send_right_now = hooks(event);
	}
	if (typeof(pLoad)=='string')
	{
		event = event+',"pLoad":"'+pLoad+'"';
	}
	else
	{
		for (var prop_name in pLoad)
		{
			event = event+',"'+prop_name+'":"'+pLoad[prop_name]+'"';
		}
	}
	event = event+'}';
	eventPool.push(event);
	if ((eventPool.length > POOL_SIZE) || (send_right_now))
	{
		$(window).trigger('prepareFeedTraceEvent');
	}
}


/**
* @function
* @description prepare one block of stored traces for being sent
*/
function prepareFeedTrace()
{
	var currentLength = eventsToBeSend.length;

	eventsToBeSend[ currentLength ] = Array();
	var k = 0;
	for ( var i = (currentLength*POOL_SIZE) ; i < (currentLength*POOL_SIZE + POOL_SIZE - 1) ; i++ )
	{
		eventsToBeSend[ currentLength ][k] = eventPool.pop();
		k++;
	}
	eventsToBeSendCursor++;
	sendFeedTrace();
}


/*
does send the content of eventsToBeSend[0] to the server
*/
/**
* @function
* @description send one block of traces (non blocking)
*/
function sendFeedTrace()
{
	var events = '['+eventsToBeSend[ eventsToBeSendCursor ][0];
	var imax = eventsToBeSend[eventsToBeSendCursor].length;
	for ( var i = 1 ; i < imax ; i++)
	{
		events = events+','+eventsToBeSend[ eventsToBeSendCursor ][i];
	}
	events = events + ']';
	var sent_timeStamp = (new Date()).getTime();
	$.ajax(
	{
		type:"POST",
		url:"./server/feedtrace.php",
		data:"PROCESSURI="+PROCESSURI+"&ACTIVITYID="+ACTIVITYID+"&UNITSMAP="+UNITSMAP+"&LANGID="+LANGID+"&DURATION="+DURATION+"&events="+events,
		datatype:"text",
		success: function(data, textStatus){ sendFeedTraceSucceed(data, textStatus, sent_timeStamp); },
		error:sendFeedTraceFail,
		async:true
	});
}

/**
* @function
* @description success callback after traces sent. does affinate the size of traces package sent
* @param (String) data response from server
* @param (String) textStatus status of request
* @param (int) sent_timeStamp time the request was sent
*/
function sendFeedTraceSucceed(data, textStatus, sent_timeStamp)//callback for sendfeedtrace
{
	eventsToBeSendCursor--;

	eventsToBeSend.shift();// data send, we can delete

	// adaptation of the send frequence
	var request_time = (new Date()).getTime() - sent_timeStamp;
	if (request_time > time_limit_for_ajax_request)
	{
		// it takes too long
		increase_events_pool_size();
	}
	else
	{
		// we can increase the frequency of events storing
		reduces_events_pool_size();
	}
	//is there a response about dynax or something else ?
	if (data.length > 0)
	{
		//TODO : error handling php side
	}
}

/**
* @function
* @description the request took too much time, we increase the size of traces package, to have less frequent requests
*/
function increase_events_pool_size()
{
	if ( POOL_SIZE < MAX_POOL_SIZE)
	{
		POOL_SIZE = Math.floor(POOL_SIZE * 2);
	}
}

/**
* @function
* @description the request was fast enough, we increase the frequency of requests by reducing the size of traces package
*/
function reduces_events_pool_size()
{
	if ( POOL_SIZE > MIN_POOL_SIZE )
	{
		POOL_SIZE = Math.floor(POOL_SIZE * 0.75);
	}
}

/**
* @function
* @description callback function after request failed (TODO)
* @param (ressource) xhr ajax request ressource
* @param (String) errorString error message
* @param (exception) [exception] exception object thrown
*/
function sendFeedTraceFail(xhr, errorString, exception)//callback for sendfeedtrace
{
	// TODO : error handling
}

/**
* @function
* @description callback function after request failed (TODO)
* @param (ressource) xhr ajax request ressource
* @param (String) errorString error message
* @param (exception) [exception] exception object thrown
*/
function sendAllFeedTraceFail()//callback for sendAllfeedtrace
{
	// TODO : error handling
//	alert("sendAllFeedTraceFail");
}


/**
* @function
* @description prepare all stored traces for being sent before the page is destroyed
* @param (function) continueNext callback called after traces are sent (should be the next() method)
* @param (mixed) param parameters for the callback function
*/
function prepareAllFeedTrace(continueNext,param)
{
	var currentLength = eventsToBeSend.length;

	eventsToBeSend[ currentLength ] = Array();
	for (  ; eventPool.length > 0 ;  )//  empty the whole eventPool array
	{
		eventsToBeSend[ currentLength ].push( eventPool.pop() );
	}
	eventsToBeSendCursor = currentLength;
	sendAllFeedTrace(continueNext,param);
}


/*
function sendAllFeedTrace
does send the content of eventsToBeSend[0][...] to the server
*/
/**
* @function
* @description send all traces (non blocking)
* @param (function) customCallback callback called after traces are sent
* @param (mixed) param parameters for the callback function
*/
function sendAllFeedTrace(customCallback,param)
{
	var events = '['+eventsToBeSend[ eventsToBeSendCursor ][0];

	var imax = eventsToBeSend[eventsToBeSendCursor].length;
	for ( var i=1; i < imax; i++)
	{
		events = events+','+eventsToBeSend[ eventsToBeSendCursor ][i];
	}
	events = events+']';
	$.ajax(
	{
		type:"POST",
		url:"./server/feedtrace.php",
		data:"PROCESSURI="+PROCESSURI+"&ACTIVITYID="+ACTIVITYID+"&UNITSMAP="+UNITSMAP+"&LANGID="+LANGID+"&DURATION="+DURATION+"&events="+events,
		datatype:"text",
		success : function(){ customCallback(param); },
		error : sendAllFeedTraceFail
	});
}


/* no callback on success
used when business events catched*/
/**
* @function
* @description send all traces with a blocking function
*/
function sendAllFeedTrace_now()
{
	var currentLength = eventsToBeSend.length;

	eventsToBeSend[ currentLength ] = Array();
	for (  ; eventPool.length > 0 ;  )//  empty the whole eventPool array
	{
		eventsToBeSend[ currentLength ].push( eventPool.pop() );
	}
	eventsToBeSendCursor = currentLength;

	var events = '['+eventsToBeSend[ eventsToBeSendCursor ][0];

	var imax = eventsToBeSend[eventsToBeSendCursor].length;
	for ( var i = 1; i < imax; i++)
	{
		events = events+','+eventsToBeSend[eventsToBeSendCursor][i];
	}

	events = events+']';
	$.ajax(
	{
		type:"POST",
		url:"./server/feedtrace.php",
		data:"PROCESSURI="+PROCESSURI+"&ACTIVITYID="+ACTIVITYID+"&UNITSMAP="+UNITSMAP+"&LANGID="+LANGID+"&DURATION="+DURATION+"&events="+events,
		datatype:"text",
		error : sendAllFeedTraceFail,
		async:false
	});
}


/* custom events definition */

/* changeCss
*/
jQuery.event.special.changeCss = {setup:function(){},teardown:function(){}};
/* reloadMapEvent
order to reload the map */
jQuery.event.special.reloadMapEvent = {setup: function(){},teardown: function(){}};



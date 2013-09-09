function ItemServiceImpl(serviceApi) {

	// temporary fix
	if (typeof itemId !== "undefined") {
		this.itemId = itemId;
	}
	
	this.serviceApi = serviceApi;
	this.responses = {};
	this.scores = {};
	this.events = {};
	
	var rawstate = serviceApi.getState();
	var state = (typeof rawstate == 'undefined' || rawstate == null) ? {} : $.parseJSON(rawstate);
	this.stateVariables = typeof state == 'object' ? state : {};
	
	this.beforeFinishCallbacks = new Array();
}

ItemServiceImpl.prototype.connect = function(frame){
	frame.contentWindow.itemApi = this;
	if (typeof(frame.contentWindow.onItemApiReady) == "function") {
		frame.contentWindow.onItemApiReady(this);
	}
	console.log('ItemServiceImpl connected');
};

// Response 

ItemServiceImpl.prototype.saveResponses = function(valueArray){
	for (var attrname in valueArray) {
		this.responses[attrname] = valueArray[attrname];
	}
};

ItemServiceImpl.prototype.traceEvents = function(eventArray) {
	for (var attrname in eventArray) {
		this.events[attrname] = eventArray[attrname];
	}

	console.log('Got scores: '.eventArray);
};

// Scoring
ItemServiceImpl.prototype.saveScores = function(valueArray) {
	for (var attrname in valueArray) {
		this.scores[attrname] = valueArray[attrname];
	}
	console.log('Got scores: '.valueArray);
};

// Flow
ItemServiceImpl.prototype.beforeFinish = function(callback) {
	console.log('beforeFinish received by ItemServiceImpl');
	this.beforeFinishCallbacks.push(callback);
};

ItemServiceImpl.prototype.finish = function() {
	console.log('Running ' + this.beforeFinishCallbacks.length + ' registered events');
	for (var i = 0; i < this.beforeFinishCallbacks.length; i++) {
		this.beforeFinishCallbacks[i]();
	};

	this.serviceApi.setState(JSON.stringify(this.stateVariables), function(itemApi) {
		
		return function() {
			console.log('Responses ', itemApi.responses);	
			console.log('Scores ', itemApi.scores);	
			console.log('Events ', itemApi.events);
			//todo add item, call id etc
			
			$.ajax({
				url  		: resultsStorageEndPoint,
				data 		: {
					itemId: itemApi.itemId,
					serviceCallId: itemApi.serviceApi.getServiceCallId(),
					responseVariables: itemApi.responses,
					outcomeVariables: itemApi.scores,
					traceVariables:itemApi.events
				},
				type 		: 'post',
				dataType	: 'json',
				success		: function(reply) {
					itemApi.serviceApi.finish();
				}
			});

			
		};
	}(this));		
};

ItemServiceImpl.prototype.getVariable = function(identifier, callback) {
	if (typeof callback == 'function') {
		callback((typeof this.stateVariables[identifier] == 'undefined')
			? null
			: this.stateVariables[identifier]
		);
	}
};

ItemServiceImpl.prototype.setVariable = function(identifier, value) {
	this.stateVariables[identifier] = value;
};

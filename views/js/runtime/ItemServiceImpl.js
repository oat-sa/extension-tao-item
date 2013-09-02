function ItemServiceImpl(serviceApi, variableStorage) {
	
	// temnporary fix
	this.itemId = 'undefined';
	if (typeof itemId !== "undefined") {
		this.itemId = itemId;
	}
	
	this.serviceApi = serviceApi;
	this.variableStorage = variableStorage;
	this.responses = {};
	this.scores = {};
	this.events = {};
	
	this.beforeFinishCallbacks = new Array();
}

ItemServiceImpl.prototype.connect = function(frame){
	frame.contentWindow.itemApi = this;
	if (typeof(frame.contentWindow.onItemApiReady) == "function") {
		frame.contentWindow.onItemApiReady(this);
	}
	console.log('ItemServiceImpl connected');
}

// Response 

ItemServiceImpl.prototype.saveResponses = function(valueArray){
	for (var attrname in valueArray) {
		this.responses[attrname] = valueArray[attrname];
	}
}

ItemServiceImpl.prototype.traceEvents = function(eventArray) {
	for (var attrname in eventArray) {
		this.events[attrname] = eventArray[attrname];
	}

	console.log('Got scores: '.eventArray);
}

// Scoring
ItemServiceImpl.prototype.saveScores = function(valueArray) {
	for (var attrname in valueArray) {
		this.scores[attrname] = valueArray[attrname];
	}
	console.log('Got scores: '.valueArray);
}

// Flow
ItemServiceImpl.prototype.beforeFinish = function(callback) {
	console.log('beforeFinish received by ItemServiceImpl');
	this.beforeFinishCallbacks.push(callback);
}

ItemServiceImpl.prototype.finish = function() {
	console.log('Running ' + this.beforeFinishCallbacks.length + ' registered events');
	for (var i = 0; i < this.beforeFinishCallbacks.length; i++) {
		this.beforeFinishCallbacks[i]();
	};
	var callIdValue = this.serviceApi.getServiceCallId();
	var itemIdValue = this.itemId;

	this.variableStorage.submit(function(itemApi) {
		
		return function() {
			console.log('Responses ', itemApi.responses);	
			console.log('Scores ', itemApi.scores);	
			console.log('Events ', itemApi.events);
			//todo add item, call id etc
			
			$.ajax({
				url  		: resultsStorageEndPoint,
				data 		: {
					itemId:itemIdValue,
					serviceCallId: callIdValue,
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

			
		}
	}(this));		
};

ItemServiceImpl.prototype.getVariable = function(identifier, callback) {
	if (typeof callback == 'function') {
		return this.variableStorage.get(identifier, callback);
	}
}

ItemServiceImpl.prototype.setVariable = function(identifier, value) {
	return this.variableStorage.put(identifier, value);
}
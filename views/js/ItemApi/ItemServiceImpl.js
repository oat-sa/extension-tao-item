function ItemServiceImpl(serviceApi) {
	this.serviceApi = serviceApi;
	
	this.responses = {};
	this.scores = {};
	this.events = {};
	
	this.beforeFinishCallbacks = new Array();
}

ItemServiceImpl.prototype.connect = function(frame){
	frame.contentWindow.itemApi = this;
	if (typeof(frame.contentWindow.onItemApiReady) == "function") {
		frame.contentWindow.onItemApiReady();
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

ItemServiceImpl.prototype.beforeFinish = function(callback) {
	console.log('beforeFinish received by ItemServiceImpl');
	this.beforeFinishCallbacks.push(callback);
}

// Scoring
ItemServiceImpl.prototype.saveScores = function(valueArray) {
	for (var attrname in valueArray) {
		this.scores[attrname] = valueArray[attrname];
	}
	console.log('Got scores: '.valueArray);
}

// Flow
ItemServiceImpl.prototype.finish = function() {
	console.log('Running ' + this.beforeFinishCallbacks.length + ' registered events');
	for (var i = 0; i < this.beforeFinishCallbacks.length; i++) {
		this.beforeFinishCallbacks[i]();
	};
	console.log('Finished with responses, scores, events: ');
	console.log(this.responses);	
	console.log(this.scores);	
	console.log(this.events);
	this.serviceApi.finish();
};
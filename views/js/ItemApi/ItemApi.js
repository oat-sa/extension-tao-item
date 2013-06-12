function ItemApi() {
	this.implementation = null;
	this.pendingCalls = new Array();
}

ItemApi.prototype.setImplementation = function(implementation) {
	this.implementation = implementation;
	for (var i = 0; i < this.pendingCalls.length; i++) {
		this.pendingCalls[i](implementation);
	};
	this.pendingCalls = new Array();
};

ItemApi.prototype.__delegate = function(call) {
	if (this.implementation != null) {
		return call(this.implementation);
	} else {
		this.pendingCalls.push(function(implementation) {
			return call(implementation);
		});
	}
};

// interface to implement

ItemApi.prototype.saveResponses = function(valueArray) {
	this.__delegate((function(valueArray) {return function(implementation) {implementation.saveResponses(valueArray);}})(valueArray));
};

// Scoring
ItemApi.prototype.saveScores = function(valueArray) {
	this.__delegate((function(valueArray) {return function(implementation) {implementation.saveScores(valueArray);}})(valueArray));
};

ItemApi.prototype.traceEvents = function(eventArray) {
	this.__delegate((function(eventArray) {return function(implementation) {implementation.traceEvents(eventArray);}})(eventArray));
};

// Flow
ItemApi.prototype.beforeFinish = function(callback) {
	this.__delegate((function(callback) {return function(implementation) {implementation.beforeFinish(callback);}})(callback));
};

ItemApi.prototype.finish = function() {
	this.__delegate(function(implementation) {implementation.finish();});
};

// runtime variables, will not be submited to result service
ItemApi.prototype.setVariable = function(identifier, value) {
	this.__delegate((function(identifier, value) {return function(implementation) {implementation.setVariable(identifier, value);}})(identifier, value));
};

ItemApi.prototype.getVariable = function(identifier, callback) {
	this.__delegate((function(identifier, callback) {return function(implementation) {implementation.getVariable(identifier, callback);}})(identifier, callback));
};
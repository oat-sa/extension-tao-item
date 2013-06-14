function ItemPreviewImpl() {

	this.responses = {};
	this.scores = {};
	this.events = {};
	
	this.beforeFinishCallbacks = new Array();
}

ItemPreviewImpl.prototype.connect = function(frame){
	frame.contentWindow.itemApi = this;
	if (typeof(frame.contentWindow.onItemApiReady) == "function") {
		frame.contentWindow.onItemApiReady();
	}
	console.log('ItemPreviewImpl connected');
}

// Response 

ItemPreviewImpl.prototype.saveResponses = function(valueArray){
	for (var attrname in valueArray) {
		this.responses[attrname] = valueArray[attrname];
	}
}

ItemPreviewImpl.prototype.traceEvents = function(eventArray) {
	for (var attrname in eventArray) {
		this.events[attrname] = eventArray[attrname];
	}

	console.log('Got events: '.eventArray);
}

ItemPreviewImpl.prototype.beforeFinish = function(callback) {
	this.beforeFinishCallbacks.push(callback);
}

// Scoring
ItemPreviewImpl.prototype.saveScores = function(valueArray) {
	for (var attrname in valueArray) {
		this.scores[attrname] = valueArray[attrname];
	}
	console.log(valueArray);
}

// Variables
ItemPreviewImpl.prototype.setVariable = function(identifier, value) {
	// do nothing in preview
};

ItemPreviewImpl.prototype.getVariable = function(identifier, callback) {
	// always return null in preview
	callback(null);
};

// Flow
ItemPreviewImpl.prototype.finish = function() {

	for (var i = 0; i < this.beforeFinishCallbacks.length; i++) {
		this.beforeFinishCallbacks[i]();
	};
	
	// submit Results
	this.log('state', 'item is now finished!');
	var strOutcomes = '';
	for (var outcomeKey in this.scores){
		strOutcomes += '[ ' + outcomeKey+ ' = ' + this.scores[outcomeKey] + ' ]';
	}
	window.top.helpers.createInfoMessage('THE OUTCOME VALUES : <br/>'  + strOutcomes);
	this.log('responses', this.responses);
	this.log('outcomes', this.scores);
};

ItemPreviewImpl.prototype.log = function(title, message) {
	if (typeof(message) == 'object') {
		string = '';
		for (var attrname in message) {
			string += ', ' + attrname+ '=' + message[attrname];
		}
		message = '{' + string.substring(2) + '}';
	}
	previewConsole = $('#preview-console');
	if (previewConsole.length > 0){
		//In the preview console
		previewConsole.trigger('updateConsole', [title, message]);
	} else {
		//outside preview container
		util.log(title + ': ' + message);
	}
}
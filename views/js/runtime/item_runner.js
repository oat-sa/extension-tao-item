// we need both to initialise the api
var itemApi = null;
var frame = null;

// wait for API and frame to be ready
var bindApi = function() {
	if (frame != null && itemApi != null) {
		console.log('Connecting');
		itemApi.connect(frame);
	}
};

function onServiceApiReady(serviceApi) {
	itemApi = new ItemServiceImpl(serviceApi);
	console.log('Api ready');
	bindApi();
}

function autoResize(frame, frequence) {
	$frame = $(frame);
	setInterval(function() {
		$frame.height($frame.contents().height());
	}, frequence);
}

$(document).ready(function() {
	frame = document.getElementById('item-container');
	if (jQuery.browser.msie) {
		frame.onreadystatechange = function(){	
			if(this.readyState == 'complete'){
				console.log('Frame ready');
				bindApi();
				autoResize(frame, 10);
			}
		};
	} else {		
		frame.onload = function(){
			console.log('Frame ready');
			bindApi();
			autoResize(frame, 10);
		};
	}
});
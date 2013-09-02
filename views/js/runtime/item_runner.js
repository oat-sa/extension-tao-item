// we need both to initialise the api
var itemApi = null;
var frame = null;
//todo use taoDelivery as a proxy
var resultsStorageEndPoint = '/taoResultServer/ResultServerStateFull/storeItemVariableSet';

// wait for API and frame to be ready
var bindApi = function() {
	if (frame != null && itemApi != null) {
		console.log('Connecting');
		itemApi.connect(frame);
	}
}

function onServiceApiReady(serviceApi) {
	//var facade = util.generateFacade(ItemVariableStorage);
	var storage = new ItemVariableStorage(serviceApi.getServiceCallId());
	itemApi = new ItemServiceImpl(serviceApi, storage);
	console.log('Api ready');
	bindApi();
};

$(document).ready(function() {
	frame = document.getElementById('item-container');
	if (jQuery.browser.msie) {
		frame.onreadystatechange = function(){	
			if(this.readyState == 'complete'){
				console.log('Frame ready');
				bindApi();
			}
		}
	} else {		
		frame.onload = function(){
			console.log('Frame ready');
			bindApi();
		}
	}
});
qtiEdit = new Object();

qtiEdit.getEltInFrame = function(selector){
	var foundElts = [];
	//for each iframe:
	$('iframe').each(function(){
	
		//get its document
		$(this).each( function(){
			var selectedDocument = this.contentWindow.document;
			$(selector, selectedDocument).each(function(){
				foundElts.push($(this));  
			});
		});
	});
	return foundElts;
}

qtiEdit.getUniqueEltInFrame = function(selector){
	var foundElts = qtiEdit.getEltInFrame(selector);
	if(foundElts.length != 1){
		throw 'incorrect number of found with the selector '+selector+' ('+foundElts.length+')';
	}
	return foundElts[0];
}
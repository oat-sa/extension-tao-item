// alert('util loaded');

util = new Object();
util.debug = true;


CL = function(arg1, arg2){
	
	// if($.browser != 'msie' && util.debug){
		if(arg1){
			if(arg2){
				console.log(arg1, arg2);
			}else{
				console.log(arg1);
			}
		}
	// }
}

CD = function(object, desc){
	// if($.browser != 'msie' && util.debug){
		if(desc){
			console.log(desc+':');
		}
		console.dir(object);
	// }
}

util.htmlDecode = function(encodedStr){
	var decoded = encodeURIComponent(encodedStr);
	// var decoded = $("<div/>").html(encodedStr).text();
	return decoded;
}

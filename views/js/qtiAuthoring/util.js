// console.log('util loaded');

util = new Object();

CL = function(arg1, arg2){
	if(arg1){
		if(arg2){
			console.log(arg1, arg2);
		}else{
			console.log(arg1);
		}
	}
}
CD = function(object, desc){
	if(desc){
		console.log(desc+':');
	}
	console.dir(object);
}

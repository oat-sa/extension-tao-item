// alert('util loaded');

util = new Object();

util.log = function(arg1, arg2){
	if(console && typeof(console)!='undefined'){
		if(console.log){
			if(arg1){
				if(arg2){
					console.log(arg1, arg2);
				}else{
					console.log(arg1);
				}
			}
		}
	}
}

util.dir = function(object, desc){
	if(console && typeof(console)!='undefined'){
		if(console.log && console.dir){
			if(desc){
				console.log(desc+':');
			}
			console.dir(object);
		}
	}
}

util.tab = '    ';
util.deep = 2;
util.printFunction = false;
util.childMax = 50;

util.dump = function(obj, keyValue, layer){
	
	if(!layer) layer = 1;
	
	var tab = function(){
		var tabstring = '';
		for(var i =0; i<layer; i++){
			tabstring += util.tab;
		}
		return tabstring;
	}
	
	var val = function(value){
		var returnValue = '';
		
		if(!value){
			if(value == null){
				returnValue = '(null) null';
			}else{
				switch(typeof(value)){
					case 'undefined':{
						returnValue = '(undefined) null';
						break;
					}
					case 'number':{	
						returnValue = '(number) 0';
						break;
					}
					case 'boolean':{
						returnValue = '(boolean) false';
						break;
					}
					case 'string':{
						returnValue = '(string) ""';
						break;
					}
					default:{
						returnValue = '(array or object) empty';
					}
				}
			}	
		}else{
			returnValue = '('+typeof(value)+') '+value;
		}
		
		return returnValue;
	}
	
	if(typeof(keyValue)!='undefined'){
		util.log(tab()+keyValue+':');
		layer++;
		if(layer>util.deep){
			util.log(tab()+'limit of deep '+util.deep+' reached');
			return false;
		}
	}else{
		util.log('___________________________ obj dump ___________________________');
	}
		
	if(typeof(obj)=='object'){
		
		var childCount = 0;		
		for(var key in obj){
		
			var value = obj[key];
			if(typeof(value)=='object'){
				util.dump(value, key, layer);
				childCount ++;
			}else{
				if(typeof(value)!='function'){
					util.log(tab()+key+': ', val(value));
					childCount ++;
				}else{	
					if(util.printFunction){
						util.log(tab()+key+': ', 'function(...)');
						childCount ++;
					}
				}					
			}
			
			if(childCount > util.childMax){
				util.log(tab()+'limit of '+util.childMax+' children reached');
				return false;
			}
		}
		if(!childCount){
			util.log(tab()+'nullObj('+typeof(obj)+'): ', val(obj));
		}
		 
	}else{
		if(typeof(obj)!='function'){
			util.log(tab()+'noObj('+typeof(obj)+'): ', val(obj));
		}else{
			if(util.printFunction){
				util.log(tab()+'noObj('+typeof(obj)+'): ', 'function(...)');
			}
		}
			
	}
	
	
	//return JSON.stringify(obj);
}

CL = util.log;
CD = util.dir;
_dump = util.dump;

util.htmlEncode = function(encodedStr){
	
	var returnValue = '';
	
	if(encodedStr){
		//<br...> are replaced by <br... />
		returnValue = encodedStr;
		returnValue = returnValue.replace(/<br([^>]*)?>/ig, '<br />');
		returnValue = returnValue.replace(/<hr([^>]*)?>/ig, '<hr />');
		 
		//<img...> are replaced by <img... />
		returnValue = returnValue.replace(/(<img([^>]*)?\s?[^\/]>)+/ig,
			function($0, $1){
				return $0.replace('>', ' />');
			});
		
		// returnValue = encodeURIComponent(encodedStr);
	}
	
	
	return returnValue;
}


util.getMediaResource = function(backgroundImagePath){
	
	if(backgroundImagePath && backgroundImagePath.substring(0,4) != 'http'){
		backgroundImagePath = root_url + '/taoItems/Items/getMediaResource?path='+encodeURIComponent(backgroundImagePath);
	}
	
	return backgroundImagePath;
}

//custom object serialization method to jQuery:
$.fn.serializeObject = function(print)
{
    var o = {};
    var a = this.serializeArray();
	var out = '';
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
				if(print) out += this.name + ':' + o[this.name] +', ';
            }
            o[this.name].push(this.value || '');
			if(print) out += this.name + ':' + this.value +', ';
        } else {
            o[this.name] = this.value || '';
			if(print) out += this.name + ':' + this.value +', ';
        }
    });
	
	if(print){
		if(console){
			CL(out)
		}else{
			alert(out);
		}
	}
    return o;
};

util.indexOf = function(arrayObj, needle, start){
	for (var i = (start || 0), j = arrayObj.length; i < j; i++) {
			 if (arrayObj[i] === needle) { return i; }
		 }
		 return -1;

}

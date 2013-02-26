define(['require', 'jquery'], function(req, $){
	
	var validatorClassFunctions = {
		init:function(type, options){
			this.type = type;
			var defaultOptions = this.getDefaultOptions();
			this.options = $.extend({}, defaultOptions, options);
		},
		getMessage:function(){
			return this.message;
		},
		getType:function(){
			return this.type;
		}
	}
	
	return Class.extend(validatorClassFunctions);
});



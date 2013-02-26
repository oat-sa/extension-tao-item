define(['require', 'jquery', root_url  + 'taoItems/views/js/qtiAuthoring/validators/class.Validator.js'], function(req, $, ValidatorClass){
	
	var validatorClassFunctions = {
		init:function(options){
			this._super('notempty', options);
			this.message = (typeof this.options.message == 'string')?this.options.message:defaultOptions.message;
		},
		getDefaultOptions:function(){
			return {
				message:__('This field is required')
			};
		},
		evaluate:function(value){
			var returnValue = false;
			
			if (typeof value == 'string'){
				value = $.trim(value);
				if(value.length >= 1){
					returnValue = true;
				}
			}
			
			return returnValue;
		}
		
	}
	
	return ValidatorClass.extend(validatorClassFunctions);
});
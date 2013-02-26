define(['require', 'jquery'], function(req, $){
	
	var validatorClassFunctions = {
		init:function(options){
			var defaultOptions = {
				min:0,
				max:null,
				allowEmpty:false
			}
			this.options = $.extend({}, defaultOptions, options);
			
			if(this.options.min != null && this.options.max != null){
				this.message = __('Invalid field length')+" (minimum "+this.options.min+", maximum "+this.options.max+")";
			}else if(this.options.min != null && this.options.max == null){
				this.message = __('This field is too short')+" (minimum "+this.options.min+")";
			}else if(this.options.min == null && this.options.max != null){
				this.message = __('This field is too long')+" (maximum "+this.options.max+")";
			}else{
				throw 'validator definition : invalid options';
				CL('invalid options', this.options);
			}
		},
		evaluate:function(value){
			var returnValue = true;
			if(this.options.min != null && value.length<this.options.min){
				if(this.options.allowEmpty && value.length == 0){
					//ok
				}else{
					returnValue = false;
				}
			}
			if(this.options.max && value.length>this.options.max){
				returnValue = false;
			}
			return returnValue;
		}
		
	}
	
	return Class.extend(validatorClassFunctions);
});



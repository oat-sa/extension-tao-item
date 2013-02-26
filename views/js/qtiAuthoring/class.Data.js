define(['require', 'jquery'], function(req, $){
	
	var QTIdataClassFunctions = {
		init : function(type, serial, options){
			this.type = type;
			this.serial = serial;
			this.options = options;
			this.attributes = [];
			this.attributesCallbacks = {};
			
			var callbacks = {
				title : {
					validators:[
						{type:'notempty'},
						{type:'length', options:{max:150}}
					],
					afterValidation:function(validatorType, success, message){
						if(success){
							CL('valid!', validatorType);
						}else{
							CL('failed man', message);
						}
					},
					onChange:function(newValue, oldValue, qti){
						CL('title value changed');
						CL('newValue', newValue);
						CL('newValue', newValue);
					}
				}
			};
			
			try{
				this.initAttributesCallbacks(callbacks);
			}catch(e){
				console.warn('initAttributesCallbacks', e);
			}
		},
		
		initAttributesCallbacks : function(callbacks){
			
			for(var attributeKey in callbacks){
				
				for(var callbackName in callbacks[attributeKey]){
					
					switch(callbackName){
						case 'validators':{
							var validators = callbacks[attributeKey].validators;
							for(var i in validators){
								if(validators[i].type){
									var options = (validators[i].options)?validators[i].options:{};
									this.addAttributeValidator(attributeKey, validators[i].type, options);
								}
							}
							break;
						}
						//other authorized callback:
						case 'onChange':
						case 'beforeSave':
						case 'afterSave':
						case 'afterValidation':{
							this.addAttributeCallback(attributeKey, callbackName, callbacks[attributeKey][callbackName]);
							break;
						}
					}
					
				}
				
			}
		},
		
		addAttributeCallback : function(attribute, callbackName, callbackFunction){
			if(typeof this.attributesCallbacks[attribute] == 'undefined'){
				this.attributesCallbacks[attribute] = {};
			}
			this.attributesCallbacks[callbackName] = callbackFunction;
		},
		
		addAttributeValidator : function(attribute, type, options){
			var _this = this;
			type += '';// convert to string
			type = type.toLowerCase();
			if($.inArray(type, ['dummy','length', 'url', 'notempty', 'integer'])){
				var className = type.charAt(0).toUpperCase() + type.substr(1);
				require([root_url  + 'taoItems/views/js/qtiAuthoring/validators/class.' + className + '.js'], function(validatorClass){
					if(typeof _this.attributesCallbacks[attribute] == 'undefined'){
						_this.attributesCallbacks[attribute] = {};
					}
					if(typeof _this.attributesCallbacks[attribute].validators == 'undefined'){
						_this.attributesCallbacks[attribute].validators = [];
					}
					
					var validator =  new validatorClass(options);
					_this.attributesCallbacks[attribute].validators.push(validator);
				});
			}else{
				throw new QTIauthoringException('validator', 'invalid validator type : '+type);
			}
		},
		
		getAttributeValidators : function(){
			
		},
		
		getAttributeCallback : function(){
			
		},
		
		validateAttributeValue : function(attributeKey, value){
			var returnValue = true;
			if(this.attributesCallbacks[attributeKey] && typeof this.attributesCallbacks[attributeKey].validators == 'array'){
				
				var afterValidationCallback = this.getCallback(attributeKey, 'afterValidation');
				
				for(var i in this.attributesCallbacks[attributeKey].validators){
					var validator = this.attributesCallbacks[attributeKey].validators[i];
					var success = validator.validate(value);
					
					if(afterValidationCallback){
						afterValidationCallback(validator.getType(), success, (success)?'':validator.getMessage());
					}
						
					if(!success){
						returnValue = false;
						break;
					}
				}
			}
			return returnValue;
		},
		getCallback:function(attributeKey, callbackName){
			var returnValue = null;
			if(this.attributes[attributeKey]){
				if(typeof this.attributes[attributeKey][callbackName] == 'function'){
					returnValue = this.attributes[attributeKey][callbackName];
				}
			}
			return returnValue;
		},
		saveAttribute : function(attributeKey, value){
			
			var _this = this;
			//validate val: 
			var oldValue = (this.attributes[attributeKey] == null)?null:this.attributes[attributeKey];
			if(this.validateAttributeValue(attributeKey, value)){
			
				//save to local datamodel:
				this.attributes[attributeKey] = value;
				
				//before save callback
				var beforeSaveCallback = _this.getCallback(attributeKey, 'beforeSave');
				if(beforeSaveCallback){
					beforeSaveCallback(oldValue, value, _this);
				}
				
				//save to server:


				//call modified attribute callback:
				var afterSaveCallback = _this.getCallback(attributeKey, 'afterSave');
				if(afterSaveCallback){
					afterSaveCallback(oldValue, value, _this);
				}
			}
			
		}
	}
	
	var QTIdataClass = Class.extend(QTIdataClassFunctions);

	return QTIdataClass;
	
});



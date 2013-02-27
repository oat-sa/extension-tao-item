define(['require', 'jquery', root_url  + 'taoItems/views/js/qtiAuthoring/class.Data.js'], function(req, $, QTIdataClass){
	
	var QTIitemClassFunctions = {
		init:function(serial, options){
			this._super('item', serial, options);
			this.interactions = [];
		},
		getDefaultCallbacks : function(){
			return {
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
						CL('newValue', oldValue);
						CL('newValue', newValue);
					}
				}
			};
		},
		addInteractionsFromData:function(data){
			/*
			 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do 
			 * eiusmod tempor {{QTIinteraction:choice:new}} ut labore et dolore magna aliqua. Ut enim
			 * ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut 
			 * aliquip ex ea commodo consequat. {{QTIinteraction:graphicAssociate:new}} Duis aute irure dolor in 
			 * reprehenderit in voluptate velit esse cillum dolore eu fugiat 
			 * nulla pariatur. {{QTIinteraction:match:serial_123456}} sint occaecat cupidatat non proident, 
			 * sunt in culpa qui officia deserunt mollit anim id est laborum.
			 */
			
		},
		initInteraction:function(type, serial){
			var _this = this;
			require([root_url  + 'taoItems/views/js/qtiAuthoring/validators/class.Interaction.js'], function(InteractionClass){
				_this.interactions[serial] = new InteractionClass(type, serial);
			});
		}
	}
	
	var QTIitemClass = QTIdataClass.extend(QTIitemClassFunctions);

	return QTIitemClass;
	
});


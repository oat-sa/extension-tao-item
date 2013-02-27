define(['require', 'jquery', root_url  + 'taoItems/views/js/qtiAuthoring/class.Data.js'], function(req, $, QTIdataClass){
	
	var QTIinteractionClassFunctions = {
		init:function(type, serial, options){
			this.getInteractionType = function(){
				return type;
			}
			
			this._super('interaction', serial, options);
			this.choices = [];
		},
		addChoices:function(count){
			//append choices to the end of data, then reload response?
		},
		addChoicesFromData:function(data){
			/*
			 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do 
			 * eiusmod tempor {{choice:newInteraction}} ut labore et dolore magna aliqua. Ut enim
			 * ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut 
			 * aliquip ex ea commodo consequat. {{newInteraction:graphicAssociate}} Duis aute irure dolor in 
			 * reprehenderit in voluptate velit esse cillum dolore eu fugiat 
			 * nulla pariatur. {{choice:serial_123456}} sint occaecat cupidatat non proident, 
			 * sunt in culpa qui officia deserunt mollit anim id est laborum.
			 */
		},
		initChoice:function(type, serial){
			var _this = this;
			require([root_url  + 'taoItems/views/js/qtiAuthoring/validators/class.Choice.js'], function(ChoiceClass){
				_this.choices[serial] = new ChoiceClass(type, serial);
			});
		}
	}
	
	return QTIdataClass.extend(QTIinteractionClassFunctions);
	
});
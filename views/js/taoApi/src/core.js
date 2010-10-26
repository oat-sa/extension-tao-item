/**
 * TAO API core.
 * It provides the tools to set up the environment, 
 * stock the data and push them to the server 
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @requires jquery >= 1.4.0 {@link http://www.jquery.com}
 */

/**
 * The TaoStack class enables you:
 * - to set up the platform to communicate with 
 * (it's by default the TAO plateform but could be any other with the same services provided by the server side) 
 * - to set and get variables created by the user or defined by the platform
 * - to manage the source of data that the item could need
 * - to push the communications with the platform
 *  
 * @namespace taoApi
 * @class TaoStack
 */
function TaoStack(){
	
	/**
	 * This object describes the way the data are accessed 
	 * @var {Object} dataSource
	 */
	this.dataSource = new Object();
	
	//default data source environment
	this.dataSource.environment = {
		'type'		: 'async', 					// (manual|sync|async) 
		'url' 		: '/tao/Api/getContext',	// the url to the server [NOT for manual type] 
		'params'	: {	}						// the key/values to send to the server [NOT for manual type] 
	};
	
	//default data source settings
	this.dataSource.settings = {
		'format'		: 'json',		//only json is supported
		'method' 		: 'post',		//HTTP method (get|post) [NOT for manual type] 
		'load'			: 'onInit' 		// when the source is loaded (ONLY onInit is currently supported]
	};
	
	/**
	 * This object stores the contextual  data (sent by the server on load, or on getting them)   
	 * @var {Object} dataStore
	 */
	this.dataStore = new Object();
	
	/**
	 * Initialize and setup the data source.
	 * 
	 * @param {Object} environment 
	 * @see TaoStack.dataSource.environment
	 * 
	 * @param {Object} settings 
	 * @see TaoStack.dataSource.settings
	 * 
	 * @param {Object} source if manual data source
	 */
	this.initDataSource = function(environment, settings, source){
		if($.inArray(environment.type, ['manual','sync','async'])){
			
			this.dataSource.environment.type = environment.type;
			if(this.dataSource.environment.type != 'manual' && environment.url){
				if(/(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/.test(url)){	//test url format
		
					this.dataSource.environment.url = url;		//set url
					
					if($.isPlainObject(environment.params)){	//set parameters
						for(key in params){
							if(isScalar(environment.params[key])){
								this.dataSource.environment.params[key] = environment.params[key]+''; 
							}
						}
					}
				}
				if($.isPlainObject(settings)){	//set push settings
					if(settings.method){
						if(/^get|post$/i.test(settings.method)){
							this.dataSource.settings.method = settings.method;
						}
					}
				}
			}
			if(this.dataSource.settings.load == 'onInit'){
				this.loadData(source);
			}
		}
	};
	
	/**
	 * Load the contextual data 
	 * @param {Object} [source] the data ONLY for the manual source
	 */
	this.loadData = function(source){
		
		/** 
		 * Assign the 
		 * @param {Object} data to the 
		 * @param {TaoStack} instance 
		 */
		var populateData = function(data, instance){
			if($.isPlainObject(data)){
				for(key in instance.dataStore){
					if(data[key]){
						instance.dataStore[key] = data[key];
					}
				}
				if(instance.dataStore.subject){
					this.setTaoVar(URI.SUBJECT, instance.dataStore.subject);
				}
				if(instance.dataStore.item){
					this.setTaoVar(URI.ITEM, instance.dataStore.item);
				}
			}
		};
		
		if(this.dataSource.environment.type == 'manual' && source){		
			//manual loading
			populateData(source, this);
		}
		else{		
			
			//sync|async loading, use an ajax request 
			var params = this.dataSource.environment.params;
			var instance = this;
			$.ajax({
				'url'  		: this.dataSource.environment.url,
				'data' 		: params,
				'type' 		: this.dataSource.settings.method,
				'async'		: (this.dataSource.environment.type == 'async'),
				'dataType'  : this.dataSource.settings.format,
				'success' 	: function(data){
					populateData(data, instance);
				}
			});
		}
	};
	
	/**
	 * @var {Object} the push data
	 */
	this.push = new Object();
	this.push.environment = {
		'url' 		: '/tao/Api/save',					// the url to the server
		'params'	: {									// the params to send to the server at each communication 
			'token'	: this.dataStore.token				//these parameters comes from the dataStore
		}
	};
	this.push.settings = {
		'format'		: 'json',	//only json is supported
		'method' 		: 'post',	//HTTP method to push the data (get|post)
		'async'			: true,		//if the request is asynchrone 
		'clearAfter'	: true		//if the variables stacks are cleared once pushed
	};

		
	/**
	 * Initialize and setup the push.
	 * 
	 * @param {Object} environment 
	 * @see TaoStack.push.environment
	 * @param {Object} settings 
	 * @see TaoStack.push.settings
	 */
	this.initPush = function(environment, settings){
		if(environment.url){
			if(/(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/.test(environment.url)){	//test url
	
				this.push.environment.url = environment.url;		//set url
				
				if($.isPlainObject(environment.params)){	//ADD parameters
					for(key in environment.params){
						if(isScalar(environment.params[key]) && !this.push.environment.params[key]){	//don't edit the common params
							this.push.environment.params[key] = environment.params[key]; 
						}
					}
				}
				if($.isPlainObject(settings)){	//set push settings
					if(settings.method){
						if(/^get|post$/i.test(settings.method)){
							this.push.settings.method = settings.method;
						}
					}
					if(settings.async === false){
						this.push.settings.async = false;
					}
					if(settings.clearAfter === false){
						this.push.settings.clearAfter = false;
					}
				}
			}
		}
	};
	
	/**
	 * push all the data to the server
	 */
	this.push = function(){
		
		var params = this.push.environment.params;	//common parameters
		
		for (key in this.taoVars){					//tao variables
			if(/^##NAMESPACE#/.test(key)){
				key = key.replace('##NAMESPACE#', this.dataStore.localNamespace);
			}
			params['taoVars'][key]= this.taoVars[key];
		}
		 
		//push the data to the server
		var instance = this;
		$.ajax({
			'url'  		: this.push.environment.url,
			'data' 		: params,
			'type' 		: this.push.settings.method,
			'async'		: this.push.settings.async,
			'dataType'  : this.push.settings.format,
			'success' 	: function(data){
				if(data.saved){
					if(instance.push.settings.clearAfter){
						instance.taoVars  = new Object();
						instance.userVars = new Object();
					}
				}
			}
		});
	};
	
/* TAO Variables */
	
	/**
	 * @var {Object} contains the tao vars 
	 */
	this.taoVars = new Object();
	
	/**
	 * @param {String} key
	 * @param {boolean} label if you want to retrieve the label instead of the complete Object
	 * @return {mixed} value (false if the key is not found)
	 */
	this.getTaoVar = function(key, label){
		var value =  (this.taoVars[key]) ? this.taoVars[key] : false;
		
		if($.isPlainObject(value)){
			if( (value.indexOf('uri') > -1 && value.indexOf(URI.LABEL) > -1 && value.length == 2) || label){
				return value[URI.LABEL];
			}
		}
		return value;
	};
	
	/**
	 * The set method is restricted to scalar,
	 * but could be used to reference a property node
	 * 
	 * @param {String} key
	 * @param {String|int|float|boolean} value
	 * @param {String} [property] the property uri 
	 */
	this.setTaoVar = function(key, value, property){
		
		if(isScalar(value)){
		
			var currentValue =  (this.taoVars[key]) ? this.taoVars[key] : false;
			if($.isPlainObject(currentValue)){
				if(property){
					this.taoVars[key][property] = value;
				}
				else if( value.indexOf('uri') > -1 && value.indexOf(URI.LABEL) > -1){
					this.taoVars[key][URI.LABEL] = value;
				}
			}
			else{
				this.taoVars[key] = value;
			}
		}
	};
	
/* Custom Variables */
	
	/**
	 * @var {Object} contains the user custom vars 
	 */
	this.userVars = new Object();
	
	/**
	 * @param {String} key
	 * @return {String|int|float|boolean} value (false if the key is not found)
	 */
	this.getUserVar = function(key){
		return (this.userVars[key]) ? this.userVars[key] : false;
	};
	
	/**
	 * @param {String} key
	 * @param {String|int|float|boolean} value
	 */
	this.setUserVar = function(key, value){
		if(isScalar(value)){
			this.userVars[key] = value;
		}
	};
}


/**
 * Utility function to check if a value is a scalar
 * @param {mixed} value
 * @return {bool} true if it's a scalar
 */
function isScalar(value){
	switch((typeof value).toLowerCase()){
		case 'string':
		case 'number':
		case 'boolean':
			return true;
			
		default: 
			return false;
	}
	return false;
}

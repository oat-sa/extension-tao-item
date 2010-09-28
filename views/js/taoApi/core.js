/**
 * TAO API 
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * 
 * @require jquery >= 1.4.0 {@link http://www.jquery.com}
 *
 */

/**
 * The TaoStack class
 */
function TaoStack(){
	
	/**
	 * @var {Object} the data source 
	 */
	this.dataSource = new Object();
	this.dataSource.environment = {
		'type'		: 'async', 		// (manual|sync|async) 
		'url' 		: '/tao/',		// the url to the server [NOT for manual type] 
		'params'	: {	}			// the key/values to send to the server [NOT for manual type] 
	};
	this.dataSource.settings = {
		'format'		: 'json',	//only json is supported now
		'method' 		: 'post',	//HTTP method (get|post) [NOT for manual type] 
		'load'			: 'onInit' 	// when the source is loaded (onInit|onGet) [onInit required for manual type]
	};
	
	/**
	 * Initialize and setup the data source.
	 * 
	 * @param {Object} environment 
	 * @see TaoStack.dataSource.environment
	 * @param {Object} settings 
	 * @see TaoStack.dataSource.settings
	 */
	this.initDataSource = function(environment, settings){
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
					if(settings.load){
						if(/^onInit|onGet$/i.test(settings.load)){
							this.dataSource.settings.load = settings.load;
						}
					}
				}
			}
		}
	};
	
	/**
	 * @var {Object} the push data
	 */
	this.push = new Object();
	this.push.environment = {
		'url' 		: '/tao/',		// the url to the server
		'params'	: {	}			// the key/values to send to the server at each communication 
	};
	this.push.settings = {
		'format'		: 'json',	//only json is supported now
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
				
				if($.isPlainObject(environment.params)){	//set parameters
					for(key in environment.params){
						if(isScalar(environment.params[key])){
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
		
		var data = this.environment.params
		data['taoVars'] = this.taoVars;
		data['userVars'] = this.userVars;
		
		var instance = this;
		$.ajax({
			'url'  		: this.environment.url,
			'type' 		: this.pushSettings.method,
			'async'		: this.pushSettings.async,
			'data' 		: this.environment.params,
			'dataType'  : 'json',
			'success' 	: function(data){
				if(data.saved){
					if(instance.pushSettings.clearAfter){
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
	 * @return {String|int|float|boolean} value (false if the key is not found)
	 */
	this.getTaoVar = function(key){
		return (this.taoVars[key]) ? this.taoVars[key] : false;
	};
	
	/**
	 * @param {String} key
	 * @param {String|int|float|boolean} value
	 */
	this.setTaoVar = function(key, value){
		if(isScalar(value)){
			this.taoVars[key] = value;
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

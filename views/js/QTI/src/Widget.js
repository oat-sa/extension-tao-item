/**
 * TAO QTI API
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * 
 * @requires jquery {@link http://www.jquery.com}
 */

/**
 * The QTIWidgetFactory class enables you to build a QTI widget 
 * from XHTML elements and the given options
 * 
 * @class QTIWidgetFactory
 * @property {Object} options the interaction of parameters 
 */
var QTIWidgetFactory = function(options){
	
	//keep the current instance pointer
	var _this = this;

	/**
	 * To access the widget options 
	 * @fieldOf QTIWidgetFactory
	 * @type {Object}
	 */
	this.opts = options;

	/**
	 * the interaction selector, all elements selected must be inside this element,
	 * to be able to have some interactions in the same item
	 * @fieldOf QTIWidgetFactory
	 * @type {String}
	 */
	this.qti_item_id = "#"+this.opts["id"];
	
	
	/**
	 * the path of that library from an url,
	 * to access images.
	 * @fieldOf QTIWidget
	 * @type {String}
	 */
	this.wwwPath = '';
	//use the global variable qti_base_www
	if(typeof(qti_base_www) != 'undefined'){
		this.wwwPath = qti_base_www;
		if(!/\/$/.test(this.wwwPath) && this.wwwPath != ''){
			this.wwwPath += '/';
		}
	}
	
	/**
	 * @fieldOf QTIWidget
	 * @type {boolean}
	 */
	this.graphicDebug  = false; 
	//use the global variable qti_debug
	if(typeof(qti_debug) != 'undefined'){
		this.graphicDebug = qti_debug;
	}
	
	/**
	 * Build the widget of typename
	 * @param {String} typename
	 */
	this.build = function(typeName){
		if(typeof(qti_debug) != 'undefined'){
			if(!QTIWidget[typeName]){
				alert("Error: Unknow widget " + typeName);
			}
		}
		QTIWidget[typeName](_this);	//call the right method into the widgets/* file
	};

};

/*
 * Utilities
 */

/**
 * Get the pointer of a poly shape reagrding it's path
 * @function
 */
function pointerPolyCoordonates(path){
	var pathArray=new Array();
	pathArray=path.split(",");
	return [pathArray[0],pathArray[1]];
}
/**
 * Get the corrdinates of a poly shape reagrding it's path
 * @param path 
 * @returns 
 */
function polyCoordonates(path){
	var pathArray=new Array();
	pathArray=path.split(",");
	var pathArrayLength=pathArray.length;		
	// autoClose if needed
	if ((pathArray[0]!=pathArray[pathArrayLength-2]) && (pathArray[1]!=pathArray[pathArrayLength-1])){
		pathArray.push(pathArray[0]);
		pathArray.push(pathArray[1]);
	}		
	// move to first point
	pathArray[0]="M"+pathArray[0];		
	for (var a=1;a<pathArrayLength;a++){
		if (isPair(a)){
			pathArray[a]="L"+pathArray[a];
		}
	}		
	return pathArray.join(" ");		
}

/**
 * Check if number is pair or not
 * @function
 * @param nombre the number
 * @returns {Number}
 */
function isPair(number){
	return ((number-1)%2);
}

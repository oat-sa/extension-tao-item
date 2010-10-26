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
 * The QTIResultCollector class collects the user results of a QTI widgets
 * and return a well formated variable 
 * <i>(the type of the returned variable is deterministic for the result processing)</i>
 * 
 * @namespace QTI
 * @class QTIResultCollector
 * @property {Object} options the widget parameters
 */
function QTIResultCollector(options){

	//keep the current instance pointer
	var _this = this;
	
	/**
	 * The widget options 
	 * @type Object 
	 */
	this.opts = options;
	
	/**
	 * The id of the widget
	 * @type String
	 */
	this.id = options['id'];
	
	
	/**
	 * Collect the results of a <b>choice</b> widget 
	 * 
	 * @returns {Object} the results
	 */
	this.choice = function(){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: (_this.opts["maxChoices"] != 1) ? [] : null
		};
		
		var userData = new Array();
		$("#" + _this.id + " .tabActive").each(function(){
			if (_this.opts["maxChoices"] != 1) 
				result.value.push(this.id);
			else
				result.value = this.id;
		});
		
		return result;
	};

	
	/**
	 * Collect the results of an <b>order</b> widget 
	 * 
	 * @returns {Object} the results
	 */
	this.order = function (){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: new Object()
		};
		var i = 0;
		
		$("#" + _this.id + " ul.qti_choice_list li").each(function(){
			result.value[i] = this.id;
			i++;
		});
		return result;
	};

	
	/**
	 * Collect the results of an <b>associate</b> widget 
	 * 
	 * @returns {Object} the results
	 */
	this.associate = function(){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: (_this.opts["maxChoices"] != 1) ? [] : null
		};
		
		$("#" + _this.id + " .qti_association_pair").each(function(){
			// The field has not been filled
			if (!$(this).find('li:first').find('.filled_pair').length){
				return;
			}
			
			// Get the associated identifier
			var firstId = $(this).find('li:first').find('.filled_pair').attr('id').replace('pair_', '');
			var lastId 	= $(this).find('li:last').find('.filled_pair').attr('id').replace('pair_', '');
			
			// create the element following the matching format
			var elt = null;
			if (_this.opts.responseBaseType == "pair"){
				elt = [firstId, lastId];
			} else if (_this.opts.responseBaseType == "directedPair"){
				elt = {0:firstId, 1:lastId};
			}
			
			if (_this.opts["maxChoices"] != 1){
				result.value.push (elt);
			} else {
				result.value = elt;
			}
		});
		
		return result;
	};
	

	/**
	 * Collect the results of text based widget : 
	 * <b>text_entry</b> and <b>extended_text</b>
	 * 
	 * @todo Multiple not tested
	 * @returns {Object} the results
	 */
	this.text = function(){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: null
		};
		
		//single mode
		if($("#" + _this.id ).get(0).nodeName.toLowerCase() != 'div'){
			result.value = $("#" + _this.id).val();
		} 
		//multiple mode
		else {
			result.value = new Array();
			$("#" + _this.id + " :text").each(function(){
				result.value.push($(this).val());
			});	
		}
		
		return result;
	};
	
	
	/**
	 * @see QTIResultCollector#text
	 */
	this.text_entry = this.text;
	
	
	/**
	 * @see QTIResultCollector#text
	 */
	this.extended_text = this.text;

	
	/**
	 * Collect the results of an <b>inline_choice</b> widget 
	 * 
	 * @returns {Object} the results
	 */
	this.inline_choice = function(){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: $("#" + _this.id).val()
		};
		return result;
	};

	
	/**
	 * Collect the results of an <b>hottext</b> widget 
	 * 
	 * @returnss {Object} the results
	 */
	this.hottext = function(){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: (_this.opts["maxChoices"] != 1) ? [] : null
		};
		$("#" + _this.id + " .hottext_choice_on").each(function(){
			if (_this.opts["maxChoices"] != 1) 
				result.value.push(this.id.replace(/^hottext_choice_/, ''));
			else
				result.value = this.id.replace(/^hottext_choice_/, '');
		});
		return result;
	};

	
	/**
	 * Collect the results of an <b>gap_match</b> widget 
	 * 
	 * @todo does not work with single cardinality
	 * @returns {Object} the results
	 */
	this.gap_match = function(){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: (_this.opts["maxChoices"] != 1) ? [] : null
		};
		
		$("#" + _this.id + " .filled_gap").each(function(){
			var firstId = $(this).attr('id').replace('gap_', '');
			var lastId = $(this).parent().attr('id');
			result.value.push({0:firstId, 1:lastId});
		});
		
		return result;
	};
	
	
	/**
	 * Collect the results of a <b>match</b> widget 
	 * 
	 * @todo does not work with single cardinality
	 * @returns {Object} the results
	 */
	this.match = function(){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: []
		};
		
		$("#" + _this.id + " .tabActive").each(function(){
			var subset = new Object();
			var classes = $(this).attr('class').split(' ');
			if(classes.length > 0){
				var i = 0;
				while(i < classes.length){
					if(/^xnode_/.test(classes[i])){
						subset[0] = classes[i].replace('xnode_', '');
					}
					if(/^ynode_/.test(classes[i])){
						subset[1] = classes[i].replace('ynode_', '');
					}
					i++;
				}
				result.value.push(subset);
			}
		});
		return result;
	};
}
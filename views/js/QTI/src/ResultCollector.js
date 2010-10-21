/**
 * TAO QTI API
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * 
 * @require jquery {@link http://www.jquery.com}
 */

/** 
 * The QTIResultCollector class collects the user results of a QTI widgets defineby the options
 * @namespace QTI
 * @class QTIResultCollector
 * @param {Object} options
 */
function QTIResultCollector(options){

	var _this = this;
	
	this.opts = options;
	this.id = options['id'];
	
	// result process
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

	this.associate = function(){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: (_this.opts["maxChoices"] != 1) ? [] : null
		};
		
		$("#" + _this.id + " .qti_association_pair").each(function(){
			// The field has not been filled
			if (!$(this).find('li:first').find('.qti_droppedItem').length){
				return;
			}
			
			// Get the associated identifier
			var firstId = $(this).find('li:first').find('.qti_droppedItem')[0].id;
			var lastId = $(this).find('li:last').find('.qti_droppedItem')[0].id;
			
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
	
	// @todo Multiple not tested
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
	this.text_entry = this.text;
	this.extended_text = this.text;

	this.inline_choice = function(){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: $("#" + _this.id).val()
		};
		return result;
	};

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

	// @todo does not work with single cardinality
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
	
	// @todo does not work with single cardinality
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
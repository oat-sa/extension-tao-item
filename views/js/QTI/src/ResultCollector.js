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
 * @class QTIResultCollector
 * @property {Object} options the widget parameters
 */
function QTIResultCollector(options){

	//keep the current instance pointer
	var _this = this;

	/**
	 * The widget options
	 * @fieldOf QTIResultCollector
	 * @type Object
	 */
	this.opts = options;

	/**
	 * The id of the widget
	 * @fieldOf QTIResultCollector
	 * @type String
	 */
	this.id = options['id'];


	/**
	 * Collect the results of a <b>choice</b> widget
	 * @methodOf QTIResultCollector
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
	 * @methodOf QTIResultCollector
	 * @returns {Object} the results
	 */
	this.order = function (){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: new Object()
		};
		var i = 0;
		var listClass = (_this.opts['orientation'] == 'horizontal') ? 'qti_choice_list_horizontal' : 'qti_choice_list';
		$('#' + _this.id + ' ul.' + listClass + ' li').each(function(){
			result.value[i] = this.id;
			i++;
		});
		return result;
	};


	/**
	 * Collect the results of an <b>associate</b> widget
	 * @methodOf QTIResultCollector
	 * @returns {Object} the results
	 */
	this.associate = function(){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: (_this.opts["maxAssociations"] == 1)?null:[]
		};
		
		$("#" + _this.id + " .qti_association_pair").each(function(){
			// The field has not been filled
			if (!$(this).find('li:first').find('.filled_pair').length){
				return;
			}

			// Get the associated identifier
			var firstId = '';
			if($(this).find('li:first').find('.filled_pair').length > 0){
				firstId = $(this).find('li:first').find('.filled_pair').attr('id').replace('pair_', '');
			}
			var lastId 	= '';
			if($(this).find('li:last').find('.filled_pair').length > 0){
				lastId = $(this).find('li:last').find('.filled_pair').attr('id').replace('pair_', '');
			}



			// create the element following the matching format
			var elt = null;
			if (_this.opts.responseBaseType == "pair"){
				elt = [firstId, lastId];
			} else if (_this.opts.responseBaseType == "directedPair"){
				elt = {0:firstId, 1:lastId};
			}
			
			//maxAssociations = 0 => infinite association available
			if(_this.opts["maxAssociations"] == 1){
				result.value = elt;
			}else if(_this.opts["maxAssociations"] == 0 || result.value.length < _this.opts["maxAssociations"]){
				result.value.push(elt);
			}
			
		});

		return result;
	};


	/**
	 * Collect the results of text based widget :
	 * <b>text_entry</b> and <b>extended_text</b>
	 * @methodOf QTIResultCollector
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
			switch(_this.opts['baseType']){
				case "integer":
					result.value = parseInt($("#" + _this.id).val());
					break;
				case "float":
					result.value = parseFloat($("#" + _this.id).val());
					break;
				case "string":
				default:
					result.value = $("#" + _this.id).val();
			}
		}
		//multiple mode
		else {
			result.value = new Array();
			$("#" + _this.id + " :text").each(function(){
				switch(_this.opts['baseType']){
				case "integer":
					result.value.push(parseInt($("#" + _this.id).val()));
					break;
				case "float":
					result.value.push(parseFloat($("#" + _this.id).val()));
					break;
				case "string":
				default:
					result.value.push($("#" + _this.id).val());
			}
			});
		}

		return result;
	};


	/**
	 * @methodOf QTIResultCollector
	 * @see QTIResultCollector#text
	 */
	this.text_entry = this.text;


	/**
	 * @methodOf QTIResultCollector
	 * @see QTIResultCollector#text
	 */
	this.extended_text = this.text;


	/**
	 * Collect the results of an <b>inline_choice</b> widget
	 * @methodOf QTIResultCollector
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
	 * @methodOf QTIResultCollector
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
	 * @methodOf QTIResultCollector
	 * @todo does not work with single cardinality
	 * @returns {Object} the results
	 */
	this.gap_match = function(){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: []
		};

		$("#" + _this.id + " .filled_gap").each(function(){
			var choiceId = $(this).attr('id').replace('gap_', '');
			var groupId = $(this).parent().attr('id');
			result.value.push({0:groupId, 1:choiceId});
		});

		return result;
	};


	/**
	 * Collect the results of a <b>match</b> widget
	 * @methodOf QTIResultCollector
	 * @todo does not work with single cardinality
	 * @returns {Object} the results
	 */
	this.match = function(){
		var result = {
			"identifier": _this.opts['responseIdentifier'] // Identifier of the response
			, "value"	: (_this.opts["maxAssociations"] == 1)?null:[]
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
				
				//maxAssociations = 0 => infinite association available
				if(_this.opts["maxAssociations"] == 1){
					result.value = subset;
				}else if(_this.opts["maxAssociations"] == 0 || result.value.length < _this.opts["maxAssociations"]){
					result.value.push(subset);
				}
			
			}
		});
		return result;
	};

	/**
	 * Collect the results of an <b>hotspot</b> widget
	 * @methodOf QTIResultCollector
	 * @returns {Object} the results
	 */
	this.hotspot = function(){
		var result = {
				"identifier": _this.opts['responseIdentifier'] // Identifier of the response
				, "value"	: []
			};
		$("#" + _this.id + " li.activated").each(function(){
			if (_this.opts["maxChoices"] != 1)
				result.value.push($(this).attr('id'));
			else
				result.value = $(this).attr('id');
		});

		return result;
	};

	/**
	 * Collect the results of an <b>hotspot</b> widget
	 * @methodOf QTIResultCollector
	 * @returns {Object} the results
	 */
	this.select_point = function(){
		var result = {
				"identifier": _this.opts['responseIdentifier'] // Identifier of the response
				, "value"	: []
			};
		$("#" + _this.id + " img.select_point_cross").each(function(){
			var coords = $(this).data('coords').split(',');
			if (_this.opts["maxChoices"] != 1)
				result.value.push({'0': coords[0], '1': coords[1]});
			else
				result.value = {'0': coords[0], '1': coords[1]};
		});
		return result;
	};

	/**
	 * Collect the results of a <b>graphic order</b> widget
	 * @methodOf QTIResultCollector
	 * @returns {Object} the results
	 */
	this.graphic_order = function(){
		var result = {
				"identifier": _this.opts['responseIdentifier'] // Identifier of the response
				, "value"	: new Object()
			};
		var orderedElts = $("#" + _this.id).data('order');

		for( id in orderedElts){
			var orderedElt = orderedElts[id];
			if(orderedElt.state != 'empty'){
				result.value[parseInt(orderedElt.order)-1] = id;
			}
		}
		return result;
	};

	/**
	 * Collect the results of a <b>graphic associate</b> widget
	 * @methodOf QTIResultCollector
	 * @returns {Object} the results
	 */
	this.graphic_associate = function(){
		var result = {
				"identifier": _this.opts['responseIdentifier'] // Identifier of the response
				, "value"	: (_this.opts["maxAssociations"] == 1)?null:[]
			};
		var pairs = $("#" + _this.id).data('pairs');
		for(i in pairs){
			var pair = pairs[i].split(' ');
			if(pair.length == 2){
				if(_this.opts["maxAssociations"] == 1){
					result.value = pair;
				}else if(_this.opts["maxAssociations"] == 0 || result.value.length < _this.opts["maxAssociations"]){
					result.value.push(pair);
				}
			}
		}
		return result;
	};

	/**
	 * Collect the results of a <b>graphic gap match</b> widget
	 * @methodOf QTIResultCollector
	 * @returns {Object} the results
	 */
	this.graphic_gap_match = function(){
		var result = {
				"identifier": _this.opts['responseIdentifier'] // Identifier of the response
				, "value"	: []
			};

		$("#" + _this.id + " .filled_gap").each(function(){
			var filledId = $(this).attr("id").replace('gap_', '').split('_');
			result.value.push({0:filledId[0], 1:filledId[1]});
		});

		if ($.isArray(result.value) && result.value.length == 1) {
		    result.value = result.value.shift();
		}
		
		return result;
	};

	/**
	 * Collect the results of an <b>slider</b> widget
	 * @methodOf QTIResultCollector
	 * @returns {Object} the results
	 */
	this.slider = function(){
		return {
				"identifier": _this.opts['responseIdentifier'] // Identifier of the response
				, "value"	: parseInt($("#" + _this.id +'_qti_slider_value').val())
			};
	};

	/**
	 * Collect the results of an <b>upload</b> widget
	 * @methodOf QTIResultCollector
	 * @returns {Object} the results
	 */
	this.upload = function(){
		var value = 0;
		switch(_this.opts['baseType']){
			case "float":
				value = parseFloat( $("#" + _this.id +'_data').val());
				break;
			case "integer":
			default:
				value = parseInt( $("#" + _this.id +'_data').val());
		}

		return {
				"identifier": _this.opts['responseIdentifier'] // Identifier of the response
				, "value"	: value
			};
	};

	/**
	 * Collect the results of an <b>endattempt</b> widget
	 * @methodOf QTIResultCollector
	 * @returns {Object} the results
	 */
	this.end_attempt = function(){
		var value = 0;
		if(parseInt( $("#"+_this.id+'_data').val()) > 1){
			value = 1;
		}

		return {
				"identifier": _this.opts['responseIdentifier'] // Identifier of the response
				, "value"	: value
			};
	};
}


/** 
 * @param {Object} options
 */
function QTIResultCollector(options){

	this.opts = options;
	this.id = options['id'];
	
	var _this = this;
	
	// result process
	this.choice = function(){
		var result = {
			"identifier"	: _this.opts['responseIdentifier'] // Identifier of the response
			, "cardinality" : _this.opts["maxChoices"] << 1 != 2 ? 'multiple' : 'single'
			, "type"		: _this.id
			, "values"		: []
		};
		
		var userData = new Array();
		$("#" + _this.id + " .tabActive").each(function(){
			userData.push(this.id);
		});
		result.values = userData;
		
		return result;
	};
	
	this.order = function (){
		var result = new Array();
		$("#" + _this.id + " ul.qti_choice_list li").each(function(){
			result.push(this.id);
		});
		return result;
	};

	this.associate = function(){
		var result = new Array();
		$("#" + _this.id + " .qti_association_pair").each(function(){
			result.push([$(this).find('li:first').attr('id'), $(this).find('li:last').attr('id')]);
		});
		return result;
	};

	this.text = function(){
		
		//single mode
		if($("#" + _this.id ).get(0).nodeName.toLowerCase() != 'div'){
			return new Array($("#" + id).val());
		}
		
		//multiple mode
		var result = new Array();
		$("#" + _this.id + " :text").each(function(){
			result.push($(this).val());
		});
		return result;
	};
	this.text_entry = this.text;
	this.extended_text = this.text;

	this.inline_choice = function(){
		return [$("#" + _this.id).val()];
	};

	this.hottext = function(){
		var result = new Array();
		$("#" + _this.id + " .hottext_choice_on").each(function(){
			result.push(this.id.replace("/^hottext_choice_/", ''));
		});
		return result;
	};

	this.gap_match = function(){
		var result = new Array();
		$("#" + _this.id + " .filled_gap").each(function(){
			result.push([$(this).attr('id').replace('gap_', ''), $(this).parent().attr('id')]);
		});
		return result;
	};
	
	this.match = function(){
		var result = new Array();
		$("#" + _this.id + " .tabActive").each(function(){
			var subset = new Array();
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
				result.push(subset);
			}
		});
		return result;
	};
}
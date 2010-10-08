/**
 * Initialize the QTI environment
 * @param {Object} qti_initParam the parameters of ALL item's interaction
 * @return void
 */
function qti_init(qti_initParam){
	for (var a in qti_initParam){
		qti_init_interaction(qti_initParam[a]);
	}
}

/**
 * Initialize the widget and the result collection for an interaction
 * @param {Object} initObj the params of the interaction (parts of qti_initParam identified by the interaction id)
 * @return void
 */
function qti_init_interaction(initObj){
	
	// Instantiate the qtiMatching class
	var qtiMatching = new QTIMatching ();
	// Set the rule with a "Match Temporary Rule" which is compliant with the single choice interaction
	qtiMatching.setRule ("if(match(getVariable('RESPONSE'), getCorrect('RESPONSE'))) setOutcomeValue('SCORE', 1); else setOutcomeValue('SCORE', 0);");
	// Set "Temporary Correct Variables" 
	var correctsSerialized = "[{identifier:'RESPONSE', type:'identifier', cardinality:'single', values:['ChoiceA']}]";
	var correctsVar = unserializedQTIVariables (correctsSerialized);
	qtiMatching.setCorrects (correctsVar);
	// Set "Temporary Variables Variables" 
//	var variablesSerialized = "[{identifier:'RESPONSE', type:'identifier', cardinality:'single', values:['ChoiceA']}]";
//	var variablesVar = unserializedQTIVariables (variablesSerialized);
//	qtiMatching.setVariables (variablesVar);
	// Set "Temporary Outcome Variables" 
	var outcomeSerialized = "[{identifier:'SCORE', type:'float', cardinality:'single', values:[]}]";
	var outcomesVar = unserializedQTIVariables (outcomeSerialized);
	qtiMatching.setOutcomes (outcomesVar);
	
	//instantiate the widget class with the given interaction parameters
	var myQTIWidget = new QTIWidget(initObj);
	
	//instantiate the result class with the interaction id
	var myResultCollector = new QTIResultCollector(myQTIWidget);
	
	//get the interaction type to identify the method 
	var typeName = initObj["type"].replace('qti_', '').replace('_interaction', '');
	
	if(!myQTIWidget[typeName]){
		console.log(typeName);
	}
	
	//call the widget initialization method
	myQTIWidget[typeName].apply();
	
	// validation process
	$("#qti_validate").bind("click",function(){
		// Get user's data
		var result = myResultCollector[typeName].apply();
		console.log (result);
		
		// Create QTIVariable according to the user's data
		var myUserVar = QTIVariableFactory (result.identifier, result.type, result.cardinality, result.values);
		qtiMatching.setVariables ({'RESPONSE': myUserVar});
		
		// eval the response processing
		try {
			qtiMatching.evalResponseProcessing ();
			console.log ('your score : ' +qtiMatching.getOutcome('SCORE').values[0]);
		}catch (e){ 
			console.log(e);
		}
	});
		
}

/**
 * The QTIWidget class enables you to build a QTI widget from an XHTML Element and the given options
 * @param {Object} options
 */
function QTIWidget(options){
	
	//keep the current instance pointer
	var _this = this;

	this.opts = options;

	var qti_item_id = "#"+this.opts["id"];
	
	/**
	 * Creates a choice list widget
	 * @see simple_choice, multiple_choice
	 */
	this.choice = function(){
		var maxChoices =  parseInt(_this.opts["maxChoices"]);
		if(maxChoices > 1 || maxChoices == 0){
			_this.multiple_choice();
		}
		else{
			_this.simple_choice();
		}
	};

	/**
	 * Creates a simple choice list widget
	 */
	 this.simple_choice = function (){	
		$(qti_item_id).addClass('qti_simple_interaction');
		$(qti_item_id +" ul li").bind("click",function(){	
			$(qti_item_id+" ul li").removeClass("tabActive");		
			$(this).addClass("tabActive");					
		});
	}

	/**
	 * Creates a multiple choice list widget
	 */
	this.multiple_choice = function (){
		$(qti_item_id).addClass('qti_multi_interaction');
		$(qti_item_id+" ul li").bind("click",function(){
			if ($(this).hasClass("tabActive")) {
				$(this).removeClass("tabActive");
			}
			else {
				if ($(qti_item_id+" ul li.tabActive").length < _this.opts["maxChoices"] || _this.opts["maxChoices"] == 0) {
					$(this).addClass("tabActive");
				}
			}
		});
	};
	
	this.inline_choice = function (){};

	/**
	 * Creates a sortable list widget,
	 * can be horizontal or vertical regarding the orientation parameter
	 * @param {Object} currentObj
	 */
	this.order = function(){
		
		var sortableOptions = {
			revert: true,
			axis : 'y',
			containment: qti_item_id,
			placeholder: 'sort-placeholder',
			tolerance: 'pointer'
		};
		
		//for an horizontal sortable list
		$(qti_item_id + " ul li").addClass('sort-vertical');
		if(_this.opts['orientation']){
			if(_this.opts['orientation'] == 'horizontal'){
				sortableOptions.axis = 'x';
				sortableOptions.placeholder = 'sort-placeholder-inline';
				sortableOptions['forcePlaceHolderWidth'] = true;
				$(qti_item_id+" ul li")
						.removeClass('sort-vertical')
							.addClass('sort-horizontal')
								.css('display', 'inline');
			}
		}
		
		$(qti_item_id+" ul").sortable(sortableOptions);
		$(qti_item_id+" ul, li").disableSelection();
	};

	/**
	 * Creates a pair association widget, 
	 * where words are dragged from a cloud to pair boxes
	 */
	this.associate = function(){
		
			// max size of a text box to define target max size
			// create empty element in order to droppe in any other element of the item
			$(qti_item_id+" .qti_choice_list li").wrapInner("<div></div>");
			// calculate max size of a word in the cloud
			var maxBoxSize=0;
			$(qti_item_id+" ul.qti_choice_list li > div").each(function(){	
				if ($(this).width()>maxBoxSize){
					maxBoxSize=$(this).width();		
				}
			});
					
			// give a size to the words cloud to avoid graphical "jump" when items are dropped
			$(qti_item_id+" .qti_associate_container .qti_choice_list").height(parseFloat($(".qti_associate_container").height()));
			
			// create the pair of box specified in the maxAssociations attribute		
			for (var a=_this.opts["maxAssociations"];a>0;a--){
				var currentPairName=_this.opts["id"]+"_"+a;
				$(qti_item_id+" .qti_associate_container").after("<ul class='qti_association_pair' id='"+currentPairName+"'><li id='"+currentPairName+"_A"+"'></li><li id='"+currentPairName+"_B"+"'></li></ul>");		
			}
			
			// create an object in the current Object of the item (passed in argument) to store 
			// infos between the words cloud and the pair list
			_this.opts["link"]={};
			
			// create a label for each drop box from is id
			// setted to "null" because there is no association when item start
			$(qti_item_id+" .qti_association_pair li").each(function(){		
				_this.opts["link"][$(this).attr("id")]="null";
			});
			
			// set the size of the drop box to the max size of the cloud words 
			$(qti_item_id+" .qti_association_pair li").width(maxBoxSize+4);
			
			// size the whole pair box to center it
			var pairBoxWidth=0;
			
			$(qti_item_id+" .qti_association_pair:first li").each(function(){
				pairBoxWidth+=$(this).width();
			});
			
			$(qti_item_id+" .qti_association_pair").width(pairBoxWidth+90);
			$(qti_item_id+" .qti_association_pair").css({position:"relative",margin:"0 auto",top:"10px"});	
			
			//place target boxes
			$(qti_item_id+" .qti_association_pair").each(function(){
				$(this).after("<div class='qti_link_associate'></div>");
				
				$(qti_item_id+" .qti_link_associate:last").css("top",$(this).offset().top+23);
				$(qti_item_id+" .qti_link_associate:last").css("left",parseFloat($(this).find("li:first").offset().left)+parseFloat($(this).find("li:first").width())+14);
			
			
			//drag element from words cloud
			$(qti_item_id+" .qti_associate_container .qti_choice_list li > div").draggable({
				drag: function(event, ui){
					// label go on top of the others elements
					$(ui.helper).css("z-index","999");			
				},
				containment: qti_item_id,
				cursor:"move"
			});
				
			// pair box are droppable
			$(qti_item_id+" .qti_association_pair li").droppable({
				drop: function(event, ui){
					// add class to highlight current dropped item in pair boxes
					$(this).addClass('ui-state-highlight');
					
					
					// add new element inside the box that received the cloud element
					$(this).html("<div class='qti_dropped'><div class='qti_droppedItem'>"+$(ui.draggable).text()+"</div></div>");
					
					// _this.opts store id of the cloud word to maintain a link
					_this.opts["link"][$(this).attr("id")]=$(ui.draggable).parent().attr("id");
					
					if($(ui.helper).parentsUntil("ul").parent().hasClass("qti_choice_list"))
					{
						$(ui.helper).css({top:"0",left:"0"});
						var _matchMax = Number(_this.opts["matchMaxes"][$(ui.draggable).parent().attr("id")]["matchMax"]);
						var _current = Number(_this.opts["matchMaxes"][$(ui.draggable).parent().attr("id")]["current"]);
					
						if (_current < _matchMax) {
							
							_current++;
							_this.opts["matchMaxes"][$(ui.draggable).parent().attr("id")]["current"]=_current;
						}
						if (_current >= _matchMax) {
							$(ui.draggable).hide();
						}
		
					} 
					else {
						_this.opts["link"][$(this).attr("id")]=_this.opts["link"][$(ui.draggable).parentsUntil(".ui-droppable").parent().attr("id")];
						_this.opts["link"][$(ui.draggable).parentsUntil(".ui-droppable").parent().attr("id")]="null";
					}	
					// give a size to the dropped item to overlapp perfectly the pair box
					$(qti_item_id+" .qti_droppedItem").width($(qti_item_id+" .qti_association_pair li").width());
					$(qti_item_id+" .qti_droppedItem").height($(qti_item_id+" .qti_association_pair li").height());
					// give this new element the ability to be dragged
					$(qti_item_id+" .qti_droppedItem").draggable({
						drag: function(event, ui){
							// element is on top of the other when it's dragged
							$(this).css("z-index", "999");
						},
						stop: function(event, ui) { 		
							//var currentDroppedItemId=$(ui.helper).parentsUntil("li").parent().attr("id");
							$(this).parentsUntil(".ui-state-highlight").parent().removeClass('ui-state-highlight');
							$(this).parent().remove();		
						 },
						 containment: qti_item_id,
						 cursor:"move"
					});
					
									
				},
				hoverClass: 'active'
			});	
			
			$(qti_item_id).droppable({
				drop: function(event, ui){
					if($(ui.draggable).parentsUntil("ul").parent().hasClass("qti_choice_list"))
					{
						$(ui.draggable).css({top:"0",left:"0"});
					} else {
						var currentDroppedItemId=$(ui.draggable).parentsUntil("li").parent().attr("id");
						$(qti_item_id+" #"+_this.opts["link"][currentDroppedItemId]+" div").show();
						_this.opts["matchMaxes"][_this.opts["link"][currentDroppedItemId]]["current"]--;
						_this.opts["link"][currentDroppedItemId]="null";
					}
				},
				hoverClass: 'active'
			});
			}
		);	
	};

	/**
	 * Creates a text entry widget
	 * @see QTIWidget.string_interaction
	 */
	this.text_entry = function (){
		//adapt the field length
		if(_this.opts['expectedLength']){
			length = parseInt(_this.opts['expectedLength']);
			$(qti_item_id).css('width', (length * 10) + 'px')
							.attr('maxLength', length);
		}
		
		_this.string_interaction();
	};

	/**
	 * Creates a  extended text widget,
	 * it can be a big text area or a set of text entries regarding the context
	 * @see QTIWidget.string_interaction
	 */
	this.extended_text = function (){
		
			
		//usual case: one textarea 
		if($(qti_item_id).get(0).nodeName.toLowerCase() == 'textarea') {
			
			//adapt the field length
			if(_this.opts['expectedLength'] || _this.opts_this.opts['expectedLines']){
				
				baseWidth 	= parseInt($(qti_item_id).css('width')) | 400;
				baseHeight 	= parseInt($(qti_item_id).css('height')) | 100;
				if(currentObj['expectedLength']){
					length 		= parseInt(_this.opts['expectedLength']);
					width = length * 10;
					if( width > baseWidth){
						height = (width / baseWidth) * 16;
						if(height  > baseHeight){
							$(qti_item_id).css('height', height + 'px');
						}
					}
					$(qti_item_id).attr('maxLength', length);
				}
				if(_this.opts['expectedLines']){
					$(qti_item_id).css('height', (parseInt(_this.opts['expectedLines']) * 16) + 'px');
				}
			}
		
			_this.string_interaction();
		}
		
		//multiple text inputs
		if($(qti_item_id).get(0).nodeName.toLowerCase() == 'div') {
			//adapt the fields length
			if(_this.opts['expectedLength']){
				length = parseInt(_this.opts['expectedLength']);
				$(qti_item_id + " :text").css('width', (length * 10) + 'px')
											.attr('maxLength', length);
			}
			//apply the pattern to all fields
			if(_this.opts['patternMask']){
				var pattern = new RegExp("/^"+_this.opts['patternMask']+"$/");
				$(qti_item_id  + " :text").change(function(){
					$(this).removeClass('field-error');
					if(!pattern.test($(this).val())){
						$(this).addClass('field-error');
					}
				});
			}
		}
	};

	/**
	 * Initialize the parametrized behavoir of text input likes widgets 
	 * It supports now the Regex matching and string cloning 
	 * @param {Object} currentObj
	 */
	this.string_interaction = function(){
		
		//add the error class if the value don't match the given pattern
		if(_this.opts['patternMask']){
			var pattern = new RegExp("/^"+_this.opts['patternMask']+"$/");
			$(qti_item_id).change(function(){
				$(this).removeClass('field-error');
				if(!pattern.test($(this).val())){
					$(this).addClass('field-error');
				}
			});
		}
		
		//create a 2nd field to capture the string if the stringIdentifier has been defined
		if(_this.opts['stringIdentifier']){
			$(qti_item_id).after("<input type='hidden' id='"+_this.opts['stringIdentifier']+"' />");
			$("#"+_this.opts['stringIdentifier']).addClass('qti_text_entry_interaction');
			$(qti_item_id).change(function(){
				$("#"+_this.opts['stringIdentifier']).val($(this).val());
			});
		}
	};

	/**
	 * Creates a  hottext widget,
	 * it support 3 behaviors: 
	 * 	- without restriction, 
	 *  - one by one and 
	 *  - N at a time
	 * 
	 */
	this.hottext = function(){
		
		//the hottext behavior depends on the maxChoice value
		var maxChoices = (_this.opts['maxChoices']) ? parseInt(_this.opts['maxChoices']) : 1;
		$(qti_item_id + " .hottext_choice").click(function(){
			
			//no behavior restriction
			if(maxChoices == 0){
				$(this).toggleClass('hottext_choice_on');
				$(this).toggleClass('hottext_choice_off');
			}
			
			//only one selected at a time 
			if(maxChoices == 1){
				$(qti_item_id + " .hottext_choice").removeClass('hottext_choice_on').addClass('hottext_choice_off');
				$(this).removeClass('hottext_choice_off').addClass('hottext_choice_on');
			}
			
			//there is only maxChoices selected at a time
			if(maxChoices > 1){
				if($(qti_item_id + " .hottext_choice_on").length < maxChoices || $(this).hasClass('hottext_choice_on') ){
					$(this).toggleClass('hottext_choice_on');
					$(this).toggleClass('hottext_choice_off');
				}
			}
		});
	};
	
	/**
	 * Creates a  gap match widget			dirname(__FILE__).'/samples/associate.xml',
			dirname(__FILE__).'/samples/choice_multiple.xml',
	 */
	this.gap_match = function(){
		
		//add the container to the words cloud 
		$(qti_item_id + " .qti_choice_list").wrap("<div class='qti_gap_match_container'></div>");
		$(qti_item_id + " .qti_choice_list li").wrapInner("<div></div>");
		
		//add breaker
		$(qti_item_id + " .qti_choice_list li:last").after("<li><div></div></li>");
		$(qti_item_id + " .qti_choice_list li:last").css('clear', 'both');
		
		//manage the cloud height and the words width
		$(qti_item_id+" .qti_choice_list").height(parseFloat($(".qti_gap_match_container").height()));
		var maxBoxSize=0;
		$(qti_item_id+" ul.qti_choice_list li > div").each(function(){	
			if ($(this).width()>maxBoxSize){
				maxBoxSize=$(this).width();		
			}
		});
		maxBoxSize = ((parseInt(maxBoxSize)/2) + 5) + 'px';
		$(qti_item_id+" .gap").css({"padding-left": maxBoxSize, "padding-right": maxBoxSize});
		
		//drag element from words cloud
		$(qti_item_id+" .qti_gap_match_container .qti_choice_list li > div").draggable({
			drag: function(event, ui){
				// label go on top of the others elements
				$(ui.helper).css("z-index","999");			
			},
			containment: qti_item_id,
			revert: true,
			cursor:"move"
		});
		
		/**
		 * remove an element from the filled gap
		 * @param {jQuery} element
		 */
		var removeFilledGap = function(elt){
			var filledId = elt.attr("id").replace('gap_', '');
			var _matchMax = Number(_this.opts["matchMaxes"][filledId]["matchMax"]);
			var _current = Number(_this.opts["matchMaxes"][filledId]["current"]);
			if (_current > 0) {
				_this.opts["matchMaxes"][filledId]["current"] = _current - 1;
			}
			elt.parent().css({
				"padding-left": maxBoxSize, 
				"padding-right": maxBoxSize
			}).removeClass('ui-state-highlight');
			elt.remove();
			if(_current >= _matchMax){
				$("#"+filledId+" div").show();
			}
		};
		
		// pair box are droppable
		$(qti_item_id+" .gap").droppable({
			drop: function(event, ui){
			
				var draggedId = $(ui.draggable).parent().attr("id");
	
				//prevent of re-filling the gap and dragging between the gaps
				if($(this).find("#gap_"+draggedId).length > 0 || /^gap_/.test($(ui.draggable).attr('id'))){
					return false;
				}
	
				//remove the old element
				if($(this).html() != ''){
					$('.filled_gap', $(this)).each(function(){
						removeFilledGap($(this));
					});
				}
				$(this).css({
					"padding-left": '5px', 
					"padding-right": '5px'
				}).addClass('ui-state-highlight');
				
				// add the new element inside the box that received the cloud element
				$(this).html("<span id='gap_"+draggedId+"' class='filled_gap'>"+$(ui.draggable).text()+"</span>");
				
				var _matchMax = Number(_this.opts["matchMaxes"][draggedId]["matchMax"]);
				var _current = Number(_this.opts["matchMaxes"][draggedId]["current"]);
				
				if (_current < _matchMax) {
					_current++;
					_this.opts["matchMaxes"][draggedId]["current"] = _current;
				}
				if(_current >= _matchMax){
					$(ui.draggable).hide();
				}
				
				//enable to drop it back to remove it from the gap
				$(qti_item_id+" .filled_gap").draggable({
					drag: function(event, ui){
						// label go on top of the others elements
						$(ui.helper).css("z-index","999");			
					},
					stop: function(){
						removeFilledGap($(this));
					},
					revert: false,
					containment: qti_item_id,
					cursor:"move"
				});
				
			},
			hoverClass: 'active'
		});	
	};
	
	this.match = function(){
		
		$(qti_item_id + " .choice_list:last").addClass('choice_list_cols');
		var cols = new Array();
		$(qti_item_id + " .choice_list_cols li").each(function(){
			cols.push(this.id);
		});
		
		$(qti_item_id + " .choice_list:first").addClass('choice_list_rows');
		var rows = new Array();
		$(qti_item_id + " .choice_list_rows li").each(function(){
			rows.push(this.id);
		});
		
		$(qti_item_id + " .choice_list_cols").after("<div class='match_node_container'></div>");
		
		$(qti_item_id + " .match_node_container").height(parseInt( $(qti_item_id + " .choice_list:first").height()));
		$(qti_item_id + " .match_node_container").css('left', $(qti_item_id + " .choice_list_rows").width());
		
		var i = 0;
		while(i < rows.length){
			var xnode = 'xnode_' + rows[i];
			var j = 0;
			while(j < cols.length){
				var ynode = 'ynode_' + cols[j];
				var node_id = 'match_node_'+i+'_'+j;
				
				$(qti_item_id + " .match_node_container").append("<div id='"+node_id+"' class='match_node "+xnode+" "+ynode+"'></div>");
				
				left = 0;
				if(j > 0){
					p = $("#"+ 'match_node_'+i+'_'+(j-1)).position();
					left = parseInt(p.left)  + parseInt($("#"+ 'match_node_'+i+'_'+(j-1)).width()) + (10);
				}
				$(qti_item_id + " #"+node_id).css({
					'top' 	: (i * 25) + 'px',
					'left'	: left + 'px',
					'width'	: $("#"+ cols[j]).width()
				});
				j++;
			}
			i++;
		}
		$(qti_item_id + " .match_node").click(function(){
			$(this).toggleClass('tabActive');
		});
		
	};
}


/** 
 * @param {QTIWidget} qtiWidget
 */
function QTIResultCollector(myQTIWidget){

	var myQTIWidget = myQTIWidget;
	var id = myQTIWidget.opts['id'];
	
	// result process
	this.choice = function(){
		var result = {
			"identifier"	: "RESPONSE" // Identifier of the response
			, "cardinality" : myQTIWidget.opts["maxChoices"] << 1 != 2 ? 'multiple' : 'single'
			, "type"		: "identifier"
			, "values"		: []
		};
		
		var userData = new Array();
		$("#" + id + " .tabActive").each(function(){
			userData.push(this.id);
		});
		result.values = userData;
		
		return result;
	};
	
	this.order = function (){
		var result = new Array();
		$("#" + id + " ul.qti_choice_list li").each(function(){
			result.push(this.id);
		});
		return result;
	};

	this.associate = function(){
		var result = new Array();
		$("#" + id + " .qti_association_pair").each(function(){
			result.push([$(this).find('li:first').attr('id'), $(this).find('li:last').attr('id')]);
		});
		return result;
	};

	this.text = function(){
		
		//single mode
		if($("#" + id ).get(0).nodeName.toLowerCase() != 'div'){
			return new Array($("#" + id).val());
		}
		
		//multiple mode
		var result = new Array();
		$("#" + id + " :text").each(function(){
			result.push($(this).val());
		});
		return result;
	};
	this.text_entry = this.text;
	this.extended_text = this.text;

	this.inline_choice = function(){
		return [$("#" + id).val()];
	};

	this.hottext = function(){
		var result = new Array();
		$("#" + id + " .hottext_choice_on").each(function(){
			result.push(this.id.replace("/^hottext_choice_/", ''));
		});
		return result;
	};

	this.gap_match = function(){
		var result = new Array();
		$("#" + id + " .filled_gap").each(function(){
			result.push([$(this).attr('id').replace('gap_', ''), $(this).parent().attr('id')]);
		});
		return result;
	};
	
	this.match = function(){
		var result = new Array();
		$("#" + id + " .tabActive").each(function(){
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
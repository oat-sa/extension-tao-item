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
 * The QTIWidget class enables you to build a QTI widgets 
 * from XHTML elements and the given options
 * 
 * @namespace QTI
 * @class QTIWidget
 * @property {Object} options the interaction of parameters 
 */
var QTIWidget = function(options){
	
	//keep the current instance pointer
	var _this = this;

	/**
	 * To access the widget options 
	 * @type {Object}
	 */
	this.opts = options;

	//the interaction selector, all elements selected must be inside this element,
	// to be able to have some interactions in the same item
	var qti_item_id = "#"+this.opts["id"];
	
	
	/**
	 * the path of that library from an url,
	 * to access images.
	 * @type {String}
	 */
	this.wwwPath = '';
	//use the global variable qti_base_www
	if(typeof(qti_base_www) != 'undefined'){
		this.wwwPath = qti_base_www;
	}
	
	
	/**
	 * Creates a choice list widget
	 * @see QTIWidget#simple_choice
	 * @see QTIWidget#multiple_choice
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
		
		//add the main class 
		$(qti_item_id).addClass('qti_simple_interaction');
		
		//change the class to activate the choice on click
		$(qti_item_id +" ul li").bind("click",function(){	
			$(qti_item_id+" ul li").removeClass("tabActive");		
			$(this).addClass("tabActive");					
		});
		
		//set the current value if defined
		if(_this.opts["values"]){
			var value = _this.opts["values"];
			if(typeof(value) == 'string' && value != ''){
				$(qti_item_id+" ul li#"+value).addClass("tabActive");
			}
		}
	};

	/**
	 * Creates a multiple choice list widget
	 */
	this.multiple_choice = function (){
		
		//add the main class 
		$(qti_item_id).addClass('qti_multi_interaction');
		
		//change the class to activate the choices on click
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
		
		//set the current values if defined
		if(_this.opts["values"]){
			var values = _this.opts["values"];
			if(typeof(values) == 'object' || typeof(values) == 'array'){
				for(i in values){
					var value = values[i];
					if(typeof(value) == 'string' && value != ''){
						$(qti_item_id+" ul li#"+value).addClass("tabActive");
					}
					if(typeof(value) == 'object'){
						if(value.value){
							$(qti_item_id+" ul li#"+value.value).addClass("tabActive");
						}
					}
				}
			}
		}
	};
	
	/**
	 * We use the html <i>select</i> widget,
	 * the function is listed only to keep the same behavior than the other
	 */
	this.inline_choice = function (){
		if(_this.opts["values"]){
			var value = _this.opts["values"];
			if(typeof(value) == 'string' && value != ''){
				$(qti_item_id+" option[value='"+value+"']").attr('selected', true);
			}
		}
	};

	/**
	 * Creates a sortable list widget,
	 * can be horizontal or vertical regarding the orientation parameter
	 */
	this.order = function(){
		
		//if the values are defined
		if(_this.opts["values"]){
			var values = _this.opts["values"];
			if(typeof(values) == 'object' || typeof(values) == 'array'){
				
				//we take the list element corresponding to the given ids 
				var list = new Array();
				for(i in values){
					var value = values[i];
					if(typeof(value) == 'string' && value != ''){
						list.push($(qti_item_id+" ul.qti_choice_list li#"+value));
					}
					if(typeof(value) == 'object'){
						if(value.value){
							list.push($(qti_item_id+" ul.qti_choice_list li#"+value.value));
						}
					}
				}
				
				//and we reorder the elements in the list
				if(list.length == $(qti_item_id+" ul.qti_choice_list li").length && list.length > 0){
					$(qti_item_id+" ul.qti_choice_list").empty();
					for(i in list){
						$(qti_item_id+" ul.qti_choice_list").append(list[i]);
					}
				}
			}
		}
		
		
		var suffixe="";
		
		// test direction
		if(_this.opts.orientation=="horizontal"){
			// horizontal sortable options
			$(qti_item_id+" .qti_choice_list").removeClass("qti_choice_list").addClass("qti_choice_list_horizontal");
				
				var sortableOptions = 
				{
					placeholder: 'sort-placeholder',
					axis : 'x',
					containment: qti_item_id,
					tolerance: 'pointer',
					forcePlaceholderSize: true,
					opacity: 0.8,
					start:function(event,ui){
						$(qti_item_id+" .sort-placeholder").width($("#"+ui.helper[0].id).width()+4);
						$(qti_item_id+" .sort-placeholder").height($("#"+ui.helper[0].id).height()+4);
						$("#"+ui.helper[0].id).css("top","-4px");
					},
					beforeStop:function(event,ui){
						$("#"+ui.helper[0].id).css("top","0");
					}			
				};
				// create suffix for common treatment
				suffixe="_horizontal";	
		} else {
			// verticale sortable options
			var sortableOptions = 
			{
				placeholder: 'sort-placeholder',
				axis : 'y',
				containment: qti_item_id,
				tolerance: 'pointer',
				forcePlaceholderSize: true,
				opacity: 0.8,
				start:function(event,ui){
					$(qti_item_id+" .sort-placeholder").width($("#"+ui.helper[0].id).width()+4);
					$(qti_item_id+" .sort-placeholder").height($("#"+ui.helper[0].id).height()+4);
					$("#"+ui.helper[0].id).css("top","-4px");
				},
				beforeStop:function(event,ui){
					$("#"+ui.helper[0].id).css("top","0");
				}
			};
		}
		//for an horizontal sortable list
		$(qti_item_id).append("<div class='sizeEvaluator'></div>");
		$(qti_item_id+" .sizeEvaluator").css("font-size",$(qti_item_id+" .qti_choice_list_horizontal li").css("font-size"));		
		
		$(qti_item_id+" .qti_choice_list_horizontal li").each(function(){
			$(qti_item_id+" .sizeEvaluator").text($(this).text());			
			var liSize=$(qti_item_id+" .sizeEvaluator").width();	
			
			var liChildren = $(this).children();
			if(liChildren.length > 0){
				$.each(liChildren, function(index, elt){
					liSize += $(elt).width();
				});
			}
			$(this).width(liSize+10);
		});
		$(qti_item_id+" .sizeEvaluator").remove();
		$(qti_item_id+" .qti_choice_list"+suffixe).sortable(sortableOptions);		
		$(qti_item_id+" .qti_choice_list"+suffixe).disableSelection();
		
		// container follow the height dimensions
		$(qti_item_id).append("<div class='breaker'> </div>");
		
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
		var listHeight = parseInt($(qti_item_id+" .qti_associate_container").height());
		var liMaxHeight = 0;
		$(qti_item_id+" .qti_choice_list li > div").each(function(){
			var liHeight = parseInt($(this).height());
			if(liHeight > liMaxHeight){
				liMaxHeight = liHeight;
			}
			if(liHeight > listHeight){
				listHeight = liHeight +10;
			}
		});
		$(qti_item_id+" .qti_associate_container .qti_choice_list").height(listHeight + 10);
		
		// create the pair of box specified in the maxAssociations attribute	
		if(_this.opts["maxAssociations"] > 0) {
			var pairSize = _this.opts["maxAssociations"];
		}
		else{
			var pairSize = parseInt($(qti_item_id+" .qti_choice_list li").length / 2);
		}
		var pairContainer = $("<div class='qti_pair_container'></div>");
		for (var a=pairSize; a>0; a--){
			var currentPairName=_this.opts["id"]+"_"+a;
			pairContainer.append("<ul class='qti_association_pair' id='"+currentPairName+"'><li id='"+currentPairName+"_A"+"'></li><li id='"+currentPairName+"_B"+"'></li></ul>");		
		}
		$(qti_item_id+" .qti_associate_container").after(pairContainer);
		
		// set the size of the drop box to the max size of the cloud words 
		$(qti_item_id+" .qti_association_pair li").width(maxBoxSize+4);
		
		// size the whole pair box to center it
		var pairBoxWidth=0;
		
		$(qti_item_id+" .qti_association_pair:first li").each(function(){
			pairBoxWidth+=$(this).width();
		});
		$(qti_item_id+" .qti_pair_container").css({position:"relative",width: ((pairBoxWidth + 20)*2)+30, margin:"0 auto 0 auto",top:"10px"});	
		$(qti_item_id+" .qti_association_pair").width(pairBoxWidth+90);
		$(qti_item_id+" .qti_association_pair li").height(liMaxHeight);
		
		$.each($(qti_item_id+" .qti_association_pair"), function(index, elt){
			$(elt).after("<div class='qti_link_associate'></div>");
			
			$(qti_item_id+" .qti_link_associate:last").css("top",$(this).position().top + 25);
			$(qti_item_id+" .qti_link_associate:last").css("left", maxBoxSize + 33);
		});

		$(qti_item_id).height( ($(qti_item_id+" .qti_association_pair:last").offset().top - $(qti_item_id).offset().top ) + liMaxHeight); 
		
		//drag element from words cloud
		$(qti_item_id+" .qti_associate_container .qti_choice_list li > div").draggable({
			drag: function(event, ui){
				// label go on top of the others elements
				$(ui.helper).css("z-index","999");			
			},
			containment: qti_item_id,
			cursor:"move",
			revert: true
		});
		
		/**
		 * remove an element from the filled gap
		 * @param {jQuery} jElement
		 */
		var removeFilledPair = function(jElement){
			var filledId = jElement.attr("id").replace('pair_', '');
			var _matchMax = Number(_this.opts["matchMaxes"][filledId]["matchMax"]);
			var _current = Number(_this.opts["matchMaxes"][filledId]["current"]);
			
			if (_current > 0) {
				_this.opts["matchMaxes"][filledId]["current"] = _current - 1;
			}
			jElement.parents('li').removeClass('ui-state-highlight');
			jElement.remove();
			if(_current >= _matchMax){
				$("#"+filledId+" div").show();
			}
		};
		
		/**
		 * Fill a pair gap by a cloud element
		 * @param  {jQuery} jDropped
		 * @param  {jQuery} jDragged
		 */
		var fillPair = function(jDropped, jDragged){
			// add class to highlight current dropped item in pair boxes
			jDropped.addClass('ui-state-highlight');
			
			var draggedId = jDragged.parents().attr('id');
			
			// add new element inside the box that received the cloud element
			jDropped.html("<div id='pair_"+draggedId+"' class='filled_pair'>"+jDragged.html()+"</div>");
			
			// give a size to the dropped item to overlapp perfectly the pair box
			$(qti_item_id+" #pair_"+draggedId).width($(qti_item_id+" .qti_association_pair li").width());
			$(qti_item_id+" #pair_"+draggedId).height($(qti_item_id+" .qti_association_pair li").height());
			
			var _matchMax 	= Number(_this.opts["matchMaxes"][draggedId]["matchMax"]);
			var _current 	= Number(_this.opts["matchMaxes"][draggedId]["current"]);
			
			if (_current < _matchMax) {
				_current++;
				_this.opts["matchMaxes"][draggedId]["current"]=_current;
			}
			if (_current >= _matchMax) {
				jDragged.hide();
			}
			
			// give this new element the ability to be dragged
			$(qti_item_id+" #pair_"+draggedId).draggable({
				drag: function(event, ui){
					// element is on top of the other when it's dragged
					$(this).css("z-index", "999");
				},
				stop: function(event, ui) {
					removeFilledPair($(this));
					return true;
				 },
				 containment: qti_item_id,
				 cursor:"move"
			});
		};
		
		// pair box are droppable
		$(qti_item_id+" .qti_association_pair li").droppable({
			drop: function(event, ui){
				
				var draggedId = $(ui.draggable).parents().attr('id');
				
				//prevent of re-filling the gap and dragging between the gaps
				if($(this).find("#pair_"+draggedId).length > 0 || /^pair_/.test($(ui.draggable).attr('id'))){
					return false;
				}
				
				var _matchGroup = _this.opts["matchMaxes"][draggedId]["matchGroup"];
				
				//Check the matchGroup of the dropped item or the opposite in the pair 
				if(/A$/.test($(this).attr('id'))){
					 var opposite =$('#' + $(this).attr('id').replace(/_A$/, "_B")).find('.filled_pair:first');
				}
				else{
					var opposite = $('#' + $(this).attr('id').replace(/_B$/, "_A")).find('.filled_pair:first');
				}
				if(opposite.length > 0){
					var oppositeId = opposite.attr('id').replace('pair_', '');
					if(_matchGroup.length > 0){ 
						if($.inArray(oppositeId, _matchGroup) < 0){
							$(this).effect("highlight", {color:'#B02222'}, 2000);
							return false;
						}
					}
					
					var _oppositeMatchGroup = _this.opts["matchMaxes"][oppositeId]["matchGroup"];
					if(_oppositeMatchGroup.length > 0){
						if($.inArray(draggedId, _oppositeMatchGroup) < 0){
							$(this).effect("highlight", {color:'#B02222'}, 2000);
							return false;
						}
					}
				}
				

				//remove the old element
				if($(this).html() != ''){
					$('.filled_pair', $(this)).each(function(){
						removeFilledPair($(this));
					});
				}
				$(ui.helper).css({top:"0",left:"0"});
				
				//fill the gap
				fillPair($(this), $(ui.draggable));
			},
			hoverClass: 'active'
		});
		
		//if the values are defined
		if(_this.opts["values"]){
			var index = 1;
			var values = _this.opts["values"];
			if(typeof(values) == 'object' || typeof(values) == 'array'){
				for(i in values){
					var pair = values[i];
					var valA, valB;
					if(pair.value){
						if(pair.value[0].value){
							valA = pair.value[0].value;
							valB = pair.value[1].value;
						}
						else{
							valA = pair.value[0];
							valB = pair.value[1];
						}
					}
					else{
						valA = pair[0];
						valB = pair[1];
					}
					
					if(typeof(valA) == 'string' && valA != ''){
						if($(qti_item_id+" li#"+valA).length == 1){
							fillPair($(qti_item_id+"_"+ index+"_A"), $(qti_item_id+" .qti_associate_container .qti_choice_list li#"+valA+" > div"));
						}
					}
					if(typeof(valB) == 'string' && valB != ''){
						if($(qti_item_id+" li#"+valB).length == 1){
							fillPair($(qti_item_id+"_"+ index+"_B"), $(qti_item_id+" .qti_associate_container .qti_choice_list li#"+valB+" > div"));
						}
					}
					index++;
				}
			}
		}
	};

	/**
	 * Creates a text entry widget
	 * 
	 * @see QTIWidget#string_interaction
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
	 * 
	 * @see QTIWidget#string_interaction
	 */
	this.extended_text = function (){
		
			
		//usual case: one textarea 
		if($(qti_item_id).get(0).nodeName.toLowerCase() == 'textarea') {
			
			//adapt the field length
			if(_this.opts['expectedLength'] || _this.opts_this.opts['expectedLines']){
				
				baseWidth 	= parseInt($(qti_item_id).css('width')) | 400;
				baseHeight 	= parseInt($(qti_item_id).css('height')) | 100;
				if(_this.opts['expectedLength']){
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
			//set the current values if defined
			if(_this.opts["values"]){
				var values = _this.opts["values"];
				if(typeof(values) == 'object' || typeof(values) == 'array'){
					for(i in values){
						var value = values[i];
						if(typeof(value) == 'string' && value != ''){
							$(qti_item_id+" :text#" + qti_item_id+ "_"+ i).val(value);
						}
						if(typeof(value) == 'object'){
							if(value.value){
								$(qti_item_id+" :text#" + qti_item_id+ "_"+ i).val(value.value);
							}
						}
					}
				}
			}
		}
	};

	/**
	 * Initialize the parametrized behavoir of text input likes widgets 
	 * It supports now the Regex matching and string cloning 
	 */
	this.string_interaction = function(){
		
		//add the error class if the value don't match the given pattern
		if(_this.opts['patternMask']){
			var pattern = new RegExp("^"+_this.opts['patternMask']+"$");
			
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
		
		if(_this.opts["values"]){
			var value = _this.opts["values"];
			if(typeof(value) == 'string' && value != ''){
				$(qti_item_id).val(value);
			}
		}
	};

	/**
	 * Creates a  hottext widget,
	 * it support 3 behaviors: 
	 * 	- without restriction, 
	 *  - one by one and 
	 *  - N at a time
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
		//set the current values if defined
		if(_this.opts["values"]){
			var values = _this.opts["values"];
			if(typeof(values) == 'string' && values != ''){
				$(qti_item_id + " #hottext_choice_"+values).switchClass('hottext_choice_off', 'hottext_choice_on');
			}
			if(typeof(values) == 'object' || typeof(values) == 'array'){
				for(i in values){
					var value = values[i];
					if(typeof(value) == 'string' && value != ''){
						$(qti_item_id + " #hottext_choice_"+value).switchClass('hottext_choice_off', 'hottext_choice_on');
					}
					if(typeof(value) == 'object'){
						if(value.value){
							$(qti_item_id + " #hottext_choice_"+value.value).switchClass('hottext_choice_off', 'hottext_choice_on');
						}
					}
				}
			}
		}
	};
	
	/**
	 * Creates a  gap match widget			
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
		
		$(qti_item_id+" .gap").html('&nbsp;');
		
		/**
		 * Fill a gap with an element of the word's cloud
		 * @param {jQuery} jDropped
		 * @param {jQuery} jDragged
		 */
		var fillGap = function(jDropped, jDragged){
			
			var draggedId = jDragged.parent().attr("id");
			var _matchMax = Number(_this.opts["matchMaxes"][draggedId]["matchMax"]);
			var _current 	= Number(_this.opts["matchMaxes"][draggedId]["current"]);
			
			jDropped.css({'padding-left' : '5px', 'padding-right' : '5px'}).addClass('dropped_gap');
			
			// add the new element inside the box that received the cloud element
			jDropped.html("<span id='gap_"+draggedId+"' class='filled_gap'>"+jDragged.text()+"</span>");
			
			if (_current < _matchMax) {
				_current++;
				_this.opts["matchMaxes"][draggedId]["current"] = _current;
			}
			if(_current >= _matchMax){
				jDragged.hide();
			}
			
			//enable to drop it back to remove it from the gap
			$(qti_item_id+" .filled_gap").draggable({
				drag: function(event, ui){
					// label go on top of the others elements
					$(ui.helper).css("z-index","999");
					$(this).parent().addClass('ui-state-highlight');
				},
				stop: function(){
					$(this).parent().removeClass('ui-state-highlight');
					removeFilledGap($(this));
				},
				revert: false,
				containment: qti_item_id,
				cursor:"move"
			});
		};
		
		/**
		 * remove an element from the filled gap
		 * @param {jQuery} jElement
		 */
		var removeFilledGap = function(jElement){
			var filledId = jElement.attr("id").replace('gap_', '');
			var _matchMax = Number(_this.opts["matchMaxes"][filledId]["matchMax"]);
			var _current = Number(_this.opts["matchMaxes"][filledId]["current"]);
			if (_current > 0) {
				_this.opts["matchMaxes"][filledId]["current"] = _current - 1;
			}
			jElement.parent().css({
				"padding-left": maxBoxSize, 
				"padding-right": maxBoxSize
			}).removeClass('dropped_gap').html('&nbsp;');
			jElement.remove();
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
				
				var _matchGroup = _this.opts["matchMaxes"][draggedId]["matchGroup"];
				
				///if the matchGroup of the choice is defined and not found we cancel the drop 
				if(_matchGroup.length > 0){
					if($.inArray($(this).attr('id'), _matchGroup) < 0){
						$(this).effect("highlight", {color:'#B02222'}, 2000);
						return false;
					}
				}
				
				//check too the matchGroup of the gap
				var _gapMatchGroup = _this.opts["matchMaxes"][$(this).attr('id')]["matchGroup"];
				if(_gapMatchGroup.length > 0){
					if($.inArray(draggedId, _gapMatchGroup) < 0){
						$(this).effect("highlight", {color:'#B02222'}, 2000);
						return false;
					}
				}

				//remove the old element
				if($(this).html() != ''){
					$('.filled_gap', $(this)).each(function(){
						removeFilledGap($(this));
					});
				}
				
				//fill the gap
				fillGap($(this), $(ui.draggable));
				
			},
			hoverClass: 'active'
		});	
		
		//if the values are defined
		if(_this.opts["values"]){
			var values = _this.opts["values"];
			if(typeof(values) == 'object' || typeof(values) == 'array'){
				for (i in values){
					var value = values[i];
					if(value.value){
						value = value.value;
					}
					if(values[i]['count'] == 2){
						var gap = value['0']['value'];
						var choice = value['1']['value'];
						fillGap($(qti_item_id+" .gap#"+gap), $(qti_item_id+" .qti_choice_list li#"+choice+" > div"));
					}
				}
			}
		}
	};
	
	/**
	 * Create a match widget: 
	 * a matrix of choices to map to each others
	 */
	this.match = function(){
		
		//define the columns of the matrix from the last choice list
		$(qti_item_id + " .choice_list:last").addClass('choice_list_cols');
		var cols = new Array();
		$(qti_item_id + " .choice_list_cols li").each(function(){
			cols.push(this.id);
		});
		
		
		//define the rows of the matrix from the first choice list
		$(qti_item_id + " .choice_list:first").addClass('choice_list_rows');
		var rows = new Array();
		$(qti_item_id + " .choice_list_rows li").each(function(){
			rows.push(this.id);
		});
		
		//insert the node container (it will contain the nodes of the matrix)
		$(qti_item_id + " .choice_list_cols").after("<div class='match_node_container'></div>");
		
		//make the display adjustment
		$(qti_item_id + " .match_node_container").height(parseInt( $(qti_item_id + " .choice_list_rows").height()));
		$(qti_item_id + " .match_node_container").css({
			'left': $(qti_item_id + " .choice_list_rows").width()
		});
		
		var maxHeight = 25;
		$(qti_item_id + " .choice_list_cols li").each(function(){
			var height = parseInt($(this).height());
			if(height > maxHeight){
				maxHeight = height;
			}
		});
		$(qti_item_id + " .choice_list_cols li").each(function(){
			var li = $(this);
			li.css('width', li.width());
			var myDiv = $("<div />").css({
				'width'		: li.width(),
				'height'	: maxHeight +'px',
				'border'	: li.css('border')
			});
			li.css('height', '25px');
			$(this).wrapInner(myDiv);
		});
		$(qti_item_id + " .prompt").css('margin-bottom', (maxHeight - 20) + 'px');
		
		//build all the nodes
		var i = 0;
		var currentHeight = 0;
		while(i < rows.length){
			var xnode = 'xnode_' + rows[i];
			var rowHeight = parseFloat($("#"+ rows[i]).height());
			
			var j = 0;
			while(j < cols.length){
				var ynode = 'ynode_' + cols[j];
				var node_id = 'match_node_'+i+'_'+j;
				
				//a node is a DIV with a ID made from X and Y indexes, and the classes of it's row and column
				$(qti_item_id + " .match_node_container").append("<div id='"+node_id+"' class='match_node "+xnode+" "+ynode+"'>&nbsp;</div>");
				
				//set the position and the size of the node
				left = 0;
				if(j > 0){
					p = $("#"+ 'match_node_'+i+'_'+(j-1)).position();
					left = parseInt(p.left)  + parseInt($("#"+ 'match_node_'+i+'_'+(j-1)).width()) + (12);
				}
				var colWidth  = parseFloat($("#"+ cols[j]).width());
				$(qti_item_id + " #"+node_id).css({
					'top' 	: ((currentHeight) + (i * 2) ) + 'px',
					'left'	: left + 'px',
					'width'	: colWidth + 'px',
					'height': rowHeight + 'px'
				});
				j++;
			}
			currentHeight += rowHeight;
			i++;
		}
		
		/**
		 * Exract the id from the rows and the columns from the node's classes
		 * 
		 * @param {jQuery} jElement the matrix node under the jQuery format
		 * @returns {Object} with xnode an ynode id 
		 */
		function getNodeXY(jElement){
			
			var x = null;
			var y = null;
			
			var classes = jElement.attr('class').split(' ');
			for(i in classes){
				if(/^xnode_/.test(classes[i])){
					x = {
						'id' 	: classes[i].replace('xnode_', ''),
						'class'	: classes[i]
					};
				}
				else if(/^ynode_/.test(classes[i])){
					y = {
							'id' 	: classes[i].replace('ynode_', ''),
							'class'	: classes[i]
						};
				}
				if(x != null && y != null){
					break;
				}
			}
			return {xnode: x, ynode: y};
		}
		
		/**
		 * Deactivate a node
		 * 
		 * @param {jQuery} jElement the matrix node under the jQuery format
		 */
		function deactivateNode(jElement){
			jElement.removeClass('tabActive');
			associations.splice(associations.indexOf(jElement.attr('id')), 1);
		}
		
		var maxAssociations = _this.opts['maxAssociations'];
		var associations = new Array();
		
		/**
		 * Activate / deactivate nodes regarding:
		 * 	- the maxAssociations options that should'nt be exceeded
		 *  - the matchMax option of the row and the column
		 *  - the matchGroup option defining who can be associated with who
		 * @param {jQuery} jElement
		 */
		var selectNode = function(jElement){
			
			if(jElement.hasClass('tabActive')){
				deactivateNode(jElement);
			}
			else{
				if(associations.length < maxAssociations || maxAssociations == 0){
					
					var nodeXY = getNodeXY(jElement);
					
					//check the matchGroup for the current association
					var _rowMatchGroup = _this.opts["matchMaxes"][nodeXY.xnode.id]['matchGroup'];
					if(_rowMatchGroup.length > 0){
						if($.inArray(nodeXY.ynode.id, _rowMatchGroup) < 0){
							$(this).effect("highlight", {color:'#B02222'}, 1000);
							return false;
						}
					}
					var _colMatchGroup = _this.opts["matchMaxes"][nodeXY.ynode.id]['matchGroup'];
					if(_colMatchGroup.length > 0){
						if($.inArray(nodeXY.xnode.id, _colMatchGroup) < 0){
							$(this).effect("highlight", {color:'#B02222'}, 1000);
							return false;
						}
					}
					
					//test the matchMax of the row choice
					var rowMatch = _this.opts["matchMaxes"][nodeXY.xnode.id]['matchMax'];
					if(rowMatch == 1) {
						$(qti_item_id + " ." + nodeXY.xnode['class']).each(function(){
							deactivateNode($(this));
						});
					}
					else if(rowMatch > 1){
						var rowMatched = $(qti_item_id + " ." + nodeXY.xnode['class'] + ".tabActive").length;
						if(rowMatched >= rowMatch){
							$(this).effect("highlight", {color:'#B02222'}, 1000);
							return false;
						}
					}
					
					//test the matchMax of the column choice
					var colMatch = _this.opts["matchMaxes"][nodeXY.ynode.id]['matchMax'];
					if(colMatch == 1) {
						$("." + nodeXY.ynode['class']).each(function(){
							deactivateNode($(this));
						});
					}
					else if(colMatch > 1){
						var colMatched = $(qti_item_id + " ." + nodeXY.ynode['class']+ ".tabActive").length;
						if(colMatched >= colMatch){
							$(this).effect("highlight", {color:'#B02222'}, 1000);
							return false;
						}
					}
					
					jElement.addClass('tabActive');
					associations.push(jElement.attr('id'));
				}
			}
		};
		
		//match node on click
		$(qti_item_id + " .match_node").click(function(){
			selectNode($(this));
		});
		
		
		//if the values are defined
		if(_this.opts["values"]){
			var values = _this.opts["values"];
			if(typeof(values) == 'object' || typeof(values) == 'array'){
				for (i in values){
					var value = values[i];
					if(value.value){
						value = value.value;
					}
					if(values[i]['count'] == 2){
						var row = value['0']['value'];
						var col = value['1']['value'];
						selectNode($(qti_item_id + " .match_node.xnode_"+row+".ynode_"+col));
					}
				}
			}
		}
	};
	
	
	/**
	 * Creates a clickable image with hotspots
	 */
	this.hotspot = function (){
		
		var hotSpotDebug=false;

		//if the values are defined
		var currentValues = [];
		if(_this.opts["values"]){
			var values = _this.opts["values"];
			if(typeof(values) == 'object' || typeof(values) == 'array'){
				for (i in values){
					var value = values[i];
					if(value.value){
						value = value.value;
					}
					currentValues.push(value);
				}
			}
			if(typeof(values) == 'string'){
				currentValues.push(values);
			}
		}
		
		var maxChoices=_this.opts["maxChoices"];
		var countChoices=0;
		// offset position
		$(qti_item_id+" .qti_hotspot_spotlist li").css("display","none");
		var itemHeight=$(qti_item_id).height();
		$(qti_item_id).css("height",itemHeight+options.imageHeight);
		
		// load image in rapheal area
		var paper=Raphael($(qti_item_id+" .qti_hotspot_spotlist")[0],0,0);
		paper.image(options.imagePath,0,0,options.imageWidth,options.imageHeight);
		// create hotspot
		$(qti_item_id+" .qti_hotspot_spotlist li").each(function(){
			var currentHotSpotShape=_this.opts.hotspotChoice[$(this).attr("id")]["shape"];
			var currentHotSpotCoords=_this.opts.hotspotChoice[$(this).attr("id")]["coords"].split(",");		
			// create pointer to validate interaction
			// map QTI shape to Raphael shape
			// Depending the shape, options may vary
			switch(currentHotSpotShape){
				case "circle":				
					var currentHotSpotX=Number(currentHotSpotCoords[0]);
					var currentHotSpotY=Number(currentHotSpotCoords[1]);
					var currentHotSpotSize=currentHotSpotCoords[2];
					var currentShape=paper[currentHotSpotShape](currentHotSpotX,currentHotSpotY,currentHotSpotSize);			
					if (hotSpotDebug) currentShape.attr("stroke-width", "3px");
					var shapeWidth=currentShape.getBBox().width;
					var shapeHeight=currentShape.getBBox().height;
					var pointerWidth=10;
					var pointerHeight=10;
					var pointer = paper.image(_this.wwwPath + "img/cross.png", currentHotSpotX-(pointerWidth/2), currentHotSpotY-(pointerHeight/2), pointerWidth, pointerHeight);
					break;
				case "rect":
					var currentHotSpotTopX=Number(currentHotSpotCoords[0]);
					var currentHotSpotTopY=Number(currentHotSpotCoords[1]);
					var currentHotSpotBottomX=currentHotSpotCoords[2]-currentHotSpotTopX;
					var currentHotSpotBottomY=currentHotSpotCoords[3]-currentHotSpotTopY;
					var currentShape=paper[currentHotSpotShape](currentHotSpotTopX,currentHotSpotTopY,currentHotSpotBottomX,currentHotSpotBottomY);
					if (hotSpotDebug) currentShape.attr("stroke-width", "3px");
					var shapeWidth=currentShape.getBBox().width;
					var shapeHeight=currentShape.getBBox().height;
					var pointerWidth=10;
					var pointerHeight=10;
					var pointer = paper.image(_this.wwwPath + "img/cross.png", currentHotSpotTopX+(shapeWidth/2)-(pointerWidth/2), currentHotSpotTopY+(shapeHeight/2)-(pointerHeight/2), pointerWidth, pointerHeight);				
					break;	
				case "ellipse":
					var currentHotSpotX=Number(currentHotSpotCoords[0]);
					var currentHotSpotY=Number(currentHotSpotCoords[1]);
					var currentHotSpotHradius=currentHotSpotCoords[2];
					var currentHotSpotVradius=currentHotSpotCoords[3];
					var currentShape=paper[currentHotSpotShape](currentHotSpotX,currentHotSpotY,currentHotSpotHradius,currentHotSpotVradius);	
					if (hotSpotDebug) currentShape.attr("stroke-width", "3px");			
					var pointerWidth=10;
					var pointerHeight=10;
					var pointer = paper.image(_this.wwwPath + "img/cross.png", currentHotSpotX-(pointerWidth/2), currentHotSpotY-(pointerHeight/2), pointerWidth, pointerHeight);				
					break;
				case "poly":
					var polyCoords=polyCoordonates(_this.opts.hotspotChoice[$(this).attr("id")]["coords"]);
					var currentShape=paper["path"](polyCoords);			
					if (hotSpotDebug) currentShape.attr("stroke-width", "3px");			
					var shapeWidth=currentShape.getBBox().width;
					var shapeHeight=currentShape.getBBox().height;
				 	var pointerCoordonates=pointerPolyCoordonates(_this.opts.hotspotChoice[$(this).attr("id")]["coords"]);
					var currentHotSpotTopX=Number(pointerCoordonates[0]);
					var currentHotSpotTopY=Number(pointerCoordonates[1]);
					var pointerWidth=10;
					var pointerHeight=10;
					var pointer = paper.image(_this.wwwPath + "img/cross.png", currentHotSpotTopX+(shapeWidth/2)-(pointerWidth/2), 	currentHotSpotTopY+(shapeHeight/2)-(pointerHeight/2), pointerWidth, pointerHeight);				
				break;
			}
			pointer.attr("opacity","0");
			currentShape.toFront();	
			currentShape.attr("fill", "pink");
			currentShape.attr("fill-opacity", "0");
			currentShape.attr("stroke-opacity", "0");
			if (hotSpotDebug) currentShape.attr("stroke-opacity", "1");
			currentShape.attr("stroke", "blue");	
			// add a reference to newly created object
			_this.opts[currentShape.node]=$(this).attr("id");
			$(currentShape.node).bind("mousedown",{	
				zis:currentShape,
				name: $(this).attr("id"), 
				raphElement:currentShape			
			},function(e){				
				var node = $(qti_item_id+" #"+e.data.name);
				if(node.hasClass('activated')){
					countChoices-=1;
					pointer.attr("opacity","0");
					node.removeClass('activated');
				}
				else{
					if (countChoices>=maxChoices){
						return;
					}						
					countChoices+=1;
					
					pointer.attr("opacity","1");
					paper.safari();
					node.addClass('activated');
				}
			});
			//trigger the event on load if the value is set
			if($.inArray($(this).attr("id"), currentValues) > -1){
				$(currentShape.node).trigger("mousedown", {	
					zis:currentShape,
					name: $(this).attr("id"), 
					raphElement:currentShape			
				});
			}
			
		});
	};
	
	/**
	 * Creates a clickable image with hotspots
	 */
	this.select_point = function (){
		
		var maxChoices=_this.opts["maxChoices"];
		var countChoices=0;
		var nameId=0;
		
		// offset position
		$(qti_item_id+" .qti_select_point_interaction_list li").css("display","none");
		var itemHeight=$(qti_item_id).height();
		$(qti_item_id).css("height",itemHeight+options.imageHeight);
		
		
		// load image in rapheal area
		$(qti_item_id+" .qti_select_point_interaction_container").css({"background":"url("+options.image+")", width:options.imageWidth, height:options.imageHeight});
		
		$(qti_item_id+" .qti_select_point_interaction_container").bind("click",function(e){
			
			var relativeXmouse = e.pageX-5;
			var relativeYmouse = e.pageY-5;
			
			if(countChoices>=maxChoices){
				return;
			}
			
			countChoices++;

			$(this)
				.append("<img src='"+this.wwwPath+"img/cross.png' alt='cross' class='select_point_cross' />")
				.find("img:last")
				.css({position:"absolute",top:relativeYmouse, left:relativeXmouse})
				.data('coords', relativeXmouse+','+relativeYmouse)
				.bind("click",function(e){
					countChoices--;
					$(this).remove();
					return false;
				});
		});
	};
};
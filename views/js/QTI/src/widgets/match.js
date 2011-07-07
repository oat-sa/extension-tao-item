/**
 * Match widgets: gap match, graphic gap match and match QTI's interactions
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * 
 * @requires jquery {@link http://www.jquery.com}
 * @requries raphael {@link http://raphaeljs.com/}
 */

/**
 * @namespace QTIWidget
 */
var QTIWidget = QTIWidget || {};


//
//GAP MATCH
//

/**
 * Creates a  gap match widget:
 * A text where you place some words into gaps
 * @methodOf QTIWidget		
 * @param {Object} ctx the QTIWidget context
 */
QTIWidget.gap_match = function(ctx){
	
	//add the container to the words cloud 
	$(ctx.qti_item_id + " .qti_choice_list").wrap("<div class='qti_gap_match_container'></div>");
	$(ctx.qti_item_id + " .qti_choice_list li").wrapInner("<div></div>");
	
	//add breaker
	$(ctx.qti_item_id + " .qti_choice_list li:last").after("<li><div></div></li>");
	$(ctx.qti_item_id + " .qti_choice_list li:last").css('clear', 'both');
	
	//manage the cloud height and the words width
	$(ctx.qti_item_id+" .qti_choice_list").height(parseFloat($(".qti_gap_match_container").height()));
	var maxBoxSize=0;
	$(ctx.qti_item_id+" ul.qti_choice_list li > div").each(function(){	
		if ($(this).width()>maxBoxSize){
			maxBoxSize=$(this).width();		
		}
	});
	maxBoxSize = ((parseInt(maxBoxSize)/2) + 5) + 'px';
	$(ctx.qti_item_id+" .gap").css({"padding-left": maxBoxSize, "padding-right": maxBoxSize});
	
	//drag element from words cloud
	$(ctx.qti_item_id+" .qti_gap_match_container .qti_choice_list li > div").draggable({
		drag: function(event, ui){
			// label go on top of the others elements
			$(ui.helper).css("z-index","999");			
		},
		containment: ctx.qti_item_id,
		revert: true,
		cursor:"move"
	});
	
	$(ctx.qti_item_id+" .gap").html('&nbsp;');
	
	/**
	 * Fill a gap with an element of the word's cloud
	 * @param {jQuery} jDropped
	 * @param {jQuery} jDragged
	 */
	var fillGap = function(jDropped, jDragged){
		
		var draggedId = jDragged.parent().attr("id");
		var _matchMax = Number(ctx.opts["matchMaxes"][draggedId]["matchMax"]);
		var _current 	= Number(ctx.opts["matchMaxes"][draggedId]["current"]);
		
		jDropped.css({'padding-left' : '5px', 'padding-right' : '5px'}).addClass('dropped_gap');
		
		// add the new element inside the box that received the cloud element
		jDropped.html("<span id='gap_"+draggedId+"' class='filled_gap'>"+jDragged.text()+"</span>");
		
		if (_current < _matchMax) {
			_current++;
			ctx.opts["matchMaxes"][draggedId]["current"] = _current;
		}
		if(_current >= _matchMax){
			jDragged.hide();
		}
		
		//enable to drop it back to remove it from the gap
		$(ctx.qti_item_id+" .filled_gap").draggable({
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
			containment: ctx.qti_item_id,
			cursor:"move"
		});
	};
	
	/**
	 * remove an element from the filled gap
	 * @param {jQuery} jElement
	 */
	var removeFilledGap = function(jElement){
		var filledId = jElement.attr("id").replace('gap_', '');
		var _matchMax = Number(ctx.opts["matchMaxes"][filledId]["matchMax"]);
		var _current = Number(ctx.opts["matchMaxes"][filledId]["current"]);
		if (_current > 0) {
			ctx.opts["matchMaxes"][filledId]["current"] = _current - 1;
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
	$(ctx.qti_item_id+" .gap").droppable({
		drop: function(event, ui){
		
			var draggedId = $(ui.draggable).parent().attr("id");

			//prevent of re-filling the gap and dragging between the gaps
			if($(this).find("#gap_"+draggedId).length > 0 || /^gap_/.test($(ui.draggable).attr('id'))){
				return false;
			}
			
			var _matchGroup = ctx.opts["matchMaxes"][draggedId]["matchGroup"];
			
			///if the matchGroup of the choice is defined and not found we cancel the drop 
			if(_matchGroup.length > 0){
				if($.inArray($(this).attr('id'), _matchGroup) < 0){
					$(this).effect("highlight", {color:'#B02222'}, 1000);
					return false;
				}
			}
			
			//check too the matchGroup of the gap
			var _gapMatchGroup = ctx.opts["matchMaxes"][$(this).attr('id')]["matchGroup"];
			if(_gapMatchGroup.length > 0){
				if($.inArray(draggedId, _gapMatchGroup) < 0){
					$(this).effect("highlight", {color:'#B02222'}, 1000);
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
	if(ctx.opts["values"]){
		var values = ctx.opts["values"];
		if(typeof(values) == 'object'){
			for (i in values){
				var value = values[i];
				if( value['0'] && value['1']){
					var gap = value['0'];
					var choice = value['1'];
					fillGap($(ctx.qti_item_id+" .gap#"+gap), $(ctx.qti_item_id+" .qti_choice_list li#"+choice+" > div"));
				}
			}
		}
	}
};

//
//MATCH
//

/**
 * Create a match widget: 
 * a matrix of choices to map to each others
 * @methodOf QTIWidget
 * @param {Object} ctx the QTIWidget context
 */
QTIWidget.match = function(ctx){
	
	//define the columns of the matrix from the last choice list
	$(ctx.qti_item_id + " .choice_list:last").addClass('choice_list_cols');
	var cols = new Array();
	$(ctx.qti_item_id + " .choice_list_cols li").each(function(){
		cols.push(this.id);
	});
	
	
	//define the rows of the matrix from the first choice list
	$(ctx.qti_item_id + " .choice_list:first").addClass('choice_list_rows');
	var rows = new Array();
	$(ctx.qti_item_id + " .choice_list_rows li").each(function(){
		rows.push(this.id);
	});
	
	//insert the node container (it will contain the nodes of the matrix)
	$(ctx.qti_item_id + " .choice_list_cols").after("<div class='match_node_container'></div>");
	
	//make the display adjustment
	$(ctx.qti_item_id + " .match_node_container").height(parseInt( $(ctx.qti_item_id + " .choice_list_rows").height()));
	$(ctx.qti_item_id + " .match_node_container").css({
		'left': $(ctx.qti_item_id + " .choice_list_rows").width()
	});
	
	var maxHeight = 25;
	$(ctx.qti_item_id + " .choice_list_cols li").each(function(){
		var height = parseInt($(this).height());
		if(height > maxHeight){
			maxHeight = height;
		}
	});
	$(ctx.qti_item_id + " .choice_list_cols li").each(function(){
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
	$(ctx.qti_item_id + " .prompt").css('margin-bottom', (maxHeight - 20) + 'px');
	
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
			$(ctx.qti_item_id + " .match_node_container").append("<div id='"+node_id+"' class='match_node "+xnode+" "+ynode+"'>&nbsp;</div>");
			
			//set the position and the size of the node
			left = 0;
			if(j > 0){
				p = $("#"+ 'match_node_'+i+'_'+(j-1)).position();
				left = parseInt(p.left)  + parseInt($("#"+ 'match_node_'+i+'_'+(j-1)).width()) + (12);
			}
			var colWidth  = parseFloat($("#"+ cols[j]).width());
			$(ctx.qti_item_id + " #"+node_id).css({
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
	 * @methodOf QTIWidget
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
	 * @methodOf QTIWidget
	 */
	function deactivateNode(jElement){
		jElement.removeClass('tabActive');
                
                //note: Array.indexOf() is not implemented in IE:
                for(var i=0; i<associations.length; i++){
                    if(associations[i] == jElement.attr('id')){
                        associations.splice(i, 1);
                    }
                }
	}
	
	var maxAssociations = ctx.opts['maxAssociations'];
	var associations = new Array();
	
	/**
	 * Activate / deactivate nodes regarding:
	 *  - the maxAssociations options that should'nt be exceeded
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
				var _rowMatchGroup = ctx.opts["matchMaxes"][nodeXY.xnode.id]['matchGroup'];
				if(_rowMatchGroup.length > 0){
					if($.inArray(nodeXY.ynode.id, _rowMatchGroup) < 0){
						jElement.effect("highlight", {color:'#B02222'}, 1000);
						return false;
					}
				}
				var _colMatchGroup = ctx.opts["matchMaxes"][nodeXY.ynode.id]['matchGroup'];
				if(_colMatchGroup.length > 0){
					if($.inArray(nodeXY.xnode.id, _colMatchGroup) < 0){
						jElement.effect("highlight", {color:'#B02222'}, 1000);
						return false;
					}
				}
				
				//test the matchMax of the row choice
				var rowMatch = ctx.opts["matchMaxes"][nodeXY.xnode.id]['matchMax'];
				if(rowMatch == 1) {
					$(ctx.qti_item_id + " ." + nodeXY.xnode['class']).each(function(){
						deactivateNode($(this));
					});
				}
				else if(rowMatch > 1){
					var rowMatched = $(ctx.qti_item_id + " ." + nodeXY.xnode['class'] + ".tabActive").length;
					if(rowMatched >= rowMatch){
						jElement.effect("highlight", {color:'#B02222'}, 1000);
						return false;
					}
				}
				
				//test the matchMax of the column choice
				var colMatch = ctx.opts["matchMaxes"][nodeXY.ynode.id]['matchMax'];
				if(colMatch == 1) {
					$("." + nodeXY.ynode['class']).each(function(){
						deactivateNode($(this));
					});
				}
				else if(colMatch > 1){
					var colMatched = $(ctx.qti_item_id + " ." + nodeXY.ynode['class']+ ".tabActive").length;
					if(colMatched >= colMatch){
						jElement.effect("highlight", {color:'#B02222'}, 1000);
                                                
						return false;
					}
				}
				
				jElement.addClass('tabActive');
				associations.push(jElement.attr('id'));
			}
		}
	};
	
	//match node on click
	$(ctx.qti_item_id + " .match_node").click(function(){
		selectNode($(this));
	});
	
	
	//if the values are defined
	if(ctx.opts["values"]){
		var values = ctx.opts["values"];
		if(typeof(values) == 'object'){
			for (i in values){
				var value = values[i];
				if (value['0'] && value['1']){
					var row = value['0'];
					var col = value['1'];
					selectNode($(ctx.qti_item_id + " .match_node.xnode_"+row+".ynode_"+col));
				}
			}
		}
	}
};

//
// GRAPHIC GAP MATCH
//

/**
 * Create a graphic gap match widget: 
 * a background image where you place others images on pre-defined shapes
 * @methodOf QTIWidget
 * @param {Object} ctx the QTIWidget context
 */
QTIWidget.graphic_gap_match = function (ctx){
	
	$(ctx.qti_item_id + ' .qti_graphic_gap_match_spotlist li').wrapInner('<div></div>');
	
	//add breaker
	$(ctx.qti_item_id + " .qti_graphic_gap_match_spotlist li:last").after("<li></li>");
	$(ctx.qti_item_id + " .qti_graphic_gap_match_spotlist li:last").css('clear', 'both');
	
	//adapt the spot container size
	var containerHeight = parseInt($(ctx.qti_item_id + ' .qti_graphic_gap_match_spotlist li').height());
	var containerWidth = 5;
	$(ctx.qti_item_id + ' .qti_graphic_gap_match_spotlist li').each(function(){
		containerWidth += parseInt($(this).width()) + 4;
	});
	$(ctx.qti_item_id + ' .qti_graphic_gap_match_spotlist')
		.width(containerWidth)
			.height(containerHeight);
	
	//drag element from the spot container
	$(ctx.qti_item_id+" .qti_graphic_gap_match_spotlist li > div").draggable({
		drag: function(event, ui){
			// label go on top of the others elements
			$(ui.helper).css("z-index","999");			
		},
		containment: ctx.qti_item_id,
		revert: 'valid',
		cursor:"move"
	});
	
	var imageHeight = parseInt(ctx.opts.imageHeight);
	var imageWidth = parseInt(ctx.opts.imageWidth);
	
	// offset position
	var itemHeight= parseInt($(ctx.qti_item_id).height());
	$(ctx.qti_item_id).css("height",itemHeight+imageHeight);
	
	//if the width and height are not given we preload the images to calculate them
	if(isNaN(imageHeight)|| isNaN(imageWidth)){
		
		$(ctx.qti_item_id).append("<div class='img-loader' style='visibility:hidden;'></div>");
		$loadedImg = $("<img src='"+ctx.opts.imagePath+"' />");
		$loadedImg.load(function(){
			var imageWidth = parseInt($(this).width());
			var imageHeight = parseInt($(this).height());
			$(ctx.qti_item_id+ ' .img-loader').remove();
		
			//we create the area with the calculated size 
			createArea(imageWidth, imageHeight);
		});
		$(ctx.qti_item_id + ' .img-loader').append($loadedImg);
		
	}
	else{	//we use thos providen
		createArea(imageWidth, imageHeight);
	}
	
	/**
	 * Create the area with the image in background
	 * @param {int} imageWidth
	 * @param {int} imageHeight
	 */
	function createArea(imageWidth, imageHeight){
	
		// load image in rapheal area
		$(ctx.qti_item_id + ' .qti_graphic_gap_match_spotlist').before("<div class='svg-container'></div>");
		var paper=Raphael($(ctx.qti_item_id+' .svg-container')[0], imageWidth, imageHeight);
		paper.image(ctx.opts.imagePath,0,0,imageWidth,imageHeight);
		
		//create the shapes from gaps
		var shapes = {};
		for(identifier in ctx.opts.matchMaxes){
			if(ctx.opts.matchMaxes[identifier]['shape'] && ctx.opts.matchMaxes[identifier]['coords']){
				var coords = ctx.opts.matchMaxes[identifier]['coords'].split(',');
				switch(ctx.opts.matchMaxes[identifier]['shape']){
					case 'circle':
						shapes[identifier] = paper.circle(
								Number(coords[0]), 
								Number(coords[1]), 
								Number(coords[2])
							);
						break;
					case 'rect':
						shapes[identifier] = paper.rect(
								Number(coords[0]), 
								Number(coords[1]), 
								Number(coords[2]) - Number(coords[0]),
								Number(coords[3]) - Number(coords[1])
							);
						break;
					case 'ellipse':
						shapes[identifier] = paper.ellipse(
								Number(coords[0]), 
								Number(coords[1]), 
								Number(coords[2]),
								Number(coords[3])
							);
						break;
					case "poly":
						shapes[identifier] = paper.path(
								polyCoordonates(ctx.opts.matchMaxes[identifier]["coords"])
							);			
					break;
				}
				
				shapes[identifier].attr({
					"stroke-opacity": "0",
					"stroke-width"	: "0",
					"fill-opacity"	: "0"
				});
				shapes[identifier].toFront();
				if (ctx.graphicDebug){
					shapes[identifier].attr({
						"stroke-width"	: "3px",
						"stroke-opacity": "1",
						"stroke"		: "blue"
					});
				}
				shapes[identifier].id = identifier;
			}
		}
		
		/**
		 * Detect if the pointer is inside a shape of the raphShape SVG Element
		 * 
		 * @param {Event} event
		 * 
		 * @param {Object} 	params
		 * @param {Raphael} [params.raphShape]
		 * @param {Array} 	[params.collisables]
		 * @param {Float} 	[params.offsetLeft = 0]
		 * @param {Float} 	[params.offsetTop = 0]
		 * 
 		 * @return {Boolean}
		 */
		var detectMouseCollision = function(event, params){
			return raphaelcollision(
					params.raphShape, 
					params.collisables,  
					event.pageX - ((params.offsetLeft) ?  params.offsetLeft : 0), 
					event.pageY - ((params.offsetTop ) ?  params.offsetTop  : 0)
			);
		};
		
		var collisables = [];
		for(i in shapes){
			collisables.push(shapes[i]);
		}
		var offset = $(ctx.qti_item_id+' .svg-container').offset();
		
		var collisionContext = {
			raphShape 	: paper,
			collisables	: collisables,
			offsetLeft : offset.left,
			offsetTop : offset.top	
		};
		
		//the all image is droppable
		$(ctx.qti_item_id+' .svg-container').droppable({
			accept: ctx.qti_item_id + " .qti_graphic_gap_match_spotlist li > div",
			drop: function(event, ui){
				var draggedId = $(ui.draggable).parent().attr("id");
				
				//detect if the mouse is inside a shape
				var result = detectMouseCollision(event, collisionContext);
				if(result.length > 0) {
					fillGap($(this), $(ui.draggable), result[0][2].id, result[0][2]);
				}
			}
		});
	}

	/**
	 * Fill a gap with an element of the word's cloud
	 * @param {jQuery} jDropped
	 * @param {jQuery} jDragged
	 * @param {String} gapId
	 * @param {Raphael} raphShape
	 */
	var fillGap = function(jDropped, jDragged, gapId, raphShape){
		
		var draggedId = jDragged.parent().attr("id");
		var filledId = 'gap_'+gapId+'_'+draggedId;
		
		if($(ctx.qti_item_id+' #'+filledId).length > 0){
			return false;
		}
		
		//if the matchGroup of the choice is defined and not found we cancel the drop 
		var _matchGroup = ctx.opts["matchMaxes"][draggedId]["matchGroup"];
		if(_matchGroup.length > 0){
			if($.inArray(gapId, _matchGroup) < 0){
				raphShape.animate({
					"fill-opacity" : 1,
					"fill":'red'
				}, 500);
				setTimeout(function(){
					raphShape.animate({
						"fill-opacity" :0
					}, 400);
				}, 500);
				return false;
			}
		}
		
		//check too the matchGroup of the gap
		var _gapMatchGroup = ctx.opts["matchMaxes"][gapId]["matchGroup"];
		if(_gapMatchGroup.length > 0){
			if($.inArray(draggedId, _gapMatchGroup) < 0){
				raphShape.animate({
					"fill-opacity" : 1,
					"fill":'red'
				}, 500);
				setTimeout(function(){
					raphShape.animate({
						"fill-opacity" :0
					}, 400);
				}, 500);
				return false;
			}
		}
		
		//check the matchMax of the element
		var _matchMax 	= Number(ctx.opts["matchMaxes"][draggedId]["matchMax"]);
		var _current 	= Number(ctx.opts["matchMaxes"][draggedId]["current"]);
		
		if (_current < _matchMax) {
			_current++;
			ctx.opts["matchMaxes"][draggedId]["current"] = _current;
		}
		if(_current >= _matchMax){
			jDragged.hide();
		}
		
		// add the new element inside the box that received the cloud element
		jDropped.append("<div id='"+filledId+"' class='filled_gap'>"+jDragged.html()+"</span>");
		$(ctx.qti_item_id+' #'+filledId).css({
			'top'	: raphShape.attrs.y,
			'left'	: raphShape.attrs.x
		});
		
		//enable to drop it back to remove it from the gap
		$(ctx.qti_item_id+' #'+filledId).draggable({
			drag: function(event, ui){
				// label go on top of the others elements
				$(ui.helper).css("z-index","999");
			},
			stop: function(){
				removeFilledGap($(this));
			},
			revert: false,
			containment: ctx.qti_item_id,
			cursor:"move"
		});
	};
	
	/**
	 * remove an element from the filled gap
	 * @param {jQuery} jElement
	 */
	var removeFilledGap = function(jElement){
		var filledId = jElement.attr("id").replace('gap_', '').split('_');
		var draggedId = filledId[1];
		
		var _matchMax = Number(ctx.opts["matchMaxes"][draggedId]["matchMax"]);
		var _current = Number(ctx.opts["matchMaxes"][draggedId]["current"]);
		if (_current > 0) {
			ctx.opts["matchMaxes"][draggedId]["current"] = _current - 1;
		}
		jElement.remove();
		if(_current >= _matchMax){
			$(ctx.qti_item_id+' #'+draggedId+" div").show();
		}
	};
};

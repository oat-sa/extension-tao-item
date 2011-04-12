/**
 * Associate widgets: associate and graphic associate QTI's interactions
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
//	ASSOCIATE
//

/**
 * Creates a pair association widget :
 * where words are dragged from a cloud to pair boxes
 * @methodOf QTIWidget
 * @param {Object} ctx the QTIWidget context
 */
QTIWidget.associate = function(ctx){
		
	// max size of a text box to define target max size
	// create empty element in order to droppe in any other element of the item
	$(ctx.qti_item_id+" .qti_choice_list li").wrapInner("<div></div>");
	// calculate max size of a word in the cloud
	var maxBoxSize=0;
	$(ctx.qti_item_id+" ul.qti_choice_list li > div").each(function(){	
		if ($(this).width()>maxBoxSize){
			maxBoxSize=$(this).width();		
		}
	});
			
	// give a size to the words cloud to avoid graphical "jump" when items are dropped
	var listHeight = parseInt($(ctx.qti_item_id+" .qti_associate_container").height());
	var liMaxHeight = 0;
	$(ctx.qti_item_id+" .qti_choice_list li > div").each(function(){
		var liHeight = parseInt($(this).height());
		if(liHeight > liMaxHeight){
			liMaxHeight = liHeight;
		}
		if(liHeight > listHeight){
			listHeight = liHeight +10;
		}
	});
	$(ctx.qti_item_id+" .qti_associate_container .qti_choice_list").height(listHeight + 10);
	
	// create the pair of box specified in the maxAssociations attribute	
	if(ctx.opts["maxAssociations"] > 0) {
		var pairSize = ctx.opts["maxAssociations"];
	}
	else{
		var pairSize = parseInt($(ctx.qti_item_id+" .qti_choice_list li").length / 2);
	}
	var pairContainer = $("<div class='qti_pair_container'></div>");
	for (var a=1; a<=pairSize; a++){
		var currentPairName=ctx.opts["id"]+"_"+a;
		pairContainer.append("<ul class='qti_association_pair' id='"+currentPairName+"'><li id='"+currentPairName+"_A"+"'></li><li id='"+currentPairName+"_B"+"'></li></ul>");		
	}
	$(ctx.qti_item_id+" .qti_associate_container").after(pairContainer);
	
	// set the size of the drop box to the max size of the cloud words 
	$(ctx.qti_item_id+" .qti_association_pair li").width(maxBoxSize+4);
	
	// size the whole pair box to center it
	var pairBoxWidth=0;
	
	$(ctx.qti_item_id+" .qti_association_pair:first li").each(function(){
		pairBoxWidth+=$(this).width();
	});
	$(ctx.qti_item_id+" .qti_pair_container").css({position:"relative",width: ((pairBoxWidth + 20)*2)+30, margin:"0 auto 0 auto",top:"10px"});	
	$(ctx.qti_item_id+" .qti_association_pair").width(pairBoxWidth+90);
	$(ctx.qti_item_id+" .qti_association_pair li").height(liMaxHeight);
	
	$.each($(ctx.qti_item_id+" .qti_association_pair"), function(index, elt){
		$(elt).after("<div class='qti_link_associate'></div>");
		
		$(ctx.qti_item_id+" .qti_link_associate:last").css("top",$(this).position().top + 25);
		$(ctx.qti_item_id+" .qti_link_associate:last").css("left", maxBoxSize + 33);
	});

	$(ctx.qti_item_id).height( ($(ctx.qti_item_id+" .qti_association_pair:last").offset().top - $(ctx.qti_item_id).offset().top ) + liMaxHeight); 
	
	//drag element from words cloud
	$(ctx.qti_item_id+" .qti_associate_container .qti_choice_list li > div").draggable({
		drag: function(event, ui){
			// label go on top of the others elements
			$(ui.helper).css("z-index","999");			
		},
		containment: ctx.qti_item_id,
		cursor:"move",
		revert: true
	});
	
	/**
	 * remove an element from the filled gap
	 * @param {jQuery} jElement
	 */
	var removeFilledPair = function(jElement){
		var filledId = jElement.attr("id").replace('pair_', '');
		var _matchMax = Number(ctx.opts["matchMaxes"][filledId]["matchMax"]);
		var _current = Number(ctx.opts["matchMaxes"][filledId]["current"]);
		
		if (_current > 0) {
			ctx.opts["matchMaxes"][filledId]["current"] = _current - 1;
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
		$(ctx.qti_item_id+" #pair_"+draggedId).width($(ctx.qti_item_id+" .qti_association_pair li").width());
		$(ctx.qti_item_id+" #pair_"+draggedId).height($(ctx.qti_item_id+" .qti_association_pair li").height());
		
		var _matchMax 	= Number(ctx.opts["matchMaxes"][draggedId]["matchMax"]);
		var _current 	= Number(ctx.opts["matchMaxes"][draggedId]["current"]);
		
		if (_current < _matchMax) {
			_current++;
			ctx.opts["matchMaxes"][draggedId]["current"]=_current;
		}
		if (_current >= _matchMax && _matchMax > 0) {
			jDragged.hide();
		}
		
		// give this new element the ability to be dragged
		$(ctx.qti_item_id+" #pair_"+draggedId).draggable({
			drag: function(event, ui){
				// element is on top of the other when it's dragged
				$(this).css("z-index", "999");
			},
			stop: function(event, ui) {
				removeFilledPair($(this));
				return true;
			 },
			 containment: ctx.qti_item_id,
			 cursor:"move"
		});
	};
	
	// pair box are droppable
	$(ctx.qti_item_id+" .qti_association_pair li").droppable({
		drop: function(event, ui){
			
			var draggedId = $(ui.draggable).parents().attr('id');
			
			//prevent of re-filling the gap and dragging between the gaps
			if($(this).find("#pair_"+draggedId).length > 0 || /^pair_/.test($(ui.draggable).attr('id'))){
				return false;
			}
			
			var _matchGroup = ctx.opts["matchMaxes"][draggedId]["matchGroup"];
			
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
						$(this).effect("highlight", {color:'#B02222'}, 1000);
						return false;
					}
				}
				
				var _oppositeMatchGroup = ctx.opts["matchMaxes"][oppositeId]["matchGroup"];
				if(_oppositeMatchGroup.length > 0){
					if($.inArray(draggedId, _oppositeMatchGroup) < 0){
						$(this).effect("highlight", {color:'#B02222'}, 1000);
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
	if(ctx.opts["values"]){
		var index = 1;
		var values = ctx.opts["values"];
		if(typeof(values) == 'object'){
			for(i in values){
				var pair = values[i];
				if(pair.length == 2){
					var valA = pair[0];
					if(typeof(valA) == 'string' && valA != ''){
						if($(ctx.qti_item_id+" li#"+valA).length == 1){
							fillPair($(ctx.qti_item_id+"_"+ index+"_A"), $(ctx.qti_item_id+" .qti_associate_container .qti_choice_list li#"+valA+" > div"));
						}
					}
					var valB = pair[1];
					if(typeof(valB) == 'string' && valB != ''){
						if($(ctx.qti_item_id+" li#"+valB).length == 1){
							fillPair($(ctx.qti_item_id+"_"+ index+"_B"), $(ctx.qti_item_id+" .qti_associate_container .qti_choice_list li#"+valB+" > div"));
						}
					}
					index++;
				}
			}
		}
	}
};


//
//GRAPHIC ASSOCIATE
//

/**
 * Creates a graphic associate interaction :
 * make links between spots
 * 
 * @methodOf QTIWidget
 * @param {Object} ctx the QTIWidget context
 */
QTIWidget.graphic_associate = function (ctx){

	var countChoices=ctx.opts["maxAssociations"];
    var pointsPair=new Array();
    
    var imageHeight = parseInt(ctx.opts.imageHeight);
	var imageWidth = parseInt(ctx.opts.imageWidth);
    
	// offset position
	$(ctx.qti_item_id+" .qti_graphic_associate_spotlist li").css("display","none");
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
		$(ctx.qti_item_id).prepend("<div class='svg-container'></div>");
		var paper=Raphael($(ctx.qti_item_id+' .svg-container')[0], imageWidth, imageHeight);
		paper.image(ctx.opts.imagePath,0,0,imageWidth,imageHeight);
		
		var model_obj=new Object();
        var line_ref_obj=new Object();
            
        // display max link or inifinite
        if (ctx.opts["maxAssociations"]>0){
          $(ctx.qti_item_id+" .link_counter").text(ctx.opts["maxAssociations"]);
        } else {
           $(ctx.qti_item_id+" .link_counter").html("<span class='infiniteSize'>∞</span>");
        }

        // red cross
        var deleteButton=paper.image(ctx.wwwPath+"img/delete.png",0,0,14,14);
        deleteButton.attr("opacity","0");
            
        // create hotspot
        $(ctx.qti_item_id+" .qti_graphic_associate_spotlist li").each(function(){
        	
			var currentHotSpotShape		= ctx.opts.graphicAssociateChoices[$(this).attr("id")]["shape"];
			var currentHotSpotCoords	= ctx.opts.graphicAssociateChoices[$(this).attr("id")]["coords"].split(",");
	        var currentHotSpotX, currentHotSpotY, currentHotSpotSize, currentHotSpotTopX, currentHotSpotTopY;
	        var currentHotSpotBottomX, currentHotSpotBottomY, currentHotSpotHradius, currentHotSpotVradius;
	        var pointerSize=3, pointer, pointerCoordonates;
	        var polyCoords;
	        var currentShape;
	        var shapeWidth, shapeHeight;
       
			switch(currentHotSpotShape){
				case "circle":
					currentHotSpotX=Number(currentHotSpotCoords[0]);
					currentHotSpotY=Number(currentHotSpotCoords[1]);
					currentHotSpotSize=currentHotSpotCoords[2];
					currentShape=paper[currentHotSpotShape](currentHotSpotX,currentHotSpotY,currentHotSpotSize);
					if (ctx.graphicDebug) currentShape.attr("stroke-width", "3px");
	                pointer = paper.circle(currentHotSpotX, currentHotSpotY, pointerSize);
	                break;
				case "rect":
					currentHotSpotTopX=Number(currentHotSpotCoords[0]);
					currentHotSpotTopY=Number(currentHotSpotCoords[1]);
					currentHotSpotBottomX=currentHotSpotCoords[2]-currentHotSpotTopX;
					currentHotSpotBottomY=currentHotSpotCoords[3]-currentHotSpotTopY;
					currentShape=paper[currentHotSpotShape](currentHotSpotTopX,currentHotSpotTopY,currentHotSpotBottomX,currentHotSpotBottomY);
					if (ctx.graphicDebug) currentShape.attr("stroke-width", "3px");
	                shapeWidth=currentShape.getBBox().width;
					shapeHeight=currentShape.getBBox().height;
					pointer = paper.circle(currentHotSpotTopX+(shapeWidth/2), currentHotSpotTopY+(shapeHeight/2), pointerSize);
	                break;
				case "ellipse":
					currentHotSpotX=Number(currentHotSpotCoords[0]);
					currentHotSpotY=Number(currentHotSpotCoords[1]);
					currentHotSpotHradius=currentHotSpotCoords[2];
					currentHotSpotVradius=currentHotSpotCoords[3];
					currentShape=paper[currentHotSpotShape](currentHotSpotX,currentHotSpotY,currentHotSpotHradius,currentHotSpotVradius);
					if (ctx.graphicDebug) currentShape.attr("stroke-width", "3px");
					pointer = paper.circle(currentHotSpotX, currentHotSpotY, pointerSize);
	                break;
				case "poly":
					polyCoords=polyCoordonates(ctx.opts.graphicAssociateChoices[$(this).attr("id")]["coords"]);
					currentShape=paper["path"](polyCoords);
					if (ctx.graphicDebug) currentShape.attr("stroke-width", "3px");
					shapeWidth=currentShape.getBBox().width;
					shapeHeight=currentShape.getBBox().height;
				 	pointerCoordonates=pointerPolyCoordonates(ctx.opts.graphicAssociateChoices[$(this).attr("id")]["coords"]);
					currentHotSpotTopX=Number(pointerCoordonates[0]);
					currentHotSpotTopY=Number(pointerCoordonates[1]);
					pointer = paper.circle(currentHotSpotTopX+(shapeWidth/2), currentHotSpotTopY+(shapeHeight/2), pointerSize);
	                break;
			}

	        var maxSubLink=ctx.opts.graphicAssociateChoices[$(this).attr("id")]["matchMax"];
	
	        model_obj[$(this).attr("id")] = { 
	        		pointer			: pointer,
	        		ref				: currentShape,
	        		pointerState	: "hidden",
	        		linkRelation	: {},
	        		maxSubLinkLength: maxSubLink
	        };
	        pointer.attr("fill","black");
			pointer.attr("opacity","0");
			currentShape.toFront();
			currentShape.attr("fill", "pink");
			currentShape.attr("fill-opacity", "0");
			currentShape.attr("stroke-opacity", "0");
	                    
			if (ctx.graphicDebug) currentShape.attr("stroke-opacity", "1");
	                    
			currentShape.attr("stroke", "blue");
                    
			// add a reference to newly created object
			ctx.opts[currentShape.node]=$(this).attr("id");
			$(currentShape.node).bind("mousedown",{
					zis			: currentShape,
					name		: $(this).attr("id"),
		            pair		: pointsPair,
		            zepointer	: pointer
				}, function(e){

	                for (var i in line_ref_obj){
	                	line_ref_obj[i].attr("stroke","black");
	                }
	
	                $(deleteButton.node).unbind("mousedown");
	
	                var pointer=e.data.zepointer;
	                deleteButton.attr("opacity","0");
	                pointer.attr("fill","red");
	                pointer.attr("stroke","white");
	                if (e.data.pair.length<1){
	                    e.data.pair.push(e.data.name);
	
	                    var currentLinkLength=displayedSubLink(model_obj[e.data.name].linkRelation);
	                    var maxLinkAvalaible=model_obj[e.data.name].maxSubLinkLength-currentLinkLength;
	                    
	                    if(model_obj[e.data.name].maxSubLinkLength<1){
	                        $(ctx.qti_item_id+" .sub_counter").html("<span class='infiniteSize'>∞</span>");
	                    } 
	                    else {
	                    	$(ctx.qti_item_id+" .sub_counter").text(maxLinkAvalaible);
	                    }
	                } 
	                else {
	                    e.data.pair.push(e.data.name);
	                    
	                    // store bilateral relation
                        var startId=e.data.pair[0];
                        var endId=e.data.pair[1];
	                    
	                    //define begin and end point
	                    var startPointer=model_obj[e.data.pair[0]].pointer;
	                    var endPointer=model_obj[e.data.pair[1]].pointer;
	                    var relation=e.data.pair[0]+' '+e.data.pair[1];
	                    var targetRelation=e.data.pair[1]+' '+e.data.pair[0];
	
	                    // avoid double click on same pointer
	                    if (startPointer==endPointer) {
	                        startPointer.attr("fill","black");
	                        startPointer.attr("stroke","black");
	                        endPointer.attr("fill","black");
	                        endPointer.attr("stroke","black");
	                        hideSinglePoint(model_obj, e.data.pair[0], e.data.pair[1], startPointer, endPointer);
	                        emptyArray(e.data.pair);
	                        $(ctx.qti_item_id+" .sub_counter").text("");
	                        startPointer.attr("opacity","0");
	                        return;
	                    }
	
	                    // block if maxAssociations are reached
	                    if (countChoices==0 && ctx.opts["maxAssociations"]!=0){
	                        startPointer.attr("fill","black");
	                        startPointer.attr("stroke","black");
	                        endPointer.attr("fill","black");
	                        endPointer.attr("stroke","black");
	                        hideSinglePoint(model_obj, e.data.pair[0], e.data.pair[1], startPointer, endPointer);
	                        emptyArray(e.data.pair);
	                        return;
	                    }
	                    // verify maxSubLink
	                    var subLinkLength=displayedSubLink(model_obj[e.data.pair[0]].linkRelation);
	                    var targetSubLinkLength=displayedSubLink(model_obj[e.data.pair[1]].linkRelation);
	                   
	                    // block max sub relation
	                    // case infinite
	                    if (model_obj[e.data.pair[0]].maxSubLinkLength==0){
	                        if ((targetSubLinkLength+1)>model_obj[e.data.pair[1]].maxSubLinkLength && model_obj[e.data.pair[1]].maxSubLinkLength != 0){
	                           startPointer.attr("fill","black");
	                           startPointer.attr("stroke","black");
	                           endPointer.attr("fill","black");
	                           endPointer.attr("stroke","black");
	                           hideSinglePoint(model_obj, e.data.pair[0], e.data.pair[1], startPointer, endPointer);
	                           emptyArray(e.data.pair);
	                           return;
	                        }
	
	                    } else 
	                    // case finite sublink length
	                    if (subLinkLength>=model_obj[e.data.pair[0]].maxSubLinkLength || (targetSubLinkLength+1)>model_obj[e.data.pair[1]].maxSubLinkLength){
	                         if (model_obj[e.data.pair[1]].maxSubLinkLength!=0){
	                             // empty current pair
	                             startPointer.attr("fill","black");
	                             startPointer.attr("stroke","black");
	                             endPointer.attr("fill","black");
	                             endPointer.attr("stroke","black");
	                             hideSinglePoint(model_obj, e.data.pair[0], e.data.pair[1], startPointer, endPointer);
	                             emptyArray(e.data.pair);
	                             return;
	                        }
	                    }
	                    // bug peux plus creer de sublien (a tester)
	                    if (model_obj[e.data.pair[0]].linkRelation[relation]!=undefined){
	                        emptyArray(e.data.pair);
	                        return;
	                    }
	                    var startPointX=startPointer.getBBox().x+startPointer.getBBox().width/2;
	                    var startPointY=startPointer.getBBox().y+startPointer.getBBox().width/2;
	                    var endPointX=endPointer.getBBox().x+endPointer.getBBox().width/2;
	                    var endPointY=endPointer.getBBox().y+endPointer.getBBox().width/2;
	                    var drawingPath="M"+startPointX+" "+startPointY+"L"+endPointX+" "+endPointY;
	                    // trace line between dots
	                    var line=paper.path(drawingPath);
	
	                    // black pointer
	                    startPointer.attr("fill","black");
	                    startPointer.attr("stroke","black");
	                    endPointer.attr("fill","black");
	                    endPointer.attr("stroke","black");
	                    
	                    model_obj[startId].linkRelation[relation]={};
	                    model_obj[startId].linkRelation[relation].lineRef=line;
	                    model_obj[startId].linkRelation[relation].endRef=endPointer;
	
	                    model_obj[endId].linkRelation[targetRelation]={};
	                    model_obj[endId].linkRelation[targetRelation].lineRef=line;
	                    model_obj[endId].linkRelation[targetRelation].endRef=startPointer;
	
	                    line_ref_obj[relation]=line;
	                    var pairs = [];
	                    for(aPair in line_ref_obj){
	                    	pairs.push(aPair);
	                    }
	                    $(ctx.qti_item_id).data('pairs', pairs);
	                    
	                    emptyArray(e.data.pair);
	                    
	                    var pairOfPoints=e.data.pair;
	
	                     // click on line
	                     $(line.node).bind("mousedown",{
		                    	 zeline 	: line,
		                    	 centerX	: (startPointX+endPointX)/2,
		                    	 centerY	: (startPointY+endPointY)/2,
		                    	 zerelation :relation
	                    	 }, function(e){
	
	                           	var localRelation=e.data.zerelation;
	                            var localLine=e.data.zeline;
	
	                            // color all line in black
	                            for (var i in line_ref_obj){
	                                line_ref_obj[i].attr("stroke","black");
	                            }
	
	                            deleteButton.attr("opacity","1");
	                            deleteButton.attr("x",e.data.centerX-7);
                                deleteButton.attr("y",e.data.centerY-7);
	                            deleteButton.toFront();
	
	                            $(deleteButton.node).bind("mousedown",{zisbutton:deleteButton}, function(e){
	                            	
	                                delete model_obj[startId].linkRelation[relation];
	                                delete model_obj[endId].linkRelation[targetRelation];
	
	                                localLine.remove();
	                                e.data.zisbutton.attr("opacity","0");
	
	                                if (ctx.opts["maxAssociations"]>0 && countChoices < ctx.opts["maxAssociations"]){
	                                	countChoices++;
	                                	$(ctx.qti_item_id+" .link_counter").text(countChoices);
	                                }
	                                
	                                hideSinglePoint(model_obj, startId, endId, startPointer, endPointer);
	                                
	                                delete line_ref_obj[localRelation];
	
	                                emptyArray(pairOfPoints);
	                                
	                                line_ref_obj[relation]=line;
	                                var pairs = [];
	                                for(aPair in line_ref_obj){
	                                	pairs.push(aPair);
	                                }
	                                $(ctx.qti_item_id).data('pairs', pairs);
	                                
	                                $(this).unbind("mousedown");
	                            });
	                            
	                            var line=e.data.zeline;
	                            line.attr("stroke","red");
	                            
	                            $(this).unbind("mousedown");
	                     });
	
	                    // link counter displayed (except if ctx.opts["maxAssociations"]=0)
	                    if (ctx.opts["maxAssociations"]>0){
	                    	countChoices--;
	                        $(ctx.qti_item_id+" .link_counter").text(countChoices);
	                     }
	                     e.data.zis.toFront();
	                }
	
	                model_obj[e.data.name].pointer.attr("opacity","1");
	                model_obj[e.data.name].pointerState="show";
	                 
	                for (var t in model_obj){
	                     model_obj[t].ref.toFront();
	                }
				});
		});
	}
	
    //set the values if defined
	if(ctx.opts["values"]){
		var values = ctx.opts["values"];
		if(typeof(values) == 'object'){
			for(index in values){
				var value = values[index];
				if(typeof(value) == 'object'){
					if(value.length == 2){
						//create a pair by triggering the clicks 
						var startShape = model_obj[value[0]]['ref'];
						if(startShape){
							$(startShape.node).trigger("mousedown",{
								zis			: startShape,
								name		: value[0],
					            pair		: pointsPair,
					            zepointer	: model_obj[value[0]]['pointer']
							});
						
							var endShape = model_obj[value[1]]['ref'];
							if(endShape){
								$(endShape.node).trigger("mousedown",{
									zis			: endShape,
									name		: value[1],
						            pair		: pointsPair,
						            zepointer	: model_obj[value[1]]['pointer']
								});
							}
						}
					}
				}
			}
		}
	}
	
	function hideSinglePoint(obj, startId, endId, startPointer, endPointer){
       var rela=0;
       // hide points alone
       for (var z in obj[startId].linkRelation){
       rela++;
       }
        var relb=0;
        for (var e in obj[endId].linkRelation){
        relb++;
        }
        if (rela<1) startPointer.attr("opacity","0");
        if (relb<1) endPointer.attr("opacity","0");
    }

 	function emptyArray(aRay){
        while (aRay.length>0){
            aRay.pop();
        }
    }

    function displayedLink(objai){
        var xx=0;
        for (var a in objai){
            if (displayedSubLink(objai[a].linkRelation)>0)xx++;
        }
        return xx;
    }

    function displayedSubLink(objai){
        var i=0;
        for (var a in objai){
           i++;
        }
        return i;
    }
};
	
/**
 * Spots widgets: hottext, hotspot and select point QTI's interactions
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
//	HOTTEXT
//

/**
 * Creates a  hottext widget,
 * it support 3 behaviors: 
 * 	- without restriction, 
 *  - one by one and 
 *  - N at a time
 *  @methodOf QTIWidget
 *  @param {Object} ctx the QTIWidget context
 */
QTIWidget.hottext = function(ctx){
		
	//the hottext behavior depends on the maxChoice value
	var maxChoices = (ctx.opts['maxChoices']) ? parseInt(ctx.opts['maxChoices']) : 1;
	$(ctx.qti_item_id + " .hottext_choice").click(function(){
		
		//no behavior restriction
		if(maxChoices == 0){
			$(this).toggleClass('hottext_choice_on')
					.toggleClass('hottext_choice_off');
		}
		
		//only one selected at a time 
		if(maxChoices == 1){
			if($(ctx.qti_item_id + " .hottext_choice").length == 1){
				$(this).toggleClass('hottext_choice_on')
					.toggleClass('hottext_choice_off');
			}
			else{
				$(ctx.qti_item_id + " .hottext_choice").removeClass('hottext_choice_on').addClass('hottext_choice_off');
				$(this).removeClass('hottext_choice_off').addClass('hottext_choice_on');
			}
			
		}
		
		//there is only maxChoices selected at a time
		if(maxChoices > 1){
			if($(ctx.qti_item_id + " .hottext_choice_on").length < maxChoices || $(this).hasClass('hottext_choice_on') ){
				$(this).toggleClass('hottext_choice_on')
						.toggleClass('hottext_choice_off');
			}
		}
	});
	//set the current values if defined
	if(ctx.opts["values"]){
		var values = ctx.opts["values"];
		if(typeof(values) == 'string' && values != ''){
			$(ctx.qti_item_id + " #hottext_choice_"+values).switchClass('hottext_choice_off', 'hottext_choice_on');
		}
		if(typeof(values) == 'object'){
			for(i in values){
				var value = values[i];
				if(typeof(value) == 'string' && value != ''){
					$(ctx.qti_item_id + " #hottext_choice_"+value).switchClass('hottext_choice_off', 'hottext_choice_on');
				}
			}
		}
	}
};
	

	
//
//	HOTSPOT
//
	
/**
 * Creates a clickable image with hotspots
 * @methodOf QTIWidget
 */
QTIWidget.hotspot = function (ctx){
	
	//if the values are defined
	var currentValues = [];
	if(ctx.opts["values"]){
		var values = ctx.opts["values"];
		if(typeof(values) == 'object'){
			for (i in values){
				currentValues.push( values[i]);
			}
		}
		if(typeof(values) == 'string'){
			currentValues.push(values);
		}
	}
	
	var maxChoices=ctx.opts["maxChoices"];
	var countChoices=0;
	
	var imageHeight = parseInt( ctx.opts["imageHeight"]);
	var imageWidth	= parseInt( ctx.opts["imageWidth"]);
	
	// offset position
	$(ctx.qti_item_id+" .qti_hotspot_spotlist li").css("display","none");
	var itemHeight	= parseInt($(ctx.qti_item_id).height());
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
		$(ctx.qti_item_id).append("<div class='svg-container'></div>");
		var paper=Raphael($(ctx.qti_item_id+' .svg-container')[0], imageWidth, itemHeight+imageHeight);
		paper.image(ctx.opts.imagePath,0,0,imageWidth,imageHeight);
		// create hotspot
		$(ctx.qti_item_id+" .qti_hotspot_spotlist li").each(function(){
			var currentHotSpotShape=ctx.opts.hotspotChoice[$(this).attr("id")]["shape"];
			var currentHotSpotCoords=ctx.opts.hotspotChoice[$(this).attr("id")]["coords"].split(",");		
			// create pointer to validate interaction
			// map QTI shape to Raphael shape
			// Depending the shape, options may vary
			switch(currentHotSpotShape){
				case "circle":				
					var currentHotSpotX=Number(currentHotSpotCoords[0]);
					var currentHotSpotY=Number(currentHotSpotCoords[1]);
					var currentHotSpotSize=currentHotSpotCoords[2];
					var currentShape=paper[currentHotSpotShape](currentHotSpotX,currentHotSpotY,currentHotSpotSize);			
					if (ctx.graphicDebug) currentShape.attr("stroke-width", "3px");
					var shapeWidth=currentShape.getBBox().width;
					var shapeHeight=currentShape.getBBox().height;
					var pointerWidth=10;
					var pointerHeight=10;
					var pointer = paper.image(ctx.wwwPath + "img/cross.png", currentHotSpotX-(pointerWidth/2), currentHotSpotY-(pointerHeight/2), pointerWidth, pointerHeight);
					break;
				case "rect":
					var currentHotSpotTopX=Number(currentHotSpotCoords[0]);
					var currentHotSpotTopY=Number(currentHotSpotCoords[1]);
					var currentHotSpotBottomX=currentHotSpotCoords[2]-currentHotSpotTopX;
					var currentHotSpotBottomY=currentHotSpotCoords[3]-currentHotSpotTopY;
					var currentShape=paper[currentHotSpotShape](currentHotSpotTopX,currentHotSpotTopY,currentHotSpotBottomX,currentHotSpotBottomY);
					if (ctx.graphicDebug) currentShape.attr("stroke-width", "3px");
					var shapeWidth=currentShape.getBBox().width;
					var shapeHeight=currentShape.getBBox().height;
					var pointerWidth=10;
					var pointerHeight=10;
					var pointer = paper.image(ctx.wwwPath + "img/cross.png", currentHotSpotTopX+(shapeWidth/2)-(pointerWidth/2), currentHotSpotTopY+(shapeHeight/2)-(pointerHeight/2), pointerWidth, pointerHeight);				
					break;	
				case "ellipse":
					var currentHotSpotX=Number(currentHotSpotCoords[0]);
					var currentHotSpotY=Number(currentHotSpotCoords[1]);
					var currentHotSpotHradius=currentHotSpotCoords[2];
					var currentHotSpotVradius=currentHotSpotCoords[3];
					var currentShape=paper[currentHotSpotShape](currentHotSpotX,currentHotSpotY,currentHotSpotHradius,currentHotSpotVradius);	
					if (ctx.graphicDebug) currentShape.attr("stroke-width", "3px");			
					var pointerWidth=10;
					var pointerHeight=10;
					var pointer = paper.image(ctx.wwwPath + "img/cross.png", currentHotSpotX-(pointerWidth/2), currentHotSpotY-(pointerHeight/2), pointerWidth, pointerHeight);				
					break;
				case "poly":
					var polyCoords=polyCoordonates(ctx.opts.hotspotChoice[$(this).attr("id")]["coords"]);
					var currentShape=paper["path"](polyCoords);			
					if (ctx.graphicDebug) currentShape.attr("stroke-width", "3px");			
					var shapeWidth=currentShape.getBBox().width;
					var shapeHeight=currentShape.getBBox().height;
				 	var pointerCoordonates=pointerPolyCoordonates(ctx.opts.hotspotChoice[$(this).attr("id")]["coords"]);
					var currentHotSpotTopX=Number(pointerCoordonates[0]);
					var currentHotSpotTopY=Number(pointerCoordonates[1]);
					var pointerWidth=10;
					var pointerHeight=10;
					var pointer = paper.image(ctx.wwwPath + "img/cross.png", currentHotSpotTopX+(shapeWidth/2)-(pointerWidth/2), 	currentHotSpotTopY+(shapeHeight/2)-(pointerHeight/2), pointerWidth, pointerHeight);				
				break;
			}
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
				zis:currentShape,
				name: $(this).attr("id"), 
				raphElement:currentShape			
			},function(e){				
				var node = $(ctx.qti_item_id+" #"+e.data.name);
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
	}
};

//
//	SELECT POINT
//
	
/**
 * Creates a clickable image with free hotspots
 * @methodOf QTIWidget
 * @param {Object} ctx the QTIWidget context
 */
QTIWidget.select_point = function (ctx){
	
	var maxChoices=ctx.opts["maxChoices"];
	var countChoices=0;
	
	var imageHeight = parseInt(ctx.opts.imageHeight);
	var imageWidth  = parseInt(ctx.opts.imageWidth);
	
	// offset position
	var itemHeight= parseInt($(ctx.qti_item_id).height());
	$(ctx.qti_item_id).css("height",itemHeight+imageHeight);
	
	
	// load image in rapheal area
	$(ctx.qti_item_id+" .qti_select_point_interaction_container")
		.css({"background":"url("+ctx.opts.imagePath+") no-repeat"})
		.width(imageWidth + 'px')
		.height(imageHeight + 'px');
	
	/**
	 * place an removable image on the selected point
	 * @param {jQuery} jContainer
	 * @param {integer} x
	 * @param {integer} y
	 */
	function setPoint(jContainer, x, y){
		if(countChoices>=maxChoices && maxChoices!=0){
			return;
		}
		countChoices++;
		
		var offset = jContainer.offset();

		jContainer
			.append("<img src='"+ctx.wwwPath+"img/cross.png' alt='cross' class='select_point_cross' />")
			.find("img:last")
			.css({position:"absolute", top:y + 'px', left:x + 'px', "cursor":"pointer"})
			.data('coords', parseInt(x - offset.left)  + ',' + parseInt(y  - offset.top))
			.bind("click",function(e){
				countChoices--;
				$(this).remove();
				return false;
			});
	}
	
	var containerArea = $(ctx.qti_item_id+" .qti_select_point_interaction_container");
	
	//if the values are defined
	if(ctx.opts["values"]){
		var values = ctx.opts["values"];
		if(typeof(values) == 'object'){
			for (i in values){
				var value = values[i];
				if(typeof(value) == 'object'){
					if(value['0'] && value['1']){
						setPoint(containerArea, containerArea.offset().left +parseInt(value['0']), containerArea.offset().top + parseInt(value['1']));
					}
				}
			}
		}
	}
	
	//set a point on a click
	containerArea.bind("click",function(e){
		var relativeXmouse = e.pageX-5;
		var relativeYmouse = e.pageY-5;
		setPoint($(this),relativeXmouse, relativeYmouse);
	});		
};
	

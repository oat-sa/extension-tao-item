/**
 * Order widgets: order and graphic order QTI's interactions
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
//ORDER
//


/**
 * Creates a sortable list widget,
 * can be horizontal or vertical regarding the orientation parameter
 * @methodOf QTIWidget
 * @param {Object} ctx the QTIWidget context
 */
QTIWidget.order = function(ctx){
	
	//if the values are defined
	if(ctx.opts["values"]){
		var values = ctx.opts["values"];
		if(typeof(values) == 'object'){
			
			//we take the list element corresponding to the given ids 
			var list = new Array();
			for(i in values){
				var value = values[i];
				if(typeof(value) == 'string' && value != ''){
					list.push($(ctx.qti_item_id+" ul.qti_choice_list li#"+value));
				}
			}
			
			//and we reorder the elements in the list
			if(list.length == $(ctx.qti_item_id+" ul.qti_choice_list li").length && list.length > 0){
				$(ctx.qti_item_id+" ul.qti_choice_list").empty();
				for(i in list){
					$(ctx.qti_item_id+" ul.qti_choice_list").append(list[i]);
				}
			}
		}
	}
	
	
	var suffixe="";
	
	// test direction
	if(ctx.opts.orientation=="horizontal"){
		// horizontal sortable options
		$(ctx.qti_item_id+" .qti_choice_list").removeClass("qti_choice_list").addClass("qti_choice_list_horizontal");
			
			var sortableOptions = 
			{
				placeholder: 'sort-placeholder',
				axis : 'x',
				containment: ctx.qti_item_id,
				tolerance: 'pointer',
				forcePlaceholderSize: true,
				opacity: 0.8,
				start:function(event,ui){
					$(ctx.qti_item_id+" .sort-placeholder").width($("#"+ui.helper[0].id).width()+4);
					$(ctx.qti_item_id+" .sort-placeholder").height($("#"+ui.helper[0].id).height()+4);
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
			containment: ctx.qti_item_id,
			tolerance: 'pointer',
			forcePlaceholderSize: true,
			opacity: 0.8,
			start:function(event,ui){
				$(ctx.qti_item_id+" .sort-placeholder").width($("#"+ui.helper[0].id).width()+4);
				$(ctx.qti_item_id+" .sort-placeholder").height($("#"+ui.helper[0].id).height()+4);
				$("#"+ui.helper[0].id).css("top","-4px");
			},
			beforeStop:function(event,ui){
				$("#"+ui.helper[0].id).css("top","0");
			}
		};
	}
	//for an horizontal sortable list
	
	if(ctx.opts.orientation=="horizontal"){
		$(ctx.qti_item_id).append("<div class='sizeEvaluator'></div>");
		$(ctx.qti_item_id+" .sizeEvaluator").css("font-size",$(ctx.qti_item_id+" .qti_choice_list_horizontal li").css("font-size"));		
		
		var containerWidth = 0;
		$(ctx.qti_item_id+" .qti_choice_list_horizontal li").each(function(){
			$(ctx.qti_item_id+" .sizeEvaluator").text($(this).text());			
			var liSize=$(ctx.qti_item_id+" .sizeEvaluator").width();	
			
			var liChildren = $(this).children();
			if(liChildren.length > 0){
				$.each(liChildren, function(index, elt){
					liSize += $(elt).width();
				});
			}
			$(this).width(liSize+10);
			containerWidth += liSize+20;
		});
		if( parseInt($(ctx.qti_item_id).width()) < containerWidth){
			$(ctx.qti_item_id).width(containerWidth+'px')
							.css({'overflow-x': 'auto'});
		}
		
		$(ctx.qti_item_id+" .sizeEvaluator").remove();
	
	}
	$(ctx.qti_item_id+" .qti_choice_list"+suffixe).sortable(sortableOptions);		
	$(ctx.qti_item_id+" .qti_choice_list"+suffixe).disableSelection();
	
	// container follow the height dimensions
	$(ctx.qti_item_id).append("<div class='breaker'> </div>");
	
};


//
//GRAPHIC ORDER
//

/**
 * Create a graphic order widget 
 * where you order hot spots on a background image
 * @methodOf QTIWidget
 * @param {Object} ctx the QTIWidget context
 */
QTIWidget.graphic_order = function (ctx){

	//if the values are defined
	var list = new Array();
	if(ctx.opts["values"]){
		var values = ctx.opts["values"];
		var list = new Array(values.length);
		if(typeof(values) == 'object'){
			
			//we take the list element corresponding to the given ids 
			for(index in values){
				var value = values[index];
				var index = parseInt(index);
				if(typeof(value) == 'string' && value != ''){
					list[index + 1] = value;
				}
			}
		}
	}
	
    var maxChoices;
    if (ctx.opts["maxChoices"]==undefined){
        maxChoices = $(ctx.qti_item_id+" .qti_graphic_order_spotlist li").length;
    } else {
        maxChoices=ctx.opts["maxChoices"];
    }
	var countChoices=0;
	var displayCounter=1;
	
	var imageHeight = parseInt(ctx.opts.imageHeight);
	var imageWidth = parseInt(ctx.opts.imageWidth);
	
    // data
    var choice_obj=new Object();
    var shapes = new Object();
	
    // offset position
	$(ctx.qti_item_id+" .qti_graphic_order_spotlist li").css("display","none");
	var itemHeight= parseInt($(ctx.qti_item_id).height());
	$(ctx.qti_item_id).css("height", itemHeight + imageHeight);
	
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
		var paper=Raphael($(ctx.qti_item_id+' .svg-container')[0], imageWidth, imageHeight);
		paper.image(ctx.opts.imagePath,0,0, imageWidth, imageHeight);
		var state_obj=new Object();
        // create pickup zone
        $(ctx.qti_item_id).append("<ul class='pickup_area'></ul>");
        for (var a=1;a<=maxChoices;a++){
           $(ctx.qti_item_id+" .pickup_area").append("<li class='choice"+a+"'>"+a+"</li>");
           choice_obj["choice"+a]={selected:"none"};
        }
        // li behavior
        $(ctx.qti_item_id+" .pickup_area li").each(function(e){
            $(this).bind("click",function(e){
                    $(ctx.qti_item_id+" .pickup_area li").removeClass("selected");
                    $(this).addClass("selected");
            });
        });

        // redim container and pickup area
        $(ctx.qti_item_id+" .pickup_area").width(imageWidth-10-4);
        var pickup_area_height= parseInt($(ctx.qti_item_id+" .pickup_area").height());
        $(ctx.qti_item_id).css("height",itemHeight+imageHeight+pickup_area_height+20);
        // resize container
        $(ctx.qti_item_id).height(parseInt($(ctx.qti_item_id).height())+pickup_area_height);
		// create hotspot
		$(ctx.qti_item_id+" .qti_graphic_order_spotlist li").each(function(){
			var currentHotSpotShape=ctx.opts.graphicOrderChoices[$(this).attr("id")]["shape"];
			var currentHotSpotCoords=ctx.opts.graphicOrderChoices[$(this).attr("id")]["coords"].split(",");		
			// Depending the shape, options may vary
			switch(currentHotSpotShape){
				case "circle":				
					var currentHotSpotX=Number(currentHotSpotCoords[0]);
					var currentHotSpotY=Number(currentHotSpotCoords[1]);
					var currentHotSpotSize=currentHotSpotCoords[2];
					var currentShape=paper[currentHotSpotShape](currentHotSpotX,currentHotSpotY,currentHotSpotSize);			
					if (ctx.graphicDebug) currentShape.attr("stroke-width", "3px");
					
					break;
				case "rect":
					var currentHotSpotTopX=Number(currentHotSpotCoords[0]);
					var currentHotSpotTopY=Number(currentHotSpotCoords[1]);
					var currentHotSpotBottomX=currentHotSpotCoords[2]-currentHotSpotTopX;
					var currentHotSpotBottomY=currentHotSpotCoords[3]-currentHotSpotTopY;
					var currentShape=paper[currentHotSpotShape](currentHotSpotTopX,currentHotSpotTopY,currentHotSpotBottomX,currentHotSpotBottomY);
					if (ctx.graphicDebug) currentShape.attr("stroke-width", "3px");
								
					break;	
				case "ellipse":
					var currentHotSpotX=Number(currentHotSpotCoords[0]);
					var currentHotSpotY=Number(currentHotSpotCoords[1]);
					var currentHotSpotHradius=currentHotSpotCoords[2];
					var currentHotSpotVradius=currentHotSpotCoords[3];
					var currentShape=paper[currentHotSpotShape](currentHotSpotX,currentHotSpotY,currentHotSpotHradius,currentHotSpotVradius);	
					if (ctx.graphicDebug) currentShape.attr("stroke-width", "3px");			
								
					break;
				case "poly":
					var polyCoords=polyCoordonates(ctx.opts.graphicOrderChoices[$(this).attr("id")]["coords"]);
					var currentShape=paper["path"](polyCoords);			
					if (ctx.graphicDebug) currentShape.attr("stroke-width", "3px");			
								
				break;
			}
			
			shapes[$(this).attr("id")] = currentShape;
			
			currentShape.toFront();				
			currentShape.attr("fill", "pink");
			currentShape.attr("fill-opacity", "0");
			currentShape.attr("stroke-opacity", "0");
			
			state_obj[$(this).attr("id")]={state:"empty",order:0,numberIn:null,numberOut:null,choiceItemRef:null};
			
			if (ctx.graphicDebug) currentShape.attr("stroke-opacity", "1");
			currentShape.attr("stroke", "blue");	
			// add a reference to newly created object
			ctx.opts[currentShape.node]=$(this).attr("id");
			$(currentShape.node).bind("mousedown",{	
				zis: currentShape,
				name: $(this).attr("id")
			}, function(e){
				var elementSelected=$(ctx.qti_item_id+" .pickup_area li.selected").length;
				var displayCounter = parseInt($(ctx.qti_item_id+" .pickup_area li.selected").text());
				if (state_obj[e.data.name].state=="empty" && elementSelected==0) return;
				if (state_obj[e.data.name].state == "empty" && elementSelected==1) {
					state_obj[e.data.name].state = "filled";
					state_obj[e.data.name].order = displayCounter;
					var shapeCoordonatesWidth = e.data.zis.getBBox().width;
					var shapeCoordonatesHeight = e.data.zis.getBBox().height;
					var shapeCoordonatesX = e.data.zis.getBBox().x + (shapeCoordonatesWidth / 2);
					var shapeCoordonatesY = e.data.zis.getBBox().y + (shapeCoordonatesHeight / 2);	
					var orderInfo 	= paper.text(shapeCoordonatesX, shapeCoordonatesY, $(ctx.qti_item_id+" .pickup_area li.selected").text());
					var orderInfo1 	= paper.text(shapeCoordonatesX, shapeCoordonatesY, $(ctx.qti_item_id+" .pickup_area li.selected").text());
					state_obj[e.data.name].numberIn=orderInfo;
					state_obj[e.data.name].numberOut=orderInfo1;
                    state_obj[e.data.name].choiceItemRef=$(ctx.qti_item_id+" .pickup_area li.selected");
					orderInfo.attr("font-family", "verdana");
					orderInfo.attr("font-size", 16);
					orderInfo.attr("font-weight", "bold");
					orderInfo.attr("fill", "#009933");
					orderInfo.attr("stroke", "#009933");
					orderInfo.attr("stroke-width", "3px");
					orderInfo1.attr("font-family", "verdana");
					orderInfo1.attr("font-size", 16);
					orderInfo1.attr("font-weight", "bold");
					orderInfo1.attr("fill", "#ffffff");
					currentShape.toFront();
                    $(ctx.qti_item_id+" .pickup_area li.selected").css("visibility","hidden");
					$(ctx.qti_item_id+" .pickup_area li.selected").removeClass("selected");
				} else {
					state_obj[e.data.name].numberIn.remove();
					state_obj[e.data.name].numberOut.remove();
					state_obj[e.data.name].state="empty";
					state_obj[e.data.name].choiceItemRef.css("visibility","visible");
				}
				$(ctx.qti_item_id).data('order', state_obj);
			});	
		});
	}
	
	//trigger the event on load if the value is set
	for(var index = 0; index <list.length; index++){
		var identifier = list[index];
		if(identifier != undefined){
			var choice = $(ctx.qti_item_id+" .pickup_area li.choice"+index).addClass('selected');
			var shape = shapes[identifier];
			$(shape.node).trigger("mousedown", {
				zis:shape,
				name: identifier, 
				raphElement:shape
			});
		}
	}
};

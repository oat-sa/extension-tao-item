

function simple_choice(currentObj){	
	var qti_item_id="#"+currentObj["id"];
	$(qti_item_id).addClass('qti_simple_interaction');
	$(qti_item_id+" ul li").bind("click",function(){	
		$(qti_item_id+" ul li").removeClass("tabActive");		
		$(this).addClass("tabActive");					
	});
}

function multiple_choice(currentObj){
	var qti_item_id="#"+currentObj["id"];
	$(qti_item_id).addClass('qti_multi_interaction');
	$(qti_item_id+" ul li").bind("click",function(){
		if ($(this).hasClass("tabActive")) {
			$(this).removeClass("tabActive");
		}
		else {
			if ($(qti_item_id+" ul li.tabActive").length < currentObj["maxChoices"] || currentObj["maxChoices"] == 0) {
				$(this).addClass("tabActive");
			}
		}
	});
}

function sort(currentObj){
	var qti_item_id="#"+currentObj["id"];
	$(qti_item_id+" ul").sortable({
			revert: true,
			axis : "y",
			containment: qti_item_id
		});
		$(qti_item_id+" ul, li").disableSelection();
		
}

function associate(currentObj){
	
		var qti_item_id="#"+currentObj["id"];
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
		for (var a=currentObj["maxAssociations"];a>0;a--){
			var currentPairName=currentObj["id"]+"_"+a;
			$(qti_item_id+" .qti_associate_container").after("<ul class='qti_association_pair' id='"+currentPairName+"'><li id='"+currentPairName+"_A"+"'></li><li id='"+currentPairName+"_B"+"'></li></ul>");		
		}
		
		// create an object in the current Object of the item (passed in argument) to store 
		// infos between the words cloud and the pair list
		currentObj["link"]={};
		
		// create a label for each drop box from is id
		// setted to "null" because there is no association when item start
		$(qti_item_id+" .qti_association_pair li").each(function(){		
			currentObj["link"][$(this).attr("id")]="null";
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
				
				
				// currentObj store id of the cloud word to maintain a link
				currentObj["link"][$(this).attr("id")]=$(ui.draggable).parent().attr("id");
				
				
				if($(ui.helper).parentsUntil("ul").parent().hasClass("qti_choice_list"))
				{
					$(ui.helper).css({top:"0",left:"0"});
					var _matchMax = Number(currentObj["maxMaxes"][$(ui.draggable).parent().attr("id")]["matchMax"])
					var _current = Number(currentObj["maxMaxes"][$(ui.draggable).parent().attr("id")]["current"]);
				
					if (_current < _matchMax) {
						
						_current++;
						currentObj["maxMaxes"][$(ui.draggable).parent().attr("id")]["current"]=_current;
					}
					if (_current >= _matchMax) {
						$(ui.draggable).hide();
					}
	
				} 
						else {
								currentObj["link"][$(this).attr("id")]=currentObj["link"][$(ui.draggable).parentsUntil(".ui-droppable").parent().attr("id")];
								currentObj["link"][$(ui.draggable).parentsUntil(".ui-droppable").parent().attr("id")]="null";
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
			hoverClass: 'active_pair'
		});	
		
		
			$(qti_item_id).droppable({
				drop: function(event, ui){
					if($(ui.draggable).parentsUntil("ul").parent().hasClass("qti_choice_list"))
					{
						$(ui.draggable).css({top:"0",left:"0"});
					} else {
						var currentDroppedItemId=$(ui.draggable).parentsUntil("li").parent().attr("id");
						$(qti_item_id+" #"+currentObj["link"][currentDroppedItemId]+" div").show();
						currentObj["maxMaxes"][currentObj["link"][currentDroppedItemId]]["current"]--;
						currentObj["link"][currentDroppedItemId]="null";
					}
				},
				hoverClass: 'active_pair'
			})
				
		}
	);	
}

function text_entry(currentObj){
	var qti_item_id="#"+currentObj["id"];
	
	//adapt the field length
	if(currentObj['expectedLength']){
		length = parseInt(currentObj['expectedLength']);
		$(qti_item_id).css('width', (length * 10) + 'px')
						.attr('maxLength', length);
	}
	
	string_interaction(currentObj);
}

function extended_text(currentObj){
	
	var qti_item_id="#"+currentObj["id"];
		
	//usual case: one textarea 
	if($(qti_item_id).get(0).nodeName.toLowerCase() == 'textarea') {
		
		//adapt the field length
		if(currentObj['expectedLength'] || currentObj['expectedLines']){
			
			baseWidth 	= parseInt($(qti_item_id).css('width')) | 400;
			baseHeight 	= parseInt($(qti_item_id).css('height')) | 100;
			if(currentObj['expectedLength']){
				length 		= parseInt(currentObj['expectedLength']);
				width = length * 10;
				if( width > baseWidth){
					height = (width / baseWidth) * 16;
					if(height  > baseHeight){
						$(qti_item_id).css('height', height + 'px');
					}
				}
				$(qti_item_id).attr('maxLength', length);
			}
			if(currentObj['expectedLines']){
				$(qti_item_id).css('height', (parseInt(currentObj['expectedLines']) * 16) + 'px');
			}
		}
	
		string_interaction(currentObj);
	}
	
	//multiple text inputs
	if($(qti_item_id).get(0).nodeName.toLowerCase() == 'div') {
		//adapt the fields length
		if(currentObj['expectedLength']){
			length = parseInt(currentObj['expectedLength']);
			$(qti_item_id + " :text").css('width', (length * 10) + 'px')
										.attr('maxLength', length);
		}
		//apply the pattern to all fields
		if(currentObj['patternMask']){
			var pattern = new RegExp("/^"+currentObj['patternMask']+"$/");
			$(qti_item_id  + " :text").change(function(){
				$(this).removeClass('field-error');
				if(!pattern.test($(this).val())){
					$(this).addClass('field-error');
				}
			});
		}
	}
}

function string_interaction(currentObj){
	
	var qti_item_id="#"+currentObj["id"];
	
	//add the error class if the value don't match the given pattern
	if(currentObj['patternMask']){
		var pattern = new RegExp("/^"+currentObj['patternMask']+"$/");
		$(qti_item_id).change(function(){
			$(this).removeClass('field-error');
			if(!pattern.test($(this).val())){
				$(this).addClass('field-error');
			}
		});
	}
	
	//create a 2nd field to capture the string if the stringIdentifier has been defined
	if(currentObj['stringIdentifier']){
		$(qti_item_id).after("<input type='hidden' id='"+currentObj['stringIdentifier']+"' />");
		$("#"+currentObj['stringIdentifier']).addClass('qti_text_entry_interaction');
		$(qti_item_id).change(function(){
			$("#"+currentObj['stringIdentifier']).val($(this).val());
		});
	}
}

function qti_init(){
	for (var a in qti_initParam){
		qti_init_items(qti_initParam[a]);
	}
}


function qti_init_items(initObj){
	
	var resultMethod = null;
	var initObj = initObj;
	
	switch (initObj["type"]) {
		case "qti_choice_interaction":
			var maxChoices =  parseInt(initObj["maxChoices"]);
			if(maxChoices > 1 || maxChoices == 0){
				multiple_choice(initObj);
			}
			else{
				simple_choice(initObj);
			}
			resultMethod = choice_interaction_result;
			break;
		case "qti_order_interaction":
			sort(initObj);
			resultMethod = order_interaction_result;
			break;
		case "qti_associate_interaction":
			associate(initObj);
			resultMethod = associate_interaction_result;
			break;
		case "qti_text_entry_interaction":
			text_entry(initObj);
			resultMethod = text_result;
			break;
		case "qti_extended_text_interaction":
			extended_text(initObj);
			resultMethod = text_result;
			break;
	}
	
	// validation process
	$("#qti_validate").bind("click",function(){
		console.log(resultMethod(initObj['id']));
	});
		
}

// result process
function choice_interaction_result(id){
	var result = new Array();
	$("#" + id + " .tabActive").each(function(){
		result.push(this.id);
	});
	return result;
}

function order_interaction_result(id){
	var result = new Array();
	$("#" + id + " ul.qti_choice_list li").each(function(){
		result.push(this.id);
	});
	return result;
}

function associate_interaction_result(id){
	var result = new Array();
	$("#" + id + " .qti_association_pair").each(function(){
		result.push([$(this).find('li:first').attr('id'), $(this).find('li:last').attr('id')]);
	});
	return result;
}

function text_result(id){
	
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
}
/**
 * 
 * @param {String} params
 * @return {Object}
 */
function unserializeUrl(params){
    var data = {};
    var hashes = params.split('&');
    for(i in hashes){
    	var hash = hashes[i].split('=');
        if(hash.length == 2){
        	if(/\[\]$/.test(hash[0])){
        		var key = hash[0].replace(/\[\]$/, '');
        		if(typeof(data[key]) == "undefined"){
        			data[key] = [];
        		}
        		data[key].push(hash[1]);
        	}
        	else{
        		data[hash[0]] = hash[1];
        	}
        }
    }
    return data;
}


$(document).ready(function(){
	var timer = new Date();
	
	var previewConsole = $('#preview-console', window.top.document);
	
	//attach an updateConsole event
	previewConsole.bind('updateConsole', function(event, type, message){
		var hour = timer.getHours() + ':' + ((timer.getMinutes() > 10) ? timer.getMinutes() : '0' +timer.getMinutes());
		$(this).find('ul').append('<li><b>[' +  hour  + '] ' + type + '</b>: ' + message + '</li>');
	});
	
	//controls: close console
	previewConsole.find(".console-control .ui-icon-circle-close").bind('click', function(){
		previewConsole.unbind('updateConsole').hide();
		return false;
	});
	
	//controls: clean console
	previewConsole.find(".console-control .ui-icon-trash").bind('click', function(){
		previewConsole.find('div.console-content').empty();
		return false;
	});
	
	//controls: show/hide console
	previewConsole.find(".console-control .toggler").bind('click', function(e){
		previewConsole.find('div.console-content').toggle();
		if($(this).hasClass('ui-icon-circle-minus')){
			previewConsole.height('16px');
			$(this).switchClass('ui-icon-circle-minus', 'ui-icon-circle-plus');
		}
		else{
			previewConsole.height('150px');
			$(this).switchClass('ui-icon-circle-plus', 'ui-icon-circle-minus');
		}
		return false;
	});
	
	//log in the console the ajax request to the Preview Api
	$('body').ajaxSuccess(function(event,request, settings){
		if(/PreviewApi/.test(settings.url)){
			
			var message = '';
			//taoApi Push
			if(/save$/.test(settings.url)){
				var data = unserializeUrl(decodeURIComponent(settings.data));
				for(key in data){
					if(key != 'token'){
						message += key + ' => '  + data[key] +'<br />';
					}
				}
				if(message != ''){
					previewConsole.trigger('updateConsole', ['push data', message]);
				}
			}
			
			//taoApi events
			else if(/traceEvents$/.test(settings.url)){
				var data = unserializeUrl(decodeURIComponent(settings.data));
				if(data.events){
					for(index in data.events){
						try{
							var eventData = $.parseJSON(data.events[index]);
							message += '<br />' + eventData.type + ' on element ' + eventData.name;
							if(eventData.id != 'noID'){
								message += '[id=' + eventData.id +']';
							}
						}catch(exp){ 
							console.log(exp);
						}
					}
					message += '<br />'
					previewConsole.trigger('updateConsole', ['trace events', message]);
				}
			}
			
			//taoMatching data
			else if(/evaluate$/.test(settings.url)){
				var data = unserializeUrl(decodeURIComponent(settings.data));
				if(data.data){
					try{
						var matchingDataList = $.parseJSON(data.data);
						for(i in matchingDataList){
							for(key in matchingDataList[i]){
								message += '<br />' + key + ' = ' + matchingDataList[i][key];
							}
						}
					}catch(exp){ 
						console.log(exp);
					}
					message += '<br />'
					previewConsole.trigger('updateConsole', ['sent answers', message]);
				}
			}
			
			//others requests
			else{
				message = settings.type + ' ' + 
							settings.url + ' ? ' + 
							decodeURIComponent(settings.data) + ' => ' +
							request.responseText;
				previewConsole.trigger('updateConsole', ['remote request', message]);
			}
		}
	});
	
	afterFinish(function(){
		previewConsole.trigger('updateConsole', ['state', 'item is now finished!']);
	});
	
});
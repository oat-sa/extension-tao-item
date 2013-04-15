/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
/**
 * Get an url-formated parameters string, parse it and return a JSONized object
 * @param {String} params
 * @return {Object}
 */
function unserializeUrl(params){
    var data = {};
    var hashes = params.split('&');
    for(var i in hashes){
		if(typeof hashes[i] == 'string'){
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
    }
    return data;
}


$(document).ready(function(){
	var timer = new Date();
	
	//get the console element in the top page
	var previewConsole = $('#preview-console', window.top.document);
	
	//attach an updateConsole event, tobe triggered
	previewConsole.bind('updateConsole', function(event, type, message){
		var hour = timer.getHours();
		var min	 = timer.getMinutes();
		var sec	 = timer.getSeconds();
		var logTime = ((hour>10)? hour : '0'+hour ) + ':' + ((min>10)? min : '0'+min) + ':' + ((sec>10)? sec: '0'+sec);
		$(this).find('ul').append('<li><b>[' +  logTime  + '] ' + type + '</b>: ' + message + '</li>');
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
			$(this).removeClass('ui-icon-circle-minus').addClass('ui-icon-circle-plus');
		}
		else{
			previewConsole.height('150px');
			$(this).removeClass('ui-icon-circle-plus').addClass('ui-icon-circle-minus');
		}
		return false;
	});
	
	//log in the console the ajax request to the Preview Api
	$('body').ajaxSuccess(function(event, request, settings){
		
		if(/PreviewApi/.test(settings.url)){
			
			var message = '';
			//taoApi Push
			if(/save$/.test(settings.url)){
				var data = unserializeUrl(decodeURIComponent(settings.data));
				for(var key in data){
					if(key != 'token'){
						message += '<br />' + key + ' = '  + data[key] ;
					}
				}
				if(message != ''){
					message += '<br />';
					previewConsole.trigger('updateConsole', ['push data', message]);
				}
			}
			
			//taoApi events
			else if(/traceEvents$/.test(settings.url)){
				var data = unserializeUrl(decodeURIComponent(settings.data));
				if(data.events){
					for(var index in data.events){
						try{
							var eventData = $.parseJSON(data.events[index]);
							message += '<br />' + eventData.type + ' on element ' + eventData.name;
							if(eventData.id != 'noID'){
								message += '[id=' + eventData.id +']';
							}
						}catch(exp){ }
					}
					message += '<br />';
					previewConsole.trigger('updateConsole', ['trace events', message]);
				}
			}
			
			//wfApi saving context 
			else if(/saveContext$/.test(settings.url)){
				var data = unserializeUrl(decodeURIComponent(settings.data));
					for(key in data){
						if(key != 'token'){
							message += '<br />' + key + ' = ' + data[key];
						}
					}
					message += '<br />';
					previewConsole.trigger('updateConsole', ['save context', message]);				
			}
			
			//wfApi retrieve context 
			else if(/retrieveContext$/.test(settings.url)){
				var data = unserializeUrl(decodeURIComponent(settings.data));
					for(key in data){
						if(key != 'token'){
							message += '<br />' + key + ' = ' + data[key];
						}
					}
					message += '<br />';
					previewConsole.trigger('updateConsole', ['retrieved context', message]);				
			}
			
			//taoMatching data
			else if(/evaluate$/.test(settings.url)){
				var data = unserializeUrl(decodeURIComponent(settings.data));
				if(data.data){
					try{
						var matchingDataList = $.parseJSON(data.data);
						for(key in matchingDataList){
							message += '<br />' + matchingDataList[key]['identifier'] + ' = ' + matchingDataList[key]['value'];
						}
					}catch(exp){ 
						//console.log(exp);
					}
					message += '<br />';
					previewConsole.trigger('updateConsole', ['sent answers', message]);
				}
			}
			
			//others requests
			else{
				message = 	'<br />' + 
							settings.type + ' ' + 
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
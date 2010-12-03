$(function (){

	if(typeof(initPush) != 'undefined'){
	
	var <?=get_data('envVarName')?> = <?=get_data('executionEnvironment')?>;
	initManualDataSource(<?=get_data('envVarName')?>);

	initPush(<?=get_data('pushParams')?>, null);
	
	<?if(get_data('eventData')):?>
		initEventServices({ type: 'manual', data: <?=get_data('eventData')?>}, <?=get_data('eventParams')?>);
	<?endif?>
	
	}
	
	if(typeof(matchingInit) != 'undefined'){
	
	<?if(get_data('matchingServer')):?>
		var matchingParam = $.extend(<?=get_data('matchingParams')?>, {
		    "format" : "json", 
		    "options" : {
		        "evaluateCallback" : function (outcomes) {
		        	
		        	var strOutcomes = '';
		            for (var outcomeKey in outcomes){
		                strOutcomes += '[ ' + outcomeKey+ ' = ' +outcomes[outcomeKey]['value'] + ' ]';
		            }
		            
		           if($('#preview-console', window.top.document).length > 0){
			             //display the outcomes in the main window
			           	window.top.createInfoMessage ('THE OUTCOME VALUES : <br/>'  + strOutcomes);
			            
			            //and in the preview console
			            $('#preview-console', window.top.document).trigger('updateConsole', ['outcomes', strOutcomes]);
		           	}
		           	else{
		           		//outside preview container
		           		alert(strOutcomes);
		           	}
		           
		           	// Reset the matching engine
		            matchingInit (matchingParam);
		            
		            // Finish the process
		            finish();
		        }
		    }
		});
	<?else:?>
		var matchingParam = {
		    "data" : <?=get_data('matchingData')?>
		    , "format" : "json"
		    , "options" : {
		        "evaluateCallback" : function () {
		            var outcomes = matchingGetOutcomes();
		            var strOutcomes = '';
		            for (var outcomeKey in outcomes){
		                strOutcomes += '[ ' + outcomeKey+ ' = ' +outcomes[outcomeKey]['value'] + ' ]';
		            }
		            
		            if($('#preview-console', window.top.document).length > 0){
			           //display the outcomes in the main window
			           window.top.createInfoMessage ('THE OUTCOME VALUES : <br/>'  + strOutcomes);
			           
			           //and in the preview console
			           $('#preview-console', window.top.document).trigger('updateConsole', ['outcomes', strOutcomes]);
		           }
		           else{
		           		//outside preview container
		           		alert(strOutcomes);
		           	}
		           
		           // Reset the matching engine
		           matchingInit (matchingParam);
		           // Finish the process
		            finish();
		        }
		    }
		};
	<?endif?>
	
		matchingInit(matchingParam);
	}
	
	if(typeof(recoveryCtx) != 'undefined'){
	<?if(get_data('disableContext') === true):?>
		recoveryCtx.enabled = false;
	<?else:?>
		initRecoveryContext(<?=get_data('contextSourceParams')?>, <?=get_data('contextDestinationParams')?>);
	<?endif?>
	}
});
matchingParam = {
    "data" : <?=json_encode($data)?>
    , "format" : "json"
    , "options" : {
        "evaluateCallback" : function () {
            var outcomes = matchingGetOutcomes();
            strOutcomes = '';
            for (var outcomeKey in outcomes){
                strOutcomes += '[ ' + outcomeKey+ ' = ' +outcomes[outcomeKey]['value'] + ' ]';
            }
            window.top.helpers.createInfoMessage('THE OUTCOME VALUES : <br/>'  + strOutcomes);
            // Reset the matching engine
            matchingInit (matchingParam);
            // Finish the process
            finish();
        }
    }
};

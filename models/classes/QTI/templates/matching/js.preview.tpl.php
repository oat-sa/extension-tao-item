matchingParam = {
    "data" : <?=json_encode($data)?>
    , "format" : "json"
    , "options" : {
        "evaluateCallback" : function () {
            var outcomes = matchingGetOutcomes();
            strOutcomes = '';
            for (var outcomeKey in outcomes){
                strOutcomes += outcomeKey+ ' = ' +outcomes[outcomeKey]['value'] + '<br/>';
            }
            window.top.createInfoMessage ('THE OUTCOME VALUES : <br/> '  + strOutcomes);
            finish();
        }
    }
};

// validation process (special preview)
$("#qti_validate").bind("click",function(){
    // Reinit the matching engine
    matchingInit (matchingParam);
});

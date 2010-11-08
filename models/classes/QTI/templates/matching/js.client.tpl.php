matchingParam = {
    "data" : <?=json_encode($data)?>
    , "format" : "json"
    , "options" : {
        "evaluateCallback" : function () {
            var outcomes = matchingGetOutcomes();
            console.log ('THE OUTCOME VALUE SCORE IS : '  + outcomes['SCORE']['value']);
            finish();
        }
    }
};

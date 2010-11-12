matchingParam = {
    "data" : <?=json_encode($data)?>
    , "format" : "json"
    , "options" : {
        "evaluateCallback" : function () {
            var outcomes = matchingGetOutcomes();
            window.top.createInfoMessage ('THE OUTCOME VALUE SCORE IS : '  + outcomes['SCORE']['value']);
            finish();
        }
    }
};

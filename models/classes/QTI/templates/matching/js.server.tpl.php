matchingParam = {
    "url" : "<?=$ctx_root_url.'/'.$url?>"
    , "params" : { 
         "token" : getToken()
    }
    , "format" : "json"
    , "options" : {
        "evaluateCallback" : function () {
            var outcomes = matchingGetOutcomes();
            console.log ('THE OUTCOME VALUE SCORE IS : '  + outcomes['SCORE']['value']);
            finish();
        }
    }
};

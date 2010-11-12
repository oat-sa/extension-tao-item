matchingParam = {
    "url" : "<?=$ctx_root_url.'/'.$url?>"
    , "params" : { 
         "token" : getToken()
    }
    , "format" : "json"
    , "options" : {
        "evaluateCallback" : function () {
            finish();
        }
    }
};

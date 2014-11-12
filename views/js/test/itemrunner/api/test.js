define(['jquery', 'taoItems/runtime/itemRunner'], function($, itemRunner){
        
    module('API');
   
    test('module', function(){
        ok(typeof itemRunner !== 'undefined', "The module exports something");
        ok(typeof itemRunner === 'function', "The module exports a function");
    });

    test('error', function(){
        expect(1);

        throws(function(){
            itemRunner();
            throw new Error();
        }, Error, 'An error is thrown');
    });

    asyncTest('timeout', function(){
        expect(1);
        setTimeout(function(){
            ok(true);
            start();
        }, 1);
    });
});



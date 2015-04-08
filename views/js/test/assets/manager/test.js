define([
    'taoItems/assets/manager'
], function(assetManager){


    QUnit.module('API');

    QUnit.test('module', 6, function(assert){
        assert.ok(typeof assetManager !== 'undefined', "The module exports something");
        assert.ok(typeof assetManager === 'object', "The module exports an object");
        assert.ok(typeof assetManager.addStragegy === 'function', "The manager has a method addStragegy");
        assert.ok(typeof assetManager.createContext === 'function', "The manager has a method createContext");
        assert.ok(typeof assetManager.resolve === 'function', "The manager has a method resolve");
        assert.ok(typeof assetManager.resolveBy === 'function', "The manager has a method resolveBy");
    });

    QUnit.module('Strategy', {
        teardown: function(){
            console.log('before');
            assetManager.strategies = [];
        }
    });

    QUnit.test('expected strategy format', 5, function(assert){

        assert.throws(function(){
            assetManager.addStragegy(null);
        }, TypeError, 'The strategy must be an object');

        assert.throws(function(){
            assetManager.addStragegy({
                foo : true
            });
        }, TypeError, 'The strategy must have a name');

        assert.throws(function(){
            assetManager.addStragegy({
                name : 'foo'
            });
        }, TypeError, 'The strategy must have a handle method');

        assert.throws(function(){
            assetManager.addStragegies([{
               name : null
            }]);
        }, TypeError, 'The strategy must have a name');

        var strategy = {
            name : 'foo',
            handle : function(){}
        };

        assetManager.addStragegy(strategy);

        assert.equal(assetManager.strategies[0].name, strategy.name, 'The strategy has been added');
    });


    QUnit.test('strategy resolution', 3, function(assert){

        var strategy = {
            name : 'foo',
            handle : function(path, data){
                return 'foo' + path ;
            }
        };

        assetManager.addStragegy(strategy);
        assert.equal(assetManager.strategies.length, 1, 'There is one strategy');
        assert.equal(assetManager.strategies[0].name, strategy.name, 'The strategy has been added');

        var result = assetManager.resolve('bar');
        assert.equal(result, 'foobar', 'The strategy has resolved');
    });

    QUnit.test('multiple strategies resolution', 6, function(assert){


        assetManager.addStragegies([{
            name : 'foo',
            handle : function(path, data){
                if(path === 'far'){
                    return 'foo' + path ;
                }
            }
        }, {
            name : 'boo',
            handle : function(path, data){
                if(path === 'bar'){
                    return 'boo' + path ;
                }
            }
        }]);


        assert.equal(assetManager.strategies.length, 2, 'There are 2 strategies');
        assert.equal(assetManager.strategies[0].name, 'foo', 'The foo strategy has been added');
        assert.equal(assetManager.strategies[1].name, 'boo', 'The boo strategy has been added');

        var res1 = assetManager.resolve('far');
        assert.equal(res1, 'foofar', 'The path is resolved by foo');

        var res2 = assetManager.resolve('bar');
        assert.equal(res2, 'boobar', 'The path is resolved by boo');

        assetManager.resolveBy('foo', 'far');

        var res3 = assetManager.resolve('moo');
        assert.equal(res3, undefined, 'The path is not resolved');


    });

    QUnit.test('anonymous strategies', 4, function(assert){


        assetManager.addStragegies([
        function(path, data){
            if(path === 'far'){
                return 'foo' + path ;
            }
        }, function(path, data){
            if(path === 'bar'){
                return 'boo' + path ;
            }
        }]);


        assert.equal(assetManager.strategies.length, 2, 'There are 2 strategies');

        var res1 = assetManager.resolve('far');
        assert.equal(res1, 'foofar', 'The path is resolved by foo');

        var res2 = assetManager.resolve('bar');
        assert.equal(res2, 'boobar', 'The path is resolved by boo');

        assetManager.resolveBy('foo', 'far');

        var res3 = assetManager.resolve('moo');
        assert.equal(res3, undefined, 'The path is not resolved');


    });
    QUnit.test('create a context', 6, function(assert){

        var base = "http://t.ao/";

        assetManager.addStragegy({
            name : 'foo',
            handle : function(path, data){
                return  data.base + path ;
            }
        });

        assert.equal(assetManager.strategies.length, 1, 'There is one strategy');
        assert.equal(assetManager.strategies[0].name, 'foo', 'The strategy has been added');

        var ctx = assetManager.createContext({ base : base });

        assert.ok(typeof ctx === 'object', 'The context is an object');
        assert.ok(typeof ctx.resolve === 'function', 'The context exposes a resolve function');
        assert.ok(typeof ctx.resolveBy === 'function', 'The context exposes a resolveBy function');


        var path1 = 'bar.html';
        var res1 = ctx.resolve('bar.html');
        assert.equal(res1, base + path1 , 'The path is resolved');

    });

});


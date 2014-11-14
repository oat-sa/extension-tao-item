define(['jquery', 'taoItems/runner/api/itemRunner', 'taoItems/test/runner/provider/dummyProvider'], function($, itemRunner, dummyProvider){
  

    QUnit.module('API');
   
    QUnit.test('module', function(assert){
        assert.ok(typeof itemRunner !== 'undefined', "The module exports something");
        assert.ok(typeof itemRunner === 'function', "The module exports a function");
        assert.ok(typeof itemRunner.register === 'function', "The function has a property function register");
    });

    QUnit.module('Register a Provider', {
        teardown : function(){
            //reset the provides
            itemRunner.providers = undefined;
        }
    });

    QUnit.test('Error without provider', function(assert){
        QUnit.expect(1);

        assert.throws(function(){
            itemRunner();
        }, Error, 'An error is thrown');
    });

    QUnit.test('Error with a wrong provider', function(assert){
        QUnit.expect(2);

        assert.throws(function(){
            itemRunner.register('');
        }, TypeError, 'A name is expected');
        
        assert.throws(function(){
            itemRunner.register('testProvider', {});
        }, TypeError, 'A name is expected');
    });

    QUnit.test('Register a minimal provider', function(assert){
        QUnit.expect(4);

        assert.ok(typeof itemRunner.providers === 'undefined', "the itemRunner comes without a provider");

        itemRunner.register('testProvider', {
            init : function(){
            }
        });
        itemRunner();

        assert.ok(typeof itemRunner.providers === 'object', 'The providers property is defined');
        assert.ok(typeof itemRunner.providers.testProvider === 'object', 'The testProvider is set');
        assert.ok(typeof itemRunner.providers.testProvider.init === 'function', 'The testProvider has an init function');
    });

    module('ItemRunner init', {
        teardown : function(){
            //reset the provides
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('Initialize the runner', function(assert){
        QUnit.expect(2);
        
        itemRunner.register('dummyProvider', dummyProvider);

        itemRunner('dummyProvider', {
            type: 'number',
            state : {value : 0 }
        }).on('init', function(){
            
            assert.ok(typeof this._data === 'object', 'the itemRunner context got the data assigned');
            assert.equal(this._data.type, 'number', 'the itemRunner context got the right data assigned');

            QUnit.start();
        }).init();
    });

    QUnit.asyncTest('Initialize the item with new data', function(assert){
        QUnit.expect(2);
        
        itemRunner.register('dummyProvider', dummyProvider);

        itemRunner('dummyProvider', {
            type: 'number',
            state : {value : 0 }
        }).on('init', function(){
            
            assert.ok(typeof this._data === 'object', 'the itemRunner context got the data assigned');
            assert.equal(this._data.type, 'text', 'the itemRunner context got the right data assigned');

            QUnit.start();
        }).init({
            type : 'text'
        });
    });

    module('ItemRunner render', {
        teardown : function(){
            //reset the provides
            itemRunner.providers = undefined;
        }
    });

    QUnit.asyncTest('Render an item', function(assert){
        QUnit.expect(4);
       
        var container = document.getElementById('item-container');
        assert.equal(container.id, 'item-container', 'the item container exists');
 
        itemRunner.register('dummyProvider', dummyProvider);

        var runner = itemRunner('dummyProvider', {
            type: 'number',
            state : {value : 5 }
        }).on('ready', function(){
            
            assert.ok(typeof this._data === 'object', 'the itemRunner context got the data assigned');
            assert.equal(this._data.type, 'number', 'the itemRunner context got the right data assigned');

            QUnit.start();
        })
        .init();

        assert.throws(function(){
            runner.render();
        }, TypeError, 'the render must be called with a DOM Element');


        runner.render(container);
    });
});



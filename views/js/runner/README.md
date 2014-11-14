# Runner quick overview

> The API is still to be comfirmed. There should'nt be major changes expect for the `init` and the `ready` event.

## Concept
 
The `ItemRunner` is the public API. A TestRunner calls the `ItemRunner` with itemData to render it and manage it's lifecycle.

It works in 2 steps:

1. Register a provider for the item type it will render. (This step is done only once, until the page is reloaded)

```
+----------------+             +--------------------+
|                |   register  |                    |
|   ItemRunner   <------+------+ QtiRuntimeProvider |
|                |1,n   |      |                    |
+----------------+      |      +--------------------+
                        |                            
                        |      +--------------------+
                        |      |                    |
                        +------+ OWIRuntimeProvider |
                        |      |                    |
                        |      +--------------------+
                        |                            
                        |      +--------------------+
                        |      |                    |
                        +------+ SomeOtherProvider  |
                               |                    |
                               +--------------------+
```
   
2. Create an `ItemRunner` instance for each item to render.

```
+--------------------------------------------------+               +----------------------------------------------+
|    ItemRunner                                    |               |    Provider                                  |
|--------------------------------------------------|               |----------------------------------------------|
|    ItemRunner : construct(Object itemData)       |               |                                              |
|                                                  |  delegates    |                                              |
|    ItemRunner : init()                         +------------------->  void   : init(Object data, Func done)     |
|    ItemRunner : render(HTMLElement elt)        +------------------->  void   :render(HTMLElement elt, Func done)|
|    Object     : getState()                     +------------------->  Object : getState()                       |
|    ItemRunner : setState(Object state)         +------------------->  void   :setState(Object state)            |
|    Array      : getResponses()                 +------------------->  Array  : getResponses()                   |
|    ItemRunner : setResponses(Array responses)  +------------------->  void   :setResponses(Array responses)     |
|                                                  |               |                                              |
|    ItemRunner : on(event,Func handler)           |               |                                              |
|    ItemRunner : off(event)                       |               |                                              |
|    ItemRunner : trigger(event)                   |               |                                              |
+--------------------------------------------------+               +----------------------------------------------+
```

## Sample

### Register a provider

```javascript
define(['itemRunner', 'qtiRuntimeProvider'], function(itemRunner, qtiRuntimeProvider){
    itemRunner.register('qti', qtiRuntimeProvider);
});
```


### Manipulate the itemRunner

Once the provider has been registered.

```javascript
define(['itemRunner'], function(itemRunner){

    var itemData = {
        //an object that represents the item
    };    

    var initialState = {
        //an object with item current state
    };

                                        //itemRunner is a factory that creates a chainable instance.
    itemRunner('qti', itemData)         //qti is the name of the provider registered previously 

        .on('init', function(){         //if the initialization is asynchronous it's better to render once init is done
            this.render(document.getElementById('item-container'));
        })

        .on('ready', function(){       //the user can start working, you can hide the loader, start a timer, etc.
            var self = this;           //here this is the item runner, so you have access to getState, getResponses, etc.

            //you can implement here the previous/next features, for example
            document.getElementById('next').addEventListener('click', function(){
                self.getResponses();    //store the responses
                self.getState();        //store the state
                //forward to next item.
            });
        })

        .on('statechange', function(state){
            //oh something has changed in the item, you can store the state.
        })

        .on('responsechange', function(response){
            //oh something the response has changed
        })

        .setState(initialState)

        .init();    //let's start
});
```

## Test

Run in your browser, from a valid TAO distribution following the test case: `http://{TAO_HOST}/taoItems/views/js/test/runner/api/test.html?coverage=true` 

## Build

_TBD_

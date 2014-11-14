# Runner quick overview

> The API isn't yet stable. There should'nt be major changes expect for the `init` and the `ready` event.

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
        //some JSON data
    };    


    itemRunner('qti', itemData)
        .on('statechange', function(state){
            //oh something has changed in the item, I'll store the state.
        })
        .on('ready', function(){
            var self = this;

            //the user can start working, you can hide the loader, start a timer, etc.
            
            //here this is the item runner, so you have access to getState, getResponses, etc.

            //or implement the next feature
            document.getElementById('next').addEventListener('click', function(){
                self.getResponses();    //store the responses
                self.getState();        //store the state
                //forward to next item.
            });
        })
        .init()
        .render(document.getElementById('item-container'));

   
});
```

## JsDoc


## Test



## Build

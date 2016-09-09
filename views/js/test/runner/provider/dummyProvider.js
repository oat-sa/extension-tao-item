define(['lodash'], function(_){
    'use strict';

    /**
     * Use for test  mocking.
     * Create a dummy provider that runs input like items (just the input tag)
     */
    var dummyItemRuntimeProvider = {

        init : function(data, done){
            this._data = data;
            done();
        },

        render : function(elt, done){
            var self = this;
            var input;
            var type = this._data.type || 'text';
            var val  = this._data.value || '';

            elt.innerHTML = '<input type="' + type  +'" value="'  + val + '"/>';
            input = elt.querySelector('input');
            input.addEventListener('change', function(){
                self.trigger('statechange', { value : input.value });
            });

            done();
        },

        clear : function(elt, done){
            elt.innerHTML = '';
            done();
        },

        getState : function(){
            var state = {
                value : null
            };
            var input = this.container.querySelector('input');
            if(input){
                state.value = input.value;
            }
            return state;
        },

        setState : function(state){
            var input = this.container.querySelector('input');
            if(input && state && typeof state.value !== 'undefined'){
                input.value = state.value;
            }
        },

        getResponses : function(){
            var responses = [];
            var input = this.container.querySelector('input');
            if(input){
                responses.push(input.value);
            }
            return responses;
        },

        renderFeedbacks: function(feedbacks, itemSession, done){
            var renderingQueue = [];

            _.forEach(feedbacks, function(val, key){
                if (itemSession.indexOf(key) === -1) {
                    return true;//continue with next feedback
                }
                renderingQueue.push(val);
            });

            done(renderingQueue);
        }
    };

    return dummyItemRuntimeProvider;
});

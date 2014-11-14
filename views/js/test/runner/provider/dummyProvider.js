define(function(){

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
            var type = this._data.type || 'text';

            elt.innerHTML = '<input type="' + type  +'"/>';
            elt.addEventListener('change', function(){
                self.trigger('statechange', { value : elt.value });
            });
            this.container = elt;
            done();
        },

        getState : function(){
            return { value : this.container.value };
        },

        setState : function(state){
            this.container.value = state.value; 
        },

        getResponses : function(){
            return [this.container.value];
        },

        setResponses : function(responses){
            this.setState({value : responses[0]});
        }
    };

    return dummyItemRuntimeProvider;
});

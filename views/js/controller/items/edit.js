
define(['module', 'layout/actions', 'jquery','helpers','ui/lock'],
	function(module, actions, $, helpers, lock){

        var editItemController = {
            start : function(options){
                var config = module.config();
        
                var isPreviewEnabled = !!config.isPreviewEnabled;
                var isAuthoringEnabled = !!config.isAuthoringEnabled;
   
                var previewAction = actions.getBy('item-preview');
                var authoringAction = actions.getBy('item-authoring');
               
                if(previewAction){ 
                    previewAction.state.disabled = !config.isPreviewEnabled;
                }
                if(authoringAction){
                    authoringAction.state.disabled = !config.isAuthoringEnabled;
                }
                actions.updateState();
                
                if(config.msg !== false){
                    var lk = lock($('#lock-box')).hasLock(config.msg,
                        {
                            released : function(){
                                console.log('released');
                                this.close();
                            },
                            failed : function(){
                                console.log('failed');
                                this.close();
                            },
                            url: helpers._url('release','Lock','tao'),
                            uri: config.uri
                        });
                    $('#release').on('click',function(){
                        lk.release();
                    });
                    $('#lock-box').on('released-lock', function(){
                        console.log('released');
                    });
                }
            }
        };

        return editItemController;
});


define(['module', 'layout/actions'], function(module, actions){

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
            }
        };

        return editItemController;
});

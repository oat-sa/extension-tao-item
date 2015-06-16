
define(['module', 'layout/actions', 'jquery','helpers','ui/lock', 'ui/feedback', 'i18n'],
	function(module, actions, $, helpers, lock, feedback, __){

        var editItemController = {
            start : function(){
                var config = module.config();
                var $lockBox = $('#lock-box');

                var previewAction = actions.getBy('item-preview');
                var authoringAction = actions.getBy('item-authoring');

                //if there is no lock display a message on enter authoring
                if(config.checkoutMessage !== '' && $lockBox.length === 0){
                    $('#item-authoring').on('click',function(){
                        feedback().success(config.checkoutMessage, {
                            timeout: {
                                success: 4000
                            }});
                    });
                }

                if(previewAction){
                    previewAction.state.disabled = !config.isPreviewEnabled;
                }
                if(authoringAction){
                    authoringAction.state.disabled = !config.isAuthoringEnabled;
                }
                actions.updateState();

                $lockBox.each(function() {lock($(this)).register()});
            }
        };

        return editItemController;
});

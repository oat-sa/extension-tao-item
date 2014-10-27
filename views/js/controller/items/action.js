
define([
    'layout/actions/binder',
    'uri',
    'taoItems/preview/preview'
], function(binder, uri, preview){

    binder.register('itemPreview', function itemPreview(actionContext){
        // ajax to server \taoItems_actions_ItemPreview::exposePreviewUrl?uri=actionContext.uri
        preview.init(uri.decode(actionContext.uri));
        preview.show();
    });

});
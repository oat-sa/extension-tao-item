define([
    'layout/actions/binder',
    'uri',
    'jquery',
    'context',
    'taoItems/preview/preview'
], function(binder, uri, $, context, preview){

    binder.register('itemPreview', function itemPreview(actionContext){
        console.log(context.root_url + context.shownExtension + '/ItemPreview/forwardMe?uri=' + actionContext.uri)
        preview.init(context.root_url + context.shownExtension + '/ItemPreview/forwardMe?uri=' + actionContext.uri);
        preview.show();
    });

});

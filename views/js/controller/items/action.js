define([
    'layout/actions/binder',
    'uri',
    'jquery',
    'context',
    'taoItems/preview/preview'
], function(binder, uri, $, context, preview){

    binder.register('itemPreview', function itemPreview(actionContext){
        console.log(context.root_url + context.shownExtension + '/ItemPreview/exposePreviewUrl?uri=' + actionContext.uri)
        preview.init(context.root_url + context.shownExtension + '/ItemPreview/exposePreviewUrl?uri=' + actionContext.uri);
        preview.show();
    });

});

//src="http://tao.lan/taoQtiItem/QtiPreview/render/aHR0cDovL3Rhby5sYW4vdGFvLnJkZiNpMTQxMzc4ODMwOTUzMjY1/index?serviceCallId=preview&clientConfigUrl=http%3A%2F%2Ftao.lan%2Ftao%2FClientConfig%2Fconfig%3Fextension%3DtaoQtiItem%26module%3DQtiPreview%26action%3Dindex"

//http://tao.lan/taoQtiItem/QtiPreview/index?uri=http%3A%2F%2Ftao.lan%2Ftao.rdf%23i141378830953265&lang=
//http://tao.lan/taoQtiItem/QtiPreview/render/aHR0cDovL3Rhby5sYW4vdGFvLnJkZiNpMTQxMzc4ODMwOTUzMjY1/index

//"http://tao.lan/taoItems/ItemPreview/exposePreviewUrl?uri=http_2_tao_0_lan_1_tao_0_rdf_3_i141378830953265"
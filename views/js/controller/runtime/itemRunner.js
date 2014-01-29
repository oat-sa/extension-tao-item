/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
define(['jquery', 'lodash', 'iframeResizer', 'iframeNotifier', 'urlParser'], 
        function($, _, iframeResizer, iframeNotifier, UrlParser){
    
    var itemRunner = {
        start : function(options){
           
            var $frame = $('#item-container');
            
            var itemId = options.itemId;
            var itemPath = options.itemPath;
            var resultServer = _.defaults(options.resultServer, {
                module : 'taoResultServer/ResultServerApi',
                params  : {}
            });
            var itemService = _.defaults(options.itemService, {
                module : 'taoItems/runtime/ItemServiceImpl',
                params  : {}
            });
            
            //load dynamically the right ItemService and ResultServerApi
            require([itemService.module, resultServer.module], function(ItemService, ResultServerApi){
                
                var resultServerApi = new ResultServerApi(resultServer.endpoint, resultServer.params);
                
                window.onServiceApiReady = function(serviceApi){
                
                    var itemApi = new ItemService(_.merge({
                        serviceApi : serviceApi,
                        itemId: itemId,
                        resultApi: resultServerApi
                    }, itemService.params));

                    iframeResizer.autoHeight($frame, 'body', 10);
                    var isCORSAllowed = new UrlParser(itemPath).checkCORS();
                    
                    $frame.on('load', function(){
                        var frame = this;
                        
                        //1st try to connect the api on frame load
                        itemApi.connect(frame);

                        //if we are  in the same domain, we add a variable
                        //to the frame window, so the frame knows it can communicate
                        //with the parent
                        if(isCORSAllowed === true){
                            frame.contentWindow.__knownParent__ = true;
                        } 
                        //then we can wait a specific event triggered from the item
                        $(document).on('itemready', function(){
                            itemApi.connect(frame);
                        });
                    })
                    .attr('src', itemPath);

                };
                
                //tell the parent he can trigger onServiceApiReady
                iframeNotifier.parent('serviceready');
            });
        }
    };
    
    return itemRunner;
});
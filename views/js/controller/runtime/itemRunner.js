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
define(['jquery', 'lodash', 'iframeResizer', 'iframeNotifier'], 
        function($, _, iframeResizer, iframeNotifier){
    
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
                    
                    $frame.load(function(){
                        var frame = this;
                        
                        //try to connect the api on frame load
                        itemApi.connect(frame);

                        //or when the specific event is triggered
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
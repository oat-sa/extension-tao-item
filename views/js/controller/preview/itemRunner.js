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
define(['module', 'jquery', 'lodash', 'serviceApi/ServiceApi', 'serviceApi/PseudoStorage', 'serviceApi/UserInfoService', 'taoItems/runtime/ItemServiceImpl', 'taoItems/preview-console'], 
        function(module, $, _, ServiceApi, PseudoStorage, UserInfoService, ItemServiceImpl, previewConsole ){
    
    var previewItemRunner = {
        start : function(options){
           
           var conf = _.merge(module.config(), options || {});
           
           if(conf.previewUrl){
                
                previewConsole.setup();
               
                var resultServer = _.defaults(conf.resultServer, {
                    module : 'taoResultServer/ResultServerApi',
                    endpoint : '',
                    params  : {}
                });
                
                //load dynamically the right ResultServerApi
            require([resultServer.module], function(ResultServerApi){
                
                var resultServerApi = new ResultServerApi(resultServer.endpoint, resultServer.params);
                
                var serviceApi = new ServiceApi(conf.previewUrl, {}, 'preview', new PseudoStorage(), new UserInfoService(conf.userInfoServiceRequestUrl, {}));
                var itemApi = new ItemServiceImpl({
                    serviceApi  : serviceApi,
                    resultApi   : resultServerApi
                });
                
                $('#preview-container').load(function() {
                    var frame = this;

                    //try to connect the api on frame load
                    itemApi.connect(frame);

                    //or when the specific event is triggered
                    $(document).on('itemready', function(){
                        itemApi.connect(frame);
                    });
                });
                
                 $('#preview-container').attr('src', serviceApi.getCallUrl());

                $('#finishButton').click(function() {
                    itemApi.finish();
                });
           });
       }
     }
   };
   
   return previewItemRunner;
});

//            //preview form toggling
//            $('#preview-options-opener').click(function(){
//                    var fromClass 	= 'ui-icon-carat-1-s';
//                    var toClass 	= 'ui-icon-carat-1-e';
//                    if($('#preview-options').css('display') == 'none'){
//                            fromClass 	= 'ui-icon-carat-1-e';
//                            toClass 	= 'ui-icon-carat-1-s';
//                    }
//                    $(this).find('span.ui-icon').switchClass(fromClass,toClass);
//                    $('#preview-options').toggle();
//            });
//
//            //prevent wrong iframe loading from chrome
//            if($.browser.webkit){
//                    $("#preview-container").attr('src', $("#preview-container").attr('src'));
//            }
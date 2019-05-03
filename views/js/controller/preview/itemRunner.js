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
define([
    'module',
    'jquery',
    'lodash',
    'util/encode',
    'serviceApi/ServiceApi',
    'serviceApi/PseudoStorage',
    'serviceApi/UserInfoService',
    'taoItems/runtime/ItemServiceImpl',
    'taoItems/preview/actionBarHook',
    'urlParser',
    'taoItems/previewer/factory',
], function (
    module,
    $,
    _,
    encoder,
    ServiceApi,
    PseudoStorage,
    UserInfoService,
    ItemServiceImpl,
    actionBarHook,
    UrlParser,
    previewerFactory,
) {
    'use strict';

        var previewItemRunner = {

            start: function (options) {
                var conf = _.merge(module.config(), options || {});

                if (conf.previewUrl) {

                    var resultServer = _.defaults(conf.resultServer, {
                        module: 'taoResultServer/ResultServerApi',
                        endpoint: '',
                        params: {}
                    });

                    //load dynamically the right ResultServerApi
                    require([resultServer.module], function (ResultServerApi) {

                        var resultServerApi = new ResultServerApi(
                            resultServer.endpoint,
                            resultServer.params
                        );

                        var serviceApi = new ServiceApi(
                            conf.previewUrl,
                            {},
                            'preview',
                            new PseudoStorage(),
                            new UserInfoService(conf.userInfoServiceRequestUrl, {})
                        );

                        var itemApi = new ItemServiceImpl({
                            serviceApi: serviceApi,
                            resultApi: resultServerApi
                        });

                        var state;
                        try {
                            state = JSON.parse(encoder.decodeBase64(conf.state));
                        } catch(e) {
                            state = null;
                        }

                        if (state) {
                            itemApi.setVariables(state);
                        }

                        // if (deliveryId && resultId && itemDefinition) {
                        var uri = {
                            uri: itemApi.resultApi.itemUri,
                            resultId: itemApi.itemId,
                            itemDefinition: itemApi.itemDefinition,
                            deliveryUri: itemApi.deliveryId
                        };
                        // }

                        previewerFactory(type, uri, state, {
                            readOnly: true,
                            fullPage: true
                        });
                    });
                }
            }
        };

        return previewItemRunner;
    });

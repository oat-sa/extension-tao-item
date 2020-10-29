/*
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
 * Copyright (c) 2014 - 2019 (original work) Open Assessment Techniologies SA
 *
 */
define([
    'lodash',
    'jquery',
    'i18n',
    'module',
    'layout/actions/binder',
    'taoItems/previewer/factory',
    'core/request',
    'ui/feedback',
    'ui/dialog/confirm',
    'ui/dialog/alert',
    'ui/dialog/confirmDelete',
    'util/url',
    'uri',
    'tpl!taoItems/controller/items/tpl/relatedTestsPopup',
    'tpl!taoItems/controller/items/tpl/relatedClassTestsPopup',
    'tpl!taoItems/controller/items/tpl/forbiddenClassAction',
], function (
    _,
    $,
    __,
    module,
    binder,
    previewerFactory,
    request,
    feedback,
    confirmDialog,
    alertDialog,
    confirmDeleteDialog,
    urlUtil,
    uri,
    relatedTestsPopupTpl,
    relatedClassTestsPopupTpl,
    forbiddenClassActionTpl
) {
    'use strict';

    binder.register('itemPreview', function itemPreview(actionContext) {
        var defaultConfig = {
            itemType: 'qtiItem', // TODO: field name can be changed after backend fix (for getting itemType from config )
            state: {},
            uri: {
                itemUri: actionContext.id
            }
        };
        var config = _.merge(defaultConfig, module.config());

        previewerFactory(config.itemType, config.uri, config.state, {
            readOnly: false,
            fullPage: true
        });
    });

    binder.register('deleteItem', function (actionContext) {
        return new Promise((resolve, reject) => {
            checkRelations({
                sourceId: actionContext.id
            })
            .then((responseRelated) => {
                const relatedTests = responseRelated.data;
                const name = $('a.clicked', actionContext.tree).text().trim();
                if (relatedTests.length === 0) {
                    confirmDialog(
                        `${__('Are you sure you want to delete the item')} <b>${name}</b>?`,
                        () => accept(actionContext, this.url, resolve, reject),
                        () => cancel(reject)
                    );
                } else {
                    confirmDeleteDialog(
                        relatedTestsPopupTpl({
                            name,
                            number: relatedTests.length,
                            tests: relatedTests
                        }),
                        () => accept(actionContext, this.url, resolve, reject),
                        () => cancel(reject)
                    );
                }
            });
        });
    });

    binder.register('deleteItemClass', function (actionContext) {
        return new Promise((resolve, reject) => {
            checkRelations({
                classId: actionContext.id
            })
            .then((responseRelated) => {
                const relatedTests = responseRelated.data;
                const name = $('a.clicked', actionContext.tree).text().trim();
                if (relatedTests.length === 0) {
                    confirmDeleteDialog(
                        `${__('Are you sure you want to delete the class')} <b>${name}</b> ${__('and all of its content?')}`,
                        () => accept(actionContext, this.url, resolve, reject),
                        () => cancel(reject)
                    );
                } else {
                    confirmDeleteDialog(
                        relatedClassTestsPopupTpl({
                            name,
                            number: relatedTests.length,
                            tests: relatedTests
                        }),
                        () => accept(actionContext, this.url, resolve, reject),
                        () => cancel(reject)
                    );
                }
            })
            .catch(errorObject => {
                if (errorObject.response.code === 999) {
                    alertDialog(
                        forbiddenClassActionTpl(),
                        () => cancel(reject)
                    );
                }
            });
        });
    });

    function checkRelations(data) {
        return request({
            url: urlUtil.route('relations', 'MediaRelations', 'taoMediaManager'),
            data,
            method: 'GET',
            noToken: true
        });
    }

    function accept(actionContext, url, resolve, reject) {
        return request({
            url: url,
            method: 'POST',
            data: {
                uri: uri.decode(actionContext.uri),
                classUri: actionContext.classUri,
                id: actionContext.id,
                signature: actionContext.signature
            },
            dataType: 'json'
        }).then(function (response) {
            if (response.success && response.deleted) {
                feedback().success(response.message || __('Resource deleted'));

                if (actionContext.tree) {
                    $(actionContext.tree).trigger('removenode.taotree', [
                        {
                            id: actionContext.uri || actionContext.classUri
                        }
                    ]);
                }
                return resolve({
                    uri: actionContext.uri || actionContext.classUri
                });
            } else {
                reject(response.msg || __('Unable to delete the selected resource'));
            }
        });
    }

    function cancel(reject) {
        reject({ cancel: true });
    }
});

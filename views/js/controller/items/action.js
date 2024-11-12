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
 * Copyright (c) 2014 - 2024 (original work) Open Assessment Technologies SA
 *
 */
define([
    'lodash',
    'jquery',
    'i18n',
    'module',
    'layout/actions',
    'layout/actions/binder',
    'layout/section',
    'form/translation',
    'services/translation',
    'taoItems/previewer/factory',
    'core/logger',
    'core/request',
    'ui/feedback',
    'ui/dialog/confirm',
    'ui/dialog/alert',
    'ui/dialog/confirmDelete',
    'util/url',
    'uri',
    'util/wrapLongWords',
    'tpl!taoItems/controller/items/tpl/relatedTestsPopup',
    'tpl!taoItems/controller/items/tpl/relatedClassTestsPopup',
    'tpl!taoItems/controller/items/tpl/forbiddenClassAction',
    'css!taoItems/controller/items/css/relatedTestsPopup.css'
], function (
    _,
    $,
    __,
    module,
    actionManager,
    binder,
    section,
    translationFormFactory,
    translationService,
    previewerFactory,
    loggerFactory,
    request,
    feedback,
    confirmDialog,
    alertDialog,
    confirmDeleteDialog,
    urlUtil,
    uri,
    wrapLongWords,
    relatedTestsPopupTpl,
    relatedClassTestsPopupTpl,
    forbiddenClassActionTpl
) {
    'use strict';

    const logger = loggerFactory('taoItems/actions');

    binder.register('translateItem', function (actionContext) {
        section.current().updateContentBlock('<div class="main-container flex-container-full"></div>');
        const $container = $('.main-container', section.selected.panel);
        const { rootClassUri, id: resourceUri } = actionContext;
        translationFormFactory($container, { rootClassUri, resourceUri, allowDeletion: true })
            .on('edit', (id, language) => {
                return actionManager.exec('item-authoring', {
                    id,
                    language,
                    rootClassUri,
                    originResourceUri: resourceUri,
                    translation: true,
                    actionParams: ['originResourceUri', 'language', 'translation']
                });
            })
            .on('delete', function onDelete(id, language) {
                return translationService.deleteTranslation(resourceUri, language).then(() => {
                    feedback().success(__('Translation deleted'));
                    return this.refresh();
                });
            })
            .on('error', error => {
                logger.error(error);
                feedback().error(__('An error occurred while processing your request.'));
            });
    });

    binder.register('itemPreview', function itemPreview(actionContext) {
        const defaultConfig = {
            provider: 'qtiItem',
            state: {},
            uri: actionContext.id
        };
        const config = _.merge(defaultConfig, module.config());
        const previewerConfig = _.omit(
            {
                readOnly: false,
                fullPage: true,
                pluginsOptions: config.pluginsOptions
            },
            _.isUndefined
        );
        const getProvider = id => {
            if (!id || !config.providers) {
                return config.provider;
            }
            const previewerId = parseInt(`${id}`.split('-').pop(), 10) || 0;
            if (!config.providers[previewerId]) {
                return config.provider;
            }
            return config.providers[previewerId].id;
        };
        previewerFactory(getProvider(this.id) || 'qtiItem', config.uri, config.state, previewerConfig);
    });

    binder.register('deleteItem', function (actionContext) {
        return new Promise((resolve, reject) => {
            checkRelations({
                sourceId: actionContext.id,
                type: 'item'
            }).then(responseRelated => {
                const relatedTests = responseRelated.data.relations;
                const name = prepareName($('a.clicked', actionContext.tree).text().trim());
                if (relatedTests.length === 0) {
                    confirmDialog(
                        __('Are you sure you want to delete the item %s?', `<b>${name}</b>`),
                        () => accept(actionContext, this.url, resolve, reject),
                        () => cancel(reject)
                    );
                } else {
                    confirmDeleteDialog(
                        relatedTestsPopupTpl({
                            name,
                            number: relatedTests.length,
                            numberOther: relatedTests.length - 3 > 0 ? relatedTests.length - 3 : 0,
                            tests: relatedTests.length <= 3 ? relatedTests : relatedTests.slice(0, 3),
                            multiple: relatedTests.length > 1,
                            multipleOthers: relatedTests.length - 3 > 1
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
                classId: actionContext.id,
                type: 'item'
            })
                .then(responseRelated => {
                    const relatedTests = responseRelated.data.relations;
                    const name = prepareName($('a.clicked', actionContext.tree).text().trim());
                    if (relatedTests.length === 0) {
                        confirmDeleteDialog(
                            __(
                                'Are you sure you want to delete the class %s and all of its content?',
                                `<b>${name}</b>`
                            ),
                            () => accept(actionContext, this.url, resolve, reject),
                            () => cancel(reject)
                        );
                    } else {
                        confirmDeleteDialog(
                            relatedClassTestsPopupTpl({
                                name,
                                number: relatedTests.length,
                                numberOther: relatedTests.length - 3 > 0 ? relatedTests.length - 3 : 0,
                                tests: relatedTests.length <= 3 ? relatedTests : relatedTests.slice(0, 3),
                                multiple: relatedTests.length > 1,
                                multipleOthers: relatedTests.length - 3 > 1
                            }),
                            () => accept(actionContext, this.url, resolve, reject),
                            () => cancel(reject)
                        );
                    }
                })
                .catch(errorObject => {
                    if (errorObject.response.code === 999) {
                        alertDialog(forbiddenClassActionTpl(), () => cancel(reject));
                    }
                });
        });
    });

    function checkRelations(data) {
        return request({
            url: urlUtil.route('index', 'ResourceRelations', 'tao'),
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
                if (response.success && !response.deleted) {
                    $(actionContext.tree).trigger('refresh.taotree');
                    reject(
                        response.msg ||
                            response.message ||
                            __(
                                'Unable to delete the selected resource because you do not have the required rights to delete part of its content.'
                            )
                    );
                }

                reject(response.msg || response.message || __('Unable to delete the selected resource'));
            }
        });
    }

    function cancel(reject) {
        reject({ cancel: true });
    }

    function prepareName(name) {
        if (name.length < 50) {
            return name;
        } else {
            return wrapLongWords(name, 50);
        }
    }
});

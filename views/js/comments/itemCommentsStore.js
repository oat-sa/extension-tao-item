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
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */
define(['lodash', 'core/eventifier', 'taoItems/services/itemComments'], function (
    _,
    eventifier,
    itemCommentsApi
) {
    'use strict';

    function normalizeBody(value) {
        return typeof value === 'string' ? value.trim() : '';
    }

    function hasMeaningfulContent(value) {
        if (!value) {
            return false;
        }

        const plainText = String(value)
            .replace(/<[^>]*>/g, ' ')
            .replace(/&nbsp;/gi, ' ')
            .replace(/&#160;/gi, ' ')
            .replace(/\s+/g, ' ')
            .trim();

        return plainText.length > 0;
    }

    /**
     * In-memory authoring comments session store (FR1).
     * Works for Item, Test, or Asset via resourceType.
     *
     * @param {object} config
     * @param {string} [config.resourceUri]
     * @param {string} [config.itemUri] alias of resourceUri
     * @param {string} [config.resourceType] one of RESOURCE_TYPE.* (default ITEM)
     * @param {object} [config.api]
     * @returns {object}
     */
    function itemCommentsStoreFactory(config) {
        const api = (config && config.api) || itemCommentsApi;
        let resourceUri = (config && (config.resourceUri || config.itemUri)) || '';
        let resourceType =
            (config && config.resourceType) || itemCommentsApi.RESOURCE_TYPE.ITEM;
        let comments = [];
        let count = 0;
        let draft = '';
        let loaded = false;
        let loading = false;
        let submitting = false;
        let loadError = null;
        let submitError = null;

        const store = {
            getResourceUri() {
                return resourceUri;
            },

            /**
             * @deprecated Prefer getResourceUri()
             * @returns {string}
             */
            getItemUri() {
                return this.getResourceUri();
            },

            setResourceUri(nextResourceUri) {
                if (nextResourceUri === resourceUri) {
                    return this;
                }
                resourceUri = nextResourceUri || '';
                comments = [];
                count = 0;
                draft = '';
                loaded = false;
                loading = false;
                submitting = false;
                loadError = null;
                submitError = null;
                this.trigger('reset');
                return this;
            },

            /**
             * @deprecated Prefer setResourceUri()
             * @param {string} nextItemUri
             * @returns {object}
             */
            setItemUri(nextItemUri) {
                return this.setResourceUri(nextItemUri);
            },

            getResourceType() {
                return resourceType;
            },

            setResourceType(nextResourceType) {
                if (!nextResourceType || nextResourceType === resourceType) {
                    return this;
                }
                resourceType = nextResourceType;
                comments = [];
                count = 0;
                draft = '';
                loaded = false;
                loading = false;
                submitting = false;
                loadError = null;
                submitError = null;
                this.trigger('reset');
                return this;
            },

            getComments() {
                return comments.slice();
            },

            getCount() {
                return count;
            },

            setDraft(text) {
                draft = typeof text === 'string' ? text : '';
                submitError = null;
                this.trigger('draftchange', draft);
                return this;
            },

            getDraft() {
                return draft;
            },

            hasDirtyDraft() {
                return hasMeaningfulContent(draft);
            },

            clearDraft() {
                return this.setDraft('');
            },

            isLoading() {
                return loading;
            },

            isSubmitting() {
                return submitting;
            },

            getLoadError() {
                return loadError;
            },

            getSubmitError() {
                return submitError;
            },

            load(options) {
                const force = !!(options && options.force);
                if (!resourceUri) {
                    return Promise.reject(new Error('resourceUri is required'));
                }
                if (loaded && !force) {
                    return Promise.resolve(this);
                }
                if (loading) {
                    return Promise.resolve(this);
                }

                loading = true;
                loadError = null;
                this.trigger('loading');

                return api
                    .list(resourceUri, resourceType)
                    .then(data => {
                        comments = (data && data.comments) || [];
                        count = typeof (data && data.count) === 'number' ? data.count : comments.length;
                        loaded = true;
                        loading = false;
                        this.trigger('loaded', comments.slice(), count);
                        this.trigger('countchange', count);
                        return this;
                    })
                    .catch(error => {
                        loading = false;
                        loadError = error;
                        this.trigger('error', error);
                        throw error;
                    });
            },

            submit() {
                if (!resourceUri) {
                    return Promise.reject(new Error('resourceUri is required'));
                }
                const body = normalizeBody(draft);
                if (!hasMeaningfulContent(body)) {
                    return Promise.reject(new Error('Comment body must not be empty'));
                }
                if (submitting) {
                    return Promise.resolve(this);
                }

                submitting = true;
                submitError = null;
                this.trigger('submitting');

                return api
                    .create(resourceUri, resourceType, body)
                    .then(comment => {
                        comments = comments.concat([comment]);
                        count += 1;
                        submitting = false;
                        this.clearDraft();
                        this.trigger('submitted', comment);
                        this.trigger('countchange', count);
                        return this;
                    })
                    .catch(error => {
                        submitting = false;
                        submitError = error;
                        this.trigger('submitFailed', error);
                        throw error;
                    });
            },

            /**
             * Update an existing own comment.
             * @param {string} commentId
             * @param {string} body
             * @returns {Promise<object>}
             */
            update(commentId, body) {
                const nextBody = normalizeBody(body);
                if (!commentId) {
                    return Promise.reject(new Error('comment id is required'));
                }
                if (!hasMeaningfulContent(nextBody)) {
                    return Promise.reject(new Error('Comment body must not be empty'));
                }
                if (submitting) {
                    return Promise.resolve(this);
                }

                submitting = true;
                submitError = null;
                this.trigger('submitting');

                return api
                    .update(commentId, nextBody)
                    .then(comment => {
                        comments = comments.map(existing =>
                            existing.id === comment.id
                                ? Object.assign({}, existing, comment, {
                                      editable:
                                          typeof comment.editable === 'boolean'
                                              ? comment.editable
                                              : existing.editable
                                  })
                                : existing
                        );
                        submitting = false;
                        this.trigger('updated', comment);
                        this.trigger('loaded', comments.slice(), count);
                        return this;
                    })
                    .catch(error => {
                        submitting = false;
                        submitError = error;
                        this.trigger('updateFailed', error);
                        throw error;
                    });
            },

            /**
             * Resolve or reopen a comment.
             * @param {string} commentId
             * @param {boolean} resolved
             * @returns {Promise<object>}
             */
            resolve(commentId, resolved) {
                if (!commentId) {
                    return Promise.reject(new Error('comment id is required'));
                }
                if (submitting) {
                    return Promise.resolve(this);
                }

                submitting = true;
                submitError = null;
                this.trigger('submitting');

                return api
                    .resolve(commentId, !!resolved)
                    .then(comment => {
                        comments = comments.map(existing =>
                            existing.id === comment.id
                                ? Object.assign({}, existing, comment, {
                                      editable:
                                          typeof comment.editable === 'boolean'
                                              ? comment.editable
                                              : existing.editable
                                  })
                                : existing
                        );
                        submitting = false;
                        this.trigger('resolved', comment);
                        this.trigger('loaded', comments.slice(), count);
                        return this;
                    })
                    .catch(error => {
                        submitting = false;
                        submitError = error;
                        this.trigger('resolveFailed', error);
                        throw error;
                    });
            },

            /**
             * Delete an own comment.
             * @param {string} commentId
             * @returns {Promise<object>}
             */
            delete(commentId) {
                if (!commentId) {
                    return Promise.reject(new Error('comment id is required'));
                }
                if (submitting) {
                    return Promise.resolve(this);
                }

                submitting = true;
                submitError = null;
                this.trigger('submitting');

                return api
                    .delete(commentId)
                    .then(() => {
                        comments = comments.filter(existing => existing.id !== commentId);
                        count = comments.length;
                        submitting = false;
                        this.trigger('deleted', commentId);
                        this.trigger('countchange', count);
                        this.trigger('loaded', comments.slice(), count);
                        return this;
                    })
                    .catch(error => {
                        submitting = false;
                        submitError = error;
                        this.trigger('deleteFailed', error);
                        throw error;
                    });
            },

            reset() {
                comments = [];
                count = 0;
                draft = '';
                loaded = false;
                loading = false;
                submitting = false;
                loadError = null;
                submitError = null;
                this.trigger('reset');
                return this;
            }
        };

        return eventifier(store);
    }

    return itemCommentsStoreFactory;
});

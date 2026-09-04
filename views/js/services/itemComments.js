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
define(['core/request', 'util/url'], function (request, urlUtil) {
    'use strict';

    /**
     * Wire values for RestResourceComments resourceType (short strings; mirrors ResourceCommentType).
     * @readonly
     */
    const RESOURCE_TYPE = Object.freeze({
        ITEM: 'item',
        TEST: 'test',
        ASSET: 'asset'
    });

    /**
     * @typedef {object} ItemComment
     * @property {string} id
     * @property {string} resourceUri
     * @property {string} resourceType
     * @property {string} authorId
     * @property {string} authorLabel
     * @property {string} body
     * @property {string} createdAt
     * @property {boolean} edited
     * @property {boolean} resolved
     * @property {boolean} [editable]
     */

    /**
     * @typedef {object} ItemCommentList
     * @property {ItemComment[]} comments
     * @property {number} count
     */

    /**
     * HTTP client for authoring comments REST API (FR1).
     * @returns {object}
     */
    const hasText = value => typeof value === 'string' && value.trim().length > 0;
    const isValidResourceType = type => Object.keys(RESOURCE_TYPE).some(key => RESOURCE_TYPE[key] === type);

    return {
        RESOURCE_TYPE: RESOURCE_TYPE,

        /**
         * Lists comments for a resource (oldest → newest).
         * @param {string} resourceUri
         * @param {string} resourceType one of RESOURCE_TYPE.*
         * @returns {Promise<ItemCommentList>}
         */
        list(resourceUri, resourceType) {
            if (!hasText(resourceUri)) {
                return Promise.reject(new Error('resourceUri must be a non-empty string'));
            }
            if (!isValidResourceType(resourceType)) {
                return Promise.reject(new Error('resourceType must be one of RESOURCE_TYPE values'));
            }

            return request({
                url: urlUtil.route('index', 'RestResourceComments', 'taoItems', {
                    resourceUri: resourceUri,
                    resourceType: resourceType
                }),
                method: 'GET',
                noToken: true
            }).then(response => response.data);
        },

        /**
         * Creates a comment for a resource.
         * @param {string} resourceUri
         * @param {string} resourceType one of RESOURCE_TYPE.*
         * @param {string} body
         * @returns {Promise<ItemComment>}
         */
        create(resourceUri, resourceType, body) {
            if (!hasText(resourceUri)) {
                return Promise.reject(new Error('resourceUri must be a non-empty string'));
            }
            if (!isValidResourceType(resourceType)) {
                return Promise.reject(new Error('resourceType must be one of RESOURCE_TYPE values'));
            }
            if (!hasText(body)) {
                return Promise.reject(new Error('body must be a non-empty string'));
            }

            return request({
                url: urlUtil.route('index', 'RestResourceComments', 'taoItems'),
                method: 'POST',
                data: { resourceUri: resourceUri, resourceType: resourceType, body: body },
                noToken: true
            }).then(response => response.data);
        },

        /**
         * Updates an own comment body.
         * @param {string} id
         * @param {string} body
         * @returns {Promise<ItemComment>}
         */
        update(id, body) {
            return request({
                url: urlUtil.route('update', 'RestResourceComments', 'taoItems'),
                method: 'POST',
                data: { id, body },
                noToken: true
            }).then(response => response.data);
        },

        /**
         * Resolve or reopen a comment.
         * @param {string} id
         * @param {boolean} resolved
         * @returns {Promise<ItemComment>}
         */
        resolve(id, resolved) {
            return request({
                url: urlUtil.route('resolve', 'RestResourceComments', 'taoItems'),
                method: 'POST',
                data: { id, resolved: !!resolved },
                noToken: true
            }).then(response => response.data);
        },

        /**
         * Deletes an own comment.
         * @param {string} id
         * @returns {Promise<{id: string}>}
         */
        delete(id) {
            return request({
                url: urlUtil.route('delete', 'RestResourceComments', 'taoItems'),
                method: 'POST',
                data: { id },
                noToken: true
            }).then(response => response.data);
        },

        /**
         * Search users eligible for @mention (filtered by login, ACL-scoped).
         * @param {string} resourceUri
         * @param {string} resourceType
         * @param {string} [query]
         * @param {object} [options]
         * @param {number} [options.limit=20]
         * @param {number} [options.offset=0]
         * @returns {Promise<{users: Array<{id: string, login: string, displayName: string}>, limit: number, offset: number, total: number}>}
         */
        searchMentionUsers(resourceUri, resourceType, query, options) {
            if (!hasText(resourceUri)) {
                return Promise.reject(new Error('resourceUri must be a non-empty string'));
            }
            if (!isValidResourceType(resourceType)) {
                return Promise.reject(new Error('resourceType must be one of RESOURCE_TYPE values'));
            }

            const opts = options || {};
            const params = {
                resourceUri: resourceUri,
                resourceType: resourceType,
                q: typeof query === 'string' ? query : '',
                limit: typeof opts.limit === 'number' ? opts.limit : 20,
                offset: typeof opts.offset === 'number' ? opts.offset : 0
            };

            return request({
                url: urlUtil.route('searchUsers', 'RestUser', 'tao', params),
                method: 'GET',
                noToken: true
            }).then(response => response.data);
        }
    };
});

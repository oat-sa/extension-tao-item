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
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */
define(['core/request', 'util/url'], function (request, urlUtil) {
    'use strict';

    /**
     * @typedef {object} ItemComment
     * @property {string} id
     * @property {string} itemUri
     * @property {string} authorId
     * @property {string} authorLabel
     * @property {string} body
     * @property {string} createdAt
     * @property {string} status
     * @property {boolean} edited
     * @property {boolean} resolved
     */

    /**
     * @typedef {object} ItemCommentList
     * @property {ItemComment[]} comments
     * @property {number} count
     */

    /**
     * HTTP client for Item Comments REST API (FR1 M1).
     * @returns {object}
     */
    return {
        /**
         * Lists comments for an item (oldest → newest).
         * @param {string} itemUri
         * @returns {Promise<ItemCommentList>}
         */
        list(itemUri) {
            return request({
                url: urlUtil.route('index', 'RestItemComments', 'taoItems', { itemUri }),
                method: 'GET',
                noToken: true
            }).then(response => response.data);
        },

        /**
         * Creates a comment for an item.
         * @param {string} itemUri
         * @param {string} body
         * @returns {Promise<ItemComment>}
         */
        create(itemUri, body) {
            return request({
                url: urlUtil.route('index', 'RestItemComments', 'taoItems'),
                method: 'POST',
                data: { itemUri, body },
                noToken: true
            }).then(response => response.data);
        },

        /**
         * Returns the visible comment count for an item.
         * @param {string} itemUri
         * @returns {Promise<{count: number}>}
         */
        count(itemUri) {
            return request({
                url: urlUtil.route('count', 'RestItemComments', 'taoItems', { itemUri }),
                method: 'GET',
                noToken: true
            }).then(response => response.data);
        }
    };
});

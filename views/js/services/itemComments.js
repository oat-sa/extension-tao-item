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
     * Wire values for RestResourceComments resourceType (RDF class URIs; mirrors ResourceCommentType).
     * @readonly
     */
    const RESOURCE_TYPE = Object.freeze({
        ITEM: 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
        TEST: 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test',
        ASSET: 'http://www.tao.lu/Ontologies/TAOMedia.rdf#Media'
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
    return {
        RESOURCE_TYPE: RESOURCE_TYPE,

        /**
         * Lists comments for a resource (oldest → newest).
         * @param {string} resourceUri
         * @param {string} resourceType one of RESOURCE_TYPE.*
         * @returns {Promise<ItemCommentList>}
         */
        list(resourceUri, resourceType) {
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
            return request({
                url: urlUtil.route('index', 'RestResourceComments', 'taoItems'),
                method: 'POST',
                data: { resourceUri: resourceUri, resourceType: resourceType, body: body },
                noToken: true
            }).then(response => response.data);
        }
    };
});

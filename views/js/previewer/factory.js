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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
define([
    'lodash',
    'context',
    'core/dataProvider/request',
    'core/logger',
    'core/providerLoader',
    'core/providerRegistry',
    'util/url',
    'taoItems/previewer/provider/legacy'
], function (
    _,
    context,
    request,
    loggerFactory,
    providerLoaderFactory,
    providerRegistry,
    urlHelper,
    legacyPreviewer
) {
    'use strict';

    var logger = loggerFactory('taoItems/previewer');

    /**
     * Loads and display the item previewer
     * @param {String} type
     * @param {String|Object} uri
     * @param {Object} state
     * @returns {Promise}
     */
    function previewerFactory(type, uri, state) {
        return request(urlHelper.route('previewers', 'ItemPreview', 'taoItems'))
            .then(function (modules) {
                return providerLoaderFactory()
                    .addList(modules)
                    .load(context.bundle);
            })
            .then(function (providers) {
                _.forEach(providers, function (provider) {
                    previewerFactory.registerProvider(provider.name, provider);
                });
            })
            .then(function () {
                return previewerFactory.getProvider(type);
            })
            .catch(function (err) {
                logger.error(err);
                return legacyPreviewer;
            })
            .then(function (provider) {
                return provider.init(uri, state);
            });
    }

    return providerRegistry(previewerFactory, function validateProvider(provider) {
        if (!_.isFunction(provider.init)) {
            throw new TypeError('The previewer provider MUST have a init() method');
        }
        return true;
    });
});

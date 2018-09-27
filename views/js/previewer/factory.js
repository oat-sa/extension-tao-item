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
    'module',
    'core/providerLoader',
    'core/providerRegistry',
    'util/url',
    'taoItems/previewer/adapter/legacy'
], function (
    _,
    context,
    module,
    providerLoaderFactory,
    providerRegistry,
    urlHelper,
    legacyPreviewer
) {
    'use strict';

    /**
     * Loads and display the item previewer
     * @param {String} type - The type of previewer
     * @param {String|Object} uri - The URI of the item to load
     * @param {Object} state - The state of the item
     * @param {Object} [config] - Some config entries
     * @param {String} [config.fullPage] - Force the previewer to occupy the full window.
     * @param {String} [config.readOnly] - Do not allow to modify the previewed item.
     * @param {Object} [config.previewers] - Optionally load static adapters. By default take them from the module's config.
     * @returns {Promise}
     */
    function previewerFactory(type, uri, state, config) {
        config = _.defaults(config || {}, module.config());
        return providerLoaderFactory()
            .addList(config.previewers)
            .load(context.bundle)
            .then(function (providers) {
                previewerFactory.registerProvider(legacyPreviewer.name, legacyPreviewer);
                _.forEach(providers, function (provider) {
                    previewerFactory.registerProvider(provider.name, provider);
                });
            })
            .then(function () {
                return previewerFactory.getProvider(type || legacyPreviewer.name);
            })
            .then(function (provider) {
                return provider.init(uri, state, config);
            });
    }

    return providerRegistry(previewerFactory, function validateProvider(provider) {
        if (!_.isFunction(provider.init)) {
            throw new TypeError('The previewer provider MUST have a init() method');
        }
        return true;
    });
});

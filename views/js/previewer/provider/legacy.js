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
    'util/url',
    'taoItems/preview/preview'
], function (urlHelper, preview) {
    'use strict';

    /**
     * Wraps the legacy item previewer in order to be loaded by the previewer factory
     */
    return {
        name: 'legacy',

        /**
         * Builds and shows the legacy item previewer
         *
         * @param {String} uri
         * @param {Object} state
         * @param {Object} [config]
         * @returns {Object}
         */
        init: function init(uri, state, config) {
            window.scrollTo(0,0);
            preview.init(urlHelper.route('forwardMe', 'ItemPreview', 'taoItems', {uri : uri}), state);
            preview.show();
            return preview;
        }
    };
});

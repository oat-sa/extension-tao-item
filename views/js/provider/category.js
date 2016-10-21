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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 */

/**
 * Provides category data.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'i18n',
    'util/url',
    'core/dataProvider/request',
    'core/promise'
], function( _, __, url, request, Promise){
    'use strict';

    /**
     * Per function requests configuration.
     */
    var defaultConfig = {
        getExposedsByClass : {
            url : url.route('getExposedsByClass', 'Category', 'taoItems')
        },
        setExposed : {
            url : url.route('setExposed', 'Category', 'taoItems')
        }
    };


    /**
     * Creates a configured category provider
     *
     * @param {Object} [config] - to override the default config
     * @returns {categoryProvider} the new provider
     */
    return function categoryProviderFactory(config){

        config = _.defaults(config || {}, defaultConfig);

        /**
         * @typedef {categoryProvider}
         */
        return {

            /**
             * Get the properties and the exposed state for a given class
             * @param {String} id - the class URI
             * @returns {Promise} that resolves with and object like  propertyURI : exposed
             */
            getExposedsByClass: function getExposedsByClass(id){
                return new Promise(function(resolve, reject){

                    if(_.isEmpty(id)){
                        return reject(new TypeError('The class URI must be provided in the id parameter'));
                    }

                    return resolve(request(config.getExposedsByClass.url, { id : id }));
                });
            },

            /**
             * Set the exposed state for a property
             * @param {String} id - the property URI
             * @param {Boolean} exposed - the exposed value
             * @returns {Promise} that resolves with the list of ID definitions.
             */
            setExposed : function setExposed(id, exposed){
                return new Promise(function(resolve, reject){

                    if(_.isEmpty(id)){
                        return reject(new TypeError('The property URI must be provided in the id parameter'));
                    }
                    if(!_.isBoolean(exposed)){
                        return reject(new TypeError('The exposed value is required.'));
                    }

                    return resolve(request(config.setExposed.url, { id : id, exposed: exposed }));
                });
            }
        };
    };
});

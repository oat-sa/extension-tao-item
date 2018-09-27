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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

/**
 * Provides common strategies to resolved assets
 * that may be used by any type of items.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'util/url',
    'lodash'
], function(urlUtil, _){
    'use strict';

    /**
     * Prepend a base to an URL
     * @param {Object} url - a parsed URL
     * @param {String} base - the base to prepend
     * @param {Boolean} [slashcat = false] - remove dots, double slashes, etc.
     * @returns {String} the URL
     */
    var prependToUrl = function prependToUrl(url, base, slashcat){

        //is slashcat we manage slash concact
        if(slashcat === true){
            return base.replace(/\/$/, '') + '/' + url.directory.replace(/^\.\//, '').replace(/^\//, '')
                + encodeURIComponent(url.file.replace(/^\.\//, '').replace(/^\//, ''));
        }

        return base + url.directory.replace(/^\.?\//, '') + encodeURIComponent(url.file.replace(/^\.?\//, ''));
    };

    /**
     * Unrelated strategies accessible by there name.
     * Remember to not use the whole object, but each one in an array since the order matters.
     *
     * @exports taoItems/assets/strategies
     */
    var strategies = {

        //the packedUrl will replace the asset with the url given in the assets part
        //the assetManager should add the assets part to data with .setData('assets' itemData.content.assets)
        packedUrl : {
            name : 'packedUrl',
            handle : function handlePackedUrl(url, data){
                var type;
                if(!_.isUndefined(url.source) && !_.isUndefined(data.assets)) {
                    type = _.findKey(data.assets, url.source);
                    if( type && urlUtil.isAbsolute(data.assets[type][url.source])){
                        return data.assets[type][url.source];
                    }
                }
            }
        },
        //the baseUrl concats the baseUrl in data if the url is relative
        baseUrl : {
            name : 'baseUrl',
            handle : function handleBaseUrl(url, data){
                if(typeof data.baseUrl === 'string' && (urlUtil.isRelative(url)) ){
                    return prependToUrl(url, data.baseUrl, data.slashcat);
                }
            }
        },

        //bust the cache for item CSS
        itemCssNoCache : {
            name : 'itemCssNoCache',
            handle : function handleItemCss(url, data){
                if(typeof data.baseUrl === 'string' && (urlUtil.isRelative(url)) && /\.css$/.test(url.file)) {
                    return urlUtil.build(prependToUrl(url, data.baseUrl, data.slashcat), { bust : Date.now() });
                }
            }
        },

        //absolute URL are just left intact
        external : {
            name : 'external',
            handle : function handleExternal(url){
                if(urlUtil.isAbsolute(url)){
                    return url.toString();
                }
            }
        },

        //the base64 encoded resources are also left intact
        base64 : {
            name : 'base64',
            handle : function handleB64(url){
                if(urlUtil.isBase64(url)){
                    return url.toString();
                }
            }
        },

        //special tao media protocol
        taomedia : {
            name : 'taomedia',
            handle : function handleTaoMedia(url, data){
                //either a baseUrl is given or if empty, taomedia resources are managed as relative resources
                var baseUrl = data.baseUrl || './';
                if( (typeof url === 'object' && url.protocol === 'taomedia') ||
                    (/^taomedia:\/\//.test(url.toString())) ){
                    return baseUrl + url.toString();
                }
            }
        }
    };
    return strategies;
});

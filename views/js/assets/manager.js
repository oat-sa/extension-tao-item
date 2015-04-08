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
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'lodash',
    'async'
], function(_, async){
    'use strict';

    /**
     * @typedef AssetStrategy Defines a way to resolve an asset path
     * @property {String} name - the strategy name
     * @property {Function} handle - how to resolve the strategy. It receives the path and the data in paramters and returns only if it resolves.
     */


    /**
     * The asset manager proposes you to resolve your asset path for you!
     * You need to add the resolution strategies, it will them evaluate each strategy until the right one is found
     *
     * @example
     *
        assetManager.addStragegies([{
            name : 'external',
            handle : function(path, data){
                if(/^http/.test(path)){
                    return path;
                }
            }
        }, {
            name : 'relative',
            handle : function(path, data){
                if(/^((\.\/)|(\w\/))/){
                    return data.baseUrl + '/' + path ;
                }
            }
        }]);
        assetManager.resolve('http://foo/bar.png'); //will resolved using external
        assetManager.resolve('bar.png'); //will resolved using relative strategy

     *
     * @exports taoItems/assets/manager
     * @namespace assetManager
     */
    var assetManager = {

        /**
         * The stack of strategies that would be used to resolve the asset path
         * @type {AssetStrategy[]}
         */
        strategies : [],

        /**
         * Add an asset resolution strategy.
         * The strategies will be evaluated in the order they've been added.
         * @param {AssetStrategy} strategy - the strategy to add
         * @throws {TypeError} if the strategy isn't defined correctly
         */
        addStragegy : function addStragegy (strategy){

            if(!_.isPlainObject(strategy) || !_.isFunction(strategy.handle) || !_.isString(strategy.name)){
                throw new TypeError('An asset resolution strategy is an object with a handle method and a name');
            }

            this.strategies.push(strategy);
        },

        /**
         * Add multiple strategies
         * @param {AssetStrategy[]|Function[]} strategies - the strategies to add, functions will create anonymous strategies
         * @throws {TypeError} if the strategy isn't defined correctly
         */
        addStragegies : function addStragegies(strategies){
            var self = this;

            _.forEach(strategies, function(strategy){
                if(_.isPlainObject(strategy)){
                    self.addStragegy(strategy);
                } else if(_.isFunction(strategy)){
                    //enable anonymous strategy only here
                    self.addStragegy({
                        name   : 'strategy_' + (self.strategies.length + 1),
                        handle : strategy
                    });
                }
            });
        },

        /**
         * Create a resolution context.
         */
        createContext : function createContext(data){
            var self = this;
            return {
                resolve : _.bind(_.partialRight(self.resolve, data), self),
                resolveBy : _.bind(_.partialRight(self.resolveBy, data), self)
            };
        },

        resolve : function resolve(path, data){
            var resolved;
            _.forEach(this.strategies, function(strategy){
                var result = strategy.handle(path, data);
                if(result){
                    resolved = result;
                    return false;
                }
            });

            return resolved;
        },

        resolveBy : function resolveBy(name, path, data){
            var strategy = _.find(this.strategies, {name : name});
            if(strategy){
                return strategy.handle(path, data);
            }
        }
    };

    return assetManager;
});

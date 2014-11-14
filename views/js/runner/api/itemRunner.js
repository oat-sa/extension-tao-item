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
 * Copyright (c) 2014 (original work) Open Assessment Technlogies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['jquery', 'lodash'], function($, _){
    'use strict';

     /**
     * 
     * Builds a brand new {@link ItemRunner}.
     *
     * <strong>The factory is an internal mechanism to create encapsulated contexts. 
     *  I suggest you to use directly the name <i>itemRunner</i> when you require this module.</strong>
     *
     * @example require(['itemRunner'], function(itemRunner){
                    itemRunner({itemId : 12})
     *                    .on('statechange', function(state){
     *
     *                    })
     *                    .on('ready', function(){
     *
     *                    })
     *                    .on('response', function(){
     *
     *                    })          
     *                   .render($('.item-container'));
     *          });
     * @exports itemRunner
     * @namespace itemRunnerFactory
     *
     * @param {String} [providerName] - the name of a provider previously registered see {@link itemRunnerFactory#register}
     * @param {Object} [data] - the data of the item to run
     * @param {Object} [data.state] - the initial state of the the item
     * 
     *
     * @returns {ItemRunner} 
     */
    var itemRunnerFactory = function itemRunnerFactory(providerName, data){

        //optional params based on type
        if(_.isPlainObject(providerName)){
            data = providerName;
            providerName = undefined;
        } 
        data = data || {};

        //contains the bound events.
        var events = {};

        /*
         * Select the provider
         */

        var provider;
        var providers = itemRunnerFactory.providers;
        
        //check a provider is available
        if(!providers || _.size(providers) === 0){
            throw new Error('No provider regitered');
        }

        if(_.isString(providerName) && providerName.length > 0){
            provider = providers[providerName];
        } else if(_.size(providers) === 1) {

            //if there is only one provider, then we take this one
            providerName = _.keys(providers)[0];
            provider = providers[providerName];
        }

        //now we should have a provider
        if(!provider){
            throw new Error('No candidate found for the provider');
        }
       
         

       /**
        * The ItemRunner
        * @typedef {Object} ItemRunner
        */

        /**
        * @type {ItemRunner}
        * @lends itemRunnerFactory
        */
        var ItemRunner = {

            /**
             * Initialize the runner. 
             * @param {Object} [newData] - just in case you want to change item data (it should not occurs in most case)
             * @returns {ItemRunner} to chain calls
             * @fires ItemRunner#init
             */
            init : function(newData){
                var self = this;

                if(newData){
                    data = _.merge(data, newData);
                }
        
                /**
                 * Calls provider's initialization with item data.
                 * @callback InitItemProvider
                 * @param {Object} data - the item data
                 * @param {Function} done - call once the initialization is done
                 */
                provider.init.call(this, data, function(){

                    /**
                     * the runner has initialized correclty the item
                     * @event ItemRunner#init
                     */
                    self.trigger('init');
                });

                return this;
            },

            /**
             * Initialize the current item. 
             * @param {HTMLElement|jQueryElement} elt - the DOM element that is going to contain the rendered item.
             * @returns {ItemRunner} to chain calls
             * @fires ItemRunner#ready
             */
           render : function(elt){
                var self = this;

                if(!elt instanceof HTMLElement || !elt instanceof $){
                    throw new TypeError('A valid HTMLElement (or a jquery element) at least is required to render the item');
                }
                
                /**
                 * Calls the provider's render
                 * @callback InitItemProvider
                 * @param {Object} data - the item data
                 * @param {Function} done - call once the initialization is done
                 */
                provider.render.call(this, elt, function(){

                    /**
                     * the runner has initialized correclty the item
                     * @event ItemRunner#ready
                     */
                    self.trigger('ready');
                });
           },

           /**
            * Get the current state of the running item.
            * @returns {Object} state
            */
           getState : function(){
                return data.state;
           },

           /**
            * Set the current state of the running item.
            * This should have the effect to restore the item state.
            * 
            * @param {Object} state - the new state
            * @returns {ItemRunner} 
            * @fires ItemRunner#statechange
            */
           setState : function(state){

                //TODO verfy the state type and trigger an error accordingly

                data.state = state;
    
                /**
                 * @event ItemRunner#statechange
                 * @param {Object} state - the new state
                 */
                this.trigger('statechange', data.state);

                return this;
           },

           getResponses : function(){

           },

           setResponses : function(responses){

           },

           addResponse : function(response){

           },

           /**
            * Attach an event handler.
            * Calling `on` with the same eventName multiple times add callbacks: they
            * will all be executed.
            *
            * @example itemRunner()
            *               .on('statechange', function(state){ 
            *                   //state === this.getState()
            *               });
            *
            * @param {String} name - the name of the event to listen
            * @param {Function} handler - the callback to run once the event is triggered. It's executed with the current itemRunner context (ie. this 
            * @returns {ItemRunner} 
            */
            on : function(name, handler){
                if(_.isString(name) && _.isFunction(handler)){
                    events[name] = events[name] || [];
                    events[name].push(handler);
                }
                return this;
            },

            /**
            * Remove handlers for an event.
            *
            * @example itemRunner().off('statechange');
            *
            * @param {String} name - the event name
            * @returns {ItemRunner}
            */
            off : function(name){
                if(_.isString(name)){
                    events[name] = [];
                }
                return this;
            },

            /**
            * Trigger an event manually.
            *
            * @example itemRunner().trigger('statechange', new State());
            *
            * @param {String} name - the name of the event to trigger
            * @param {*} data - arguments given to the handlers
            * @returns {ItemRunner}
            */
            trigger : function(name, data){
                var self = this;
                if(_.isString(name) && _.isArray(events[name])){
                    _.forEach(events[name], function(event){
                        event.call(self, data);
                    });
                }
                return this;
            }
        };

        return ItemRunner;
    };

    /**
     * Register an <i>Item Runtime Provider</i> into the item runner. 
     * The provider provides the behavior required by the item runner.
     *
     * @param {String} name - the provider name will be used to select the provider while instantiating the runner
     *
     * @param {Object} provider - the Item Runtime Provider as a plain object. The itemRunner forwards encapsulate and delegate calls to the provider.
     * @param {InitProvider} provider.init - the init function is the only function required. It takes itemData and the done callback in parameter.
     * @param {Function} [provider.render] - the render function takes a dom element (or a jQuery element)  and the done callback in parameter.
     * @param {Function} [provider.getState] - the getState must return an object.
     * @param {Function} [provider.setState] - the setState function takes an object in parameter. 
     * @param {Function} [provider.getResponses] - returns an Array containing the responses.
     * @param {Function} [provider.setResponses] - takes an Array of the responses in parameter.
     * 
     * @throws TypeError when a wrong provider is given or an empty name.
     */
    itemRunnerFactory.register = function registerProvider(name, provider){
        //type checking
        if(!_.isString(name) || name.length <= 0){
            throw new TypeError('It is required to give a name to your provider.');
        }      
        if(!_.isPlainObject(provider) || !_.isFunction(provider.init)){
            throw new TypeError('A provider is an object that contains an init function.');
        }      

        this.providers = this.providers || {};
        this.providers[name] = provider;
    };

    return itemRunnerFactory;
});

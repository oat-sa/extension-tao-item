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
     * @param {Object} [data] - the data of the item to run
     * @param {Object} [data.state] - the initial state of the the item
     * 
     *
     * @returns {ItemRunner} 
     */
    var itemRunnerFactory = function itemRunnerFactory(data){
        
        data = data || {};


        //contains the bound events.
        var events = {};

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

           render : function(elt){

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

    return itemRunnerFactory;
});

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
define([
    'jquery', 
    'lodash', 
    'taoItems/runtime/itemRunner', 
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiCommonRenderer/renderers/Renderer'], 
function($, _, itemRunner, QtiLoader, QtiElement, QtiRenderer){
    'use strict';

    var itemData = {};

    itemRunner.register({
        init : function(itemData, done){
            var self = this; 

            this._loader = new QtiLoader();
            this._renderer = new QtiRenderer({
                baseUrl : '.'
            });

            this._loader.loadItemData(itemData, function(item){
                self._item = item;
                this._renderer.load(function(){
                    self._item.setRenderer(this);
    
                    done();
                }, this.getLoadedClasses()); 
            }); 
        },

        render : function(elt, done){
            if(this._item){

                this._item.clear();

                try{
                    elt.innerHTML = this._item.render({});
                }catch(e){
                    console.log('error in template rendering', e);
                }
                try{
                    this._item.postRender(done);
                }catch(e){
                    console.log('error in post rendering', e);
                }
            }
        },

        getState : function(){
                        
        },

        setState : function(state){

        },

        getResponses : function(){
            if(this._item){
                return _.reduce(this._item.getInteractions(), function(res, interaction){
                    res.push(interaction.getResponses());
                    return res;
                });
            }
            return [];
        },

        setResponses : function(responses){
            
        }
        
    });

});

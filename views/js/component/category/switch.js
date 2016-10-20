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
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'ui/component',
    'tpl!taoItems/component/category/switch'
], function($, _, __, component, switchTpl){
    'use strict';

    /**
     * The titles based on current exposition state
     */
    var titles = {
        expose : __('Expose the property value to create automated categories.'),
        unexpose : __('The property is exposed to category addtion.&#13Remove exposition ?')
    };


    /**
     *
     * @param {jQueryElement} $container - where to append the component
     *
     * @returns {categorySwitchComponent} the component
     *
     * @throws {TypeError} whithout a user nor ids
     */
    return function categorySwitchFactory($container, propertyId, exposed){

        var categorySwitchComponent;


        if(!propertyId){
            throw new TypeError('We need the property id');
        }


        /**
         * @typedef {categorySwitchComponent} The component itself
         */
        categorySwitchComponent =
            component({
                setExposed : function setExposed(value){
                    if (!this.is('disabled')) {
                        this.setState('exposed', !!value);

                        if(this.is('rendered')){
                            if(value){
                                this.getElement()
                                    .removeClass('placeholder')
                                    .addClass('txt-success')
                                    .attr('title', titles.unexpose);
                            } else {
                                this.getElement()
                                    .removeClass('txt-success')
                                    .addClass('placeholder')
                                    .attr('title', titles.expose);
                            }
                        }
                    }
                },
                isExposed : function isExposed(){
                    return this.is('exposed');
                }
            }, {})
                .on('init', function(){

                    this.setState('exposed', exposed);

                    this.render($container);
                })
                .on('render', function(){
                    var self = this;
                    var $component = this.getElement();

                    this.setExposed(exposed);
                    $component
                        .off('click')
                        .on('click', function(e){
                            e.preventDefault();
                            if(!self.is('disabled')){
                                self.trigger('requestChange', self.config.id, !self.is('exposed'));
                            }
                        });
                });

        //defering initialization to be able to listen
        _.defer(function(){
            categorySwitchComponent
                .setTemplate(switchTpl)
                .init({
                    id : propertyId,
                });
        });

        return categorySwitchComponent;
    };
});



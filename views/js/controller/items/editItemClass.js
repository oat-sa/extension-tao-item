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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 *
 */


/**
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'uri',
    'ui/contextualPopup',
    'tpl!taoItems/controller/items/category',
    'context',
    'module'
], function($, uriUtil, contextualPopup, categoryTpl, context, module) {
    'use strict';

    var injectCategoryAutoAdding = function injectCategoryAutoAdding($container) {

        $('.regular-property', $container).each(function(){
            var $propertyContainer = $(this);
            var $propertyToolbar = $('.property-heading-toolbar', $propertyContainer);
            var propertyUri = uriUtil.decode($propertyContainer.attr('id'));

            var $button = $(categoryTpl({
                id : propertyUri
            }));

            if ($('.icon-edit', $propertyToolbar).length) {
                $button.insertAfter($('.icon-edit', $propertyToolbar));
            } else {
                $propertyToolbar.append($button);
            }
        });
    };

    var indexCategoryController = {

        start : function start() {

            var $container = $('#panel-manage_items .property-container');
            var classUri = $('#id').val();
            $.getJSON('/taoItems/Category/get', { id: classUri }).done(function(res){
                console.log(res);
            });

            //injectCategoryAutoAdding($container);

            //$container
                //.off('click.category', '.category-auto-adder')
                //.on('click.category', '.category-auto-adder', function(e){
                    //e.preventDefault();


                //});
        }
    };

    return indexCategoryController;
});

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
    'lodash',
    'uri',
    'taoItems/component/category/switch',
    'taoItems/provider/category',
    'ui/feedback'
], function($, _, uriUtil, categorySwitch, categoryProvider, feedback) {
    'use strict';

    var provider  = categoryProvider();

    var injectButtonPlaceholder = function injectButtonPlaceholder(uri) {
        var $propContainer = $('#property_' + uriUtil.encode(uri));
        var $placeholder,
            $propertyToolbar,
            $editButton;

        if($propContainer.length && $propContainer.hasClass('regular-property')){
            $placeholder     = $('<span>');
            $propertyToolbar = $('.property-heading-toolbar', $propContainer);
            $editButton      = $('.icon-edit', $propertyToolbar);

            if ($editButton.length) {
                $placeholder.insertAfter($editButton);
            } else {
                $propertyToolbar.append($placeholder);
            }
        }
        return $placeholder;
    };

    var handleError = function handleError(err){
        feedback().error(err.message);
    };

    var addCategorySwitch = function addCategorySwitch($container, propertyUri, exposed) {
        categorySwitch($container, propertyUri, exposed)
            .on('requestChange', function(propUri, value) {
                var self = this;
                this.setState('disabled', true);

                provider
                    .setExposed(propUri, value)
                    .then(function(success){
                        self.setState('disabled', false);
                        if(success){
                            self.setExposed(value);
                        }
                    })
                    .catch(handleError);
            });
    };


    var indexCategoryController = {

        start : function start() {
            var classUri = $('#id').val();

            provider
                .getExposedsByClass(classUri)
                .then(function(results){
                    _.forEach(results, function(exposed, uri){
                        var $container = injectButtonPlaceholder(uri);
                        if($container && $container.length){
                            addCategorySwitch($container, uri, exposed);
                        }
                    });
                })
                .catch(handleError);
        }
    };

    return indexCategoryController;
});

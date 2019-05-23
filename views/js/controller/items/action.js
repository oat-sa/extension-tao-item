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
 * Copyright (c) 2014 - 2019 (original work) Open Assessment Techniologies SA
 *
 */
define([
    'lodash',
    'module',
    'layout/actions/binder',
    'taoItems/previewer/factory'
], function(_,  module, binder, previewerFactory){
	'use strict';

    binder.register('itemPreview', function itemPreview(actionContext) {
        var defaultConfig = {
            itemType: 'qtiItem', // TODO: field name can be changed after backend fix (for getting itemType from config )
            state: { },
            uri: {
                itemUri: actionContext.id,
            },
        };
        var config = _.merge(defaultConfig, module.config());

        previewerFactory(config.itemType, config.uri, config.state, {
            readOnly: false,
            fullPage: true
        });
	});


});

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
 * Copyright (c) 2014 (original work) Open Assessment Techniologies SA
 *
 */
define([
    'layout/actions/binder',
    'uri',
    'jquery',
    'context',
    'taoItems/preview/preview',
    'helpers'
], function(binder, uri, $, context, preview, helpers){
    'use strict';

    binder.register('itemPreview', function itemPreview(actionContext){
        preview.init(helpers._url('forwardMe', 'ItemPreview', context.shownExtension, {uri : actionContext.id}));
        preview.show();
    });

});

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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */
define(['jquery', 'lodash'], function($, _){
    'use strict';

    var hasCheckedCheckbox = function hasCheckedCheckbox($container){
        return ($container.find('.form-group :checkbox:checked').length > 0);
    };

    var updateSubmitter = function($container){
        var $formSubmitter = $container.find('.form-submitter');
        if(hasCheckedCheckbox($container)){
            $formSubmitter.removeClass('disabled');
        }else{
            $formSubmitter.addClass('disabled');
        }
    };

    return {
        start : function start(){
            var $container = $('#exportChooser');
            var update = _.partial(updateSubmitter, $container);
            update.call();
            $container
                .on('change', ':checkbox', update);
        }
    };
});
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
 * Copyright (c) 2014-2018 (original work) Open Assessment Technologies SA;
 */

/**
 * Configure the extension bundles
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
module.exports = function(grunt) {
    'use strict';

    var root = grunt.option('root');

    grunt.config.merge({
        bundle : {
            taoitems : {
                options : {
                    extension : 'taoItems',
                    extensionPath : root + '/taoItems/views/js',
                    outputDir : 'loader',
                    bundles : [{
                        name : 'taoItems',
                        default : true,
                        include : [
                            'taoItems/assets/**/*',
                            'taoItems/preview/**/*',
                            'taoItems/previewer/**/*',
                            'taoItems/runner/**/*',
                            'taoItems/runtime/**/*'
                        ]
                    }, {
                        name : 'taoItemsRunner',
                        include : [
                            'taoItems/assets/**/*',
                            'taoItems/runner/**/*'
                        ]
                    }]
                }
            }
        }
    });

    grunt.registerTask('taoitemsbundle', ['bundle:taoitems']);
};

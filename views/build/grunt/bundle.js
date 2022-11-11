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
 * Copyright (c) 2014-2019 (original work) Open Assessment Technologies SA;
 */

/**
 * Configure the extension bundles
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 *
 * @param {Object} grunt - the grunt instance (convention)
 */
module.exports = function (grunt) {
    'use strict';

    const root = grunt.option('root');

    grunt.config.merge({
        bundle: {
            taoitems: {
                options: {
                    extension: 'taoItems',
                    extensionPath: `${root}/taoItems/views/js`,
                    outputDir: 'loader',
                    paths: require('./paths.json'),
                    bundles: [
                        {
                            name: 'taoItems',
                            default: true,
                            babel: true,
                            include: [
                                'taoItems/assets/**/*',
                                'taoItems/preview/**/*',
                                'taoItems/previewer/**/*',
                                'taoItems/runner/**/*',
                                'taoItems/runtime/**/*'
                            ]
                        },
                        {
                            name: 'taoItemsRunner',
                            babel: true,
                            include: ['taoItems/assets/**/*', 'taoItems/runner/**/*']
                        },
                        {
                            name: 'taoItemsRunner.es5',
                            babel: true,
                            targets: {
                                ie: '11'
                            },
                            include: ['taoItems/assets/**/*', 'taoItems/runner/**/*']
                        }
                    ]
                }
            }
        }
    });

    grunt.registerTask('taoitemsbundle', ['bundle:taoitems']);
};

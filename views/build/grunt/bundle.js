module.exports = function (grunt) {
    'use strict';

    var clean     = grunt.config('clean') || {};
    var copy      = grunt.config('copy') || {};
    var ext;
    var libs      = grunt.option('mainlibs');
    var out       = 'output';
    var requirejs = grunt.config('requirejs') || {};
    var root      = grunt.option('root');
    var uglify    = grunt.config('uglify') || {};

    ext = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);

    /**
     * Compile tao files into a bundle
     */
    requirejs.taoitems_bundle = {
        options: {
            exclude: ['mathJax'].concat(libs),
            include: ext.getExtensionsControllers(['taoItems']),
            out: out + '/taoItems/controllers.min.js',
            paths: {
                taoItems: root + '/taoItems/views/js',
                taoItemsCss: root + '/taoItems/views/css'
            }
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taoitems_bundle = {
        files: [
            { src: [out + '/taoItems/controllers.min.js'],     dest: root + '/taoItems/views/dist/controllers.min.js' },
            { src: [out + '/taoItems/controllers.min.js.map'], dest: root + '/taoItems/views/dist/controllers.min.js.map' }
        ]
    };


    /*
     * Manual bundle of the legacy API and the OWI API
     */
    uglify.legacyApi = {
        files: [{
            dest: root + '/taoItems/views/js/legacyApi/taoLegacyApi.min.js',
            src: [
                root + '/taoItems/views/js/ItemApi/ItemApi.js',
                root + '/taoItems/views/js/legacyApi/taoLegacyApi.js'
            ]
        }]
    };

    uglify.taoApi = {
        files: [{
            dest: root + '/taoItems/views/js/taoApi/taoApi.min.js',
            src: [
                root + '/taoItems/views/js/taoApi/src/constants.js',
                root + '/taoItems/views/js/taoApi/src/core.js',
                root + '/taoItems/views/js/taoApi/src/events.js',
                root + '/taoItems/views/js/taoApi/src/api.js'
            ]
        }]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);
    grunt.config('uglify', uglify);

    // bundle task
    grunt.registerTask('taoitemsbundle', ['clean:bundle', 'requirejs:taoitems_bundle', 'copy:taoitems_bundle']);
};

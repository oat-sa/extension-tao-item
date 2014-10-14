module.exports = function(grunt) { 

    var requirejs   = grunt.config('requirejs') || {};
    var clean       = grunt.config('clean') || {};
    var copy        = grunt.config('copy') || {};

    var root        = grunt.option('root');
    var libs        = grunt.option('mainlibs');
    var ext         = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);

    /**
     * Remove bundled and bundling files
     */
    clean.taoitemsbundle = ['output',  root + '/taoItems/views/js/controllers.min.js'];
    
    /**
     * Compile tao files into a bundle 
     */
    requirejs.taoitemsbundle = {
        options: {
            baseUrl : '../js',
            dir : 'output',
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoItems' : root + '/taoItems/views/js' },
            modules : [{
                name: 'taoItems/controller/routes',
                include : ext.getExtensionsControllers(['taoItems']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taoitemsbundle = {
        files: [
            { src: ['output/taoItems/controller/routes.js'],  dest: root + '/taoItems/views/js/controllers.min.js' },
            { src: ['output/taoItems/controller/routes.js.map'],  dest: root + '/taoItems/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taoitemsbundle', ['clean:taoitemsbundle', 'requirejs:taoitemsbundle', 'copy:taoitemsbundle']);
};

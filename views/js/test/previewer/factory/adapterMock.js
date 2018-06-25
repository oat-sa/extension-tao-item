define(function () {
    'use strict';

    return {
        name: 'mock',
        init: function init(uri, state, config) {
            return {
                uri: uri,
                state: state,
                config: config,
                type: 'mock'
            };
        }
    };
});

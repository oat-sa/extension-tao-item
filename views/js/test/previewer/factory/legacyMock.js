define(function () {
    'use strict';

    return {
        name: 'legacy',
        init: function init(uri, state, config) {
            return {
                uri: uri,
                state: state,
                config: config,
                type: 'legacy'
            };
        }
    };
});

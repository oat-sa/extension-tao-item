define([
    'taoItems/assets/manager',
    'taoItems/assets/strategies'
], function(assetManagerFactory, strategies){


    QUnit.module('API');

    QUnit.test('module', 2, function(assert){
        assert.ok(typeof strategies !== 'undefined', "The module exports something");
        assert.ok(typeof strategies === 'object', "The module exports an object");
    });

    QUnit.module('BaseUrl strategy');

    QUnit.test('expected strategy format', 3, function(assert){

        assert.ok(typeof strategies.baseUrl === 'object', "The baseUrl strategy exists");
        assert.equal(strategies.baseUrl.name, 'baseUrl', "The baseUrl strategy has the right name");
        assert.ok(typeof strategies.baseUrl.handle === 'function', "The baseUrl strategy has an handler");
    });

    var baseUrlDataProvider = [{
        title    : 'absolute URL',
        baseUrl  : 'http://tao.localdomain',
        slashcat   : true,
        url      : 'http://tao.localdomain/test/test.html',
        resolved : '',
    }, {
        title    : 'relative root URL',
        baseUrl  : 'http://tao.localdomain',
        slashcat   : true,
        url      : '/test/test.html',
        resolved : 'http://tao.localdomain/test/test.html'
    }, {
        title    : 'relative URL',
        baseUrl  : 'http://tao.localdomain',
        slashcat   : true,
        url      : 'test/test.html',
        resolved : 'http://tao.localdomain/test/test.html'
    }, {
        title    : 'relative URL slash in baseUrl',
        baseUrl  : 'http://tao.localdomain/',
        slashcat   : true,
        url      : '/test/test.html',
        resolved : 'http://tao.localdomain/test/test.html'
    }, {
        title    : 'relative URL current directory',
        baseUrl  : 'http://tao.localdomain',
        slashcat   : true,
        url      : './test/test.html',
        resolved : 'http://tao.localdomain/test/test.html'
    }, {
        title    : 'relative URL current directory',
        baseUrl  : 'http://tao.localdomain?path=',
        url      : './test/test.html',
        resolved : 'http://tao.localdomain?path=./test/test.html'
    }];

    QUnit
        .cases(baseUrlDataProvider)
        .test('resolve ', function(data, assert){
            var assetManager = assetManagerFactory(strategies.baseUrl, data);
            assert.equal(assetManager.resolve(data.url), data.resolved, 'The Url is resolved');
        });

    QUnit.module('External strategy');

    QUnit.test('expected strategy format', 3, function(assert){

        assert.ok(typeof strategies.external === 'object', "The external strategy exists");
        assert.equal(strategies.external.name, 'external', "The external strategy has the right name");
        assert.ok(typeof strategies.external.handle === 'function', "The external strategy has an handler");
    });

    var externalDataProvider = [{
        title    : 'absolute URL',
        url      : 'http://tao.localdomain/test/test.html',
        resolved : 'http://tao.localdomain/test/test.html',
    }, {
        title    : 'relative root URL',
        url      : '/test/test.html',
        resolved : ''
    }, {
        title    : 'FTP absolute URL',
        url      : 'ftp://tao.localdomain',
        resolved : 'ftp://tao.localdomain'
    }, {
        title    : 'relative URL current directory',
        url      : './test/test.html',
        resolved : ''
    }];

    QUnit
        .cases(externalDataProvider)
        .test('resolve ', function(data, assert){
            var assetManager = assetManagerFactory(strategies.external);
            assert.equal(assetManager.resolve(data.url), data.resolved, 'The Url is resolved');
        });
});


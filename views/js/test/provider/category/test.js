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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */

/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'taoItems/provider/category',
    'core/promise'
], function(categoryProvider, Promise) {
    'use strict';

    var testConfig = {
        getExposedsByClass : {
            url : '/taoItems/views/js/test/provider/category/getExposedsByClass.json'
        },
        setExposed : {
            url : '/taoItems/views/js/test/provider/category/setExposed.json'
        }
    };

    QUnit.module('API');

    QUnit.test('module', function(assert) {
        QUnit.expect(3);

        assert.equal(typeof categoryProvider, 'function', "The categoryProvider module exposes a function");
        assert.equal(typeof categoryProvider(), 'object', "The categoryProvider factory produces an object");
        assert.notStrictEqual(categoryProvider(), categoryProvider(), "The categoryProvider factory provides a different object on each call");
    });

    QUnit.test('methods', function(assert) {
        var provider = categoryProvider();

        QUnit.expect(3);

        assert.equal(typeof provider, 'object', "The categoryProvider factory produces an object");
        assert.equal(typeof provider.getExposedsByClass, 'function', "The provider exposes the getExposedsByClass method");
        assert.equal(typeof provider.setExposed, 'function', "The provider exposes the setExposed method");
    });

    QUnit.module('getExposedsByClass');

    QUnit.asyncTest('success', function(assert) {
        var p;
        var provider = categoryProvider( testConfig );

        QUnit.expect(4);

        p = provider.getExposedsByClass('classUri');

        assert.ok(p instanceof Promise, 'The method returns a Promise');

        p.then(function(results){
            assert.equal(typeof results, 'object', 'The method resolve with an object');
            assert.equal(typeof results['http://bertaodev/tao.rdf#i1476892921160365'], 'boolean', 'The result contains the correct values');
            assert.equal(typeof results['http://bertaodev/tao.rdf#i1476892959348167'], 'boolean', 'The result contains the correct values');
            QUnit.start();
        }).catch(function(err){
            assert.ok(false, 'The method should not reject : ' + err.message);
            QUnit.start();
        });
    });

    QUnit.asyncTest('no uri', function(assert) {
        var provider;

        QUnit.expect(2);

        provider = categoryProvider( testConfig );

        provider.getExposedsByClass().then(function(){
            assert.ok(false, 'The method must not resolve');
            QUnit.start();
        }).catch(function(err){
            assert.ok(err instanceof TypeError, 'The method rejects');
            assert.equal(err.message, 'The class URI must be provided in the id parameter', 'The method rejects with the correct message');
            QUnit.start();
        });
    });


    QUnit.module('setExposed');

    QUnit.asyncTest('success', function(assert) {
        var p, provider;

        QUnit.expect(2);

        provider = categoryProvider( testConfig );
        p = provider.setExposed('propUri', true);

        assert.ok(p instanceof Promise, 'The method returns a Promise');

        p.then(function(result){
            assert.ok(result, 'The method resolve with true');

            QUnit.start();
        }).catch(function(err){
            assert.ok(false, 'The method should not reject : ' + err.message);
            QUnit.start();
        });
    });

    QUnit.asyncTest('no uri', function(assert) {
        var provider;

        QUnit.expect(2);

        provider = categoryProvider( testConfig );

        provider.setExposed().then(function(){
            assert.ok(false, 'The method must not resolve');
            QUnit.start();
        }).catch(function(err){
            assert.ok(err instanceof TypeError, 'The method rejects');
            assert.equal(err.message, 'The property URI must be provided in the id parameter', 'The method rejects with the correct message');
            QUnit.start();
        });
    });

    QUnit.asyncTest('no value', function(assert) {
        var provider;

        QUnit.expect(2);

        provider = categoryProvider( testConfig );

        provider.setExposed('propUri').then(function(){
            assert.ok(false, 'The method must not resolve');
            QUnit.start();
        }).catch(function(err){
            assert.ok(err instanceof TypeError, 'The method rejects');
            assert.equal(err.message, 'The exposed value is required.', 'The method rejects with the correct message');
            QUnit.start();
        });
    });
});

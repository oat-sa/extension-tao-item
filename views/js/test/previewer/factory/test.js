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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
define([
    'jquery',
    'core/promise',
    'taoItems/previewer/factory'
], function($, Promise, previewerFactory) {
    'use strict';

    QUnit.module('factory', {
        afterEach: function(assert) {
            previewerFactory.clearProviders();
        }
    });

    QUnit.test('module', function(assert) {
        var config = {};
        var uri = 'item1';
        var state = {
            RESPONSE: {
                base: {
                    string: null
                }
            }
        };
        assert.expect(5);
        assert.equal(typeof previewerFactory, 'function', 'The factory module exposes a function');
        assert.equal(typeof previewerFactory.registerProvider, 'function', 'The factory module exposes a function registerProvider()');
        assert.equal(typeof previewerFactory.getProvider, 'function', 'The factory module exposes a function getProvider()');
        assert.ok(previewerFactory('mock', uri, state, config) instanceof Promise, 'The factory produces a promise');
        assert.notStrictEqual(previewerFactory('mock', uri, state, config), previewerFactory('mock', uri, state, config), 'The factory provides a different promise on each call');
    });

    QUnit.test('load adapter', function(assert) {
        var ready = assert.async();
        var config = {};
        var uri = 'item1';
        var state = {
            RESPONSE: {
                base: {
                    string: null
                }
            }
        };
        var promise = previewerFactory('mock', uri, state, config);

        assert.expect(5);
        assert.ok(promise instanceof Promise, 'The factory produces a promise');
        promise
            .then(function(previewer) {
                assert.equal(previewer.uri, uri, 'The previewer contains the expected property (uri)');
                assert.equal(previewer.state, state, 'The previewer contains the expected property (state)');
                assert.equal(previewer.config, config, 'The previewer contains the expected property (config)');
                assert.equal(previewer.type, 'mock', 'The previewer has the right type');
                ready();
            })
            .catch(function(err) {
                console.error(err);
                assert.ok(false, 'The factory should not fail');
                ready();
            });
    });

    QUnit.test('legacy adapter', function(assert) {
        var ready = assert.async();
        var config = {};
        var uri = 'item1';
        var state = {
            RESPONSE: {
                base: {
                    string: null
                }
            }
        };
        var promise = previewerFactory('legacy', uri, state, config);

        assert.expect(5);
        assert.ok(promise instanceof Promise, 'The factory produces a promise');
        promise
            .then(function(previewer) {
                assert.equal(previewer.uri, uri, 'The previewer contains the expected property (uri)');
                assert.equal(previewer.state, state, 'The previewer contains the expected property (state)');
                assert.equal(previewer.config, config, 'The previewer contains the expected property (config)');
                assert.equal(previewer.type, 'legacy', 'The previewer has the right type');
                ready();
            })
            .catch(function(err) {
                console.error(err);
                assert.ok(false, 'The factory should not fail');
                ready();
            });
    });

    QUnit.test('fallback adapter', function(assert) {
        var ready = assert.async();
        var config = {};
        var uri = 'item1';
        var state = {
            RESPONSE: {
                base: {
                    string: null
                }
            }
        };
        var promise = previewerFactory('foo', uri, state, config);

        assert.expect(2);
        assert.ok(promise instanceof Promise, 'The factory produces a promise');
        promise
            .then(function() {
                assert.ok(false, 'The factory should raise an error if the adapter is unknown');
                ready();
            })
            .catch(function() {
                assert.ok(true, 'The factory should raise an error if the adapter is unknown');
                ready();
            });
    });

});

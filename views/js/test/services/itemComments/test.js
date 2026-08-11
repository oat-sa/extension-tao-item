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
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */
define([], function () {
    'use strict';

    const moduleId = 'taoItems/services/itemComments';
    const requestModuleId = 'core/request';
    const urlModuleId = 'util/url';

    function loadService(options) {
        const requestCalls = [];
        const routeCalls = [];

        define(requestModuleId, [], function () {
            return function (payload) {
                requestCalls.push(payload);
                if (options && options.requestReject) {
                    return Promise.reject(options.requestReject);
                }
                if (options && options.requestResponse) {
                    return Promise.resolve(options.requestResponse);
                }
                return Promise.resolve({ success: true, data: null });
            };
        });

        define(urlModuleId, [], function () {
            return {
                route() {
                    const args = Array.prototype.slice.call(arguments);
                    routeCalls.push(args);
                    return '/taoItems/RestResourceComments/index';
                }
            };
        });

        return new Promise(function (resolve, reject) {
            requirejs.undef(moduleId);
            require([moduleId], function (itemComments) {
                resolve({ itemComments: itemComments, requestCalls: requestCalls, routeCalls: routeCalls });
            }, reject);
        });
    }

    function undefMocks() {
        [moduleId, requestModuleId, urlModuleId].forEach(function (id) {
            requirejs.undef(id);
        });
    }

    QUnit.module('itemComments service', {
        afterEach: function () {
            undefMocks();
        }
    });

    QUnit.test('exposes RESOURCE_TYPE constants', function (assert) {
        const ready = assert.async();
        assert.expect(3);
        loadService({})
            .then(function (ctx) {
                assert.equal(ctx.itemComments.RESOURCE_TYPE.ITEM, 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item', 'ITEM const');
                assert.equal(ctx.itemComments.RESOURCE_TYPE.TEST, 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test', 'TEST const');
                assert.equal(ctx.itemComments.RESOURCE_TYPE.ASSET, 'http://www.tao.lu/Ontologies/TAOMedia.rdf#Media', 'ASSET const');
                ready();
            })
            .catch(function (err) {
                assert.ok(false, err && err.message);
                ready();
            });
    });

    QUnit.test('list builds GET route and returns response.data', function (assert) {
        const ready = assert.async();
        const resourceUri = 'item://1';
        const payload = {
            comments: [
                {
                    id: 'c1',
                    resourceUri: resourceUri,
                    resourceType: 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
                    authorId: 'u1',
                    authorLabel: 'Ada',
                    body: 'Hello',
                    createdAt: '2026-07-27T09:12:00Z',
                    edited: false,
                    resolved: false
                }
            ],
            count: 1
        };

        assert.expect(5);
        loadService({
            requestResponse: { success: true, data: payload }
        })
            .then(function (ctx) {
                return ctx.itemComments.list(resourceUri, ctx.itemComments.RESOURCE_TYPE.ITEM).then(function (data) {
                    assert.deepEqual(data, payload, 'returns response.data');
                    assert.deepEqual(
                        ctx.routeCalls[0],
                        [
                            'index',
                            'RestResourceComments',
                            'taoItems',
                            { resourceUri: resourceUri, resourceType: 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item' }
                        ],
                        'route args for list'
                    );
                    assert.equal(ctx.requestCalls.length, 1, 'one request');
                    assert.equal(ctx.requestCalls[0].method, 'GET', 'GET method');
                    assert.equal(ctx.requestCalls[0].noToken, true, 'noToken set');
                    ready();
                });
            })
            .catch(function (err) {
                assert.ok(false, err && err.message);
                ready();
            });
    });

    QUnit.test('list propagates rejected requests', function (assert) {
        const ready = assert.async();
        const failure = new Error('list failed');

        assert.expect(1);
        loadService({ requestReject: failure })
            .then(function (ctx) {
                return ctx.itemComments.list('item://1', ctx.itemComments.RESOURCE_TYPE.ITEM).then(
                    function () {
                        assert.ok(false, 'expected rejection');
                        ready();
                    },
                    function (err) {
                        assert.strictEqual(err, failure, 'rejects with request error');
                        ready();
                    }
                );
            })
            .catch(function (err) {
                assert.ok(false, err && err.message);
                ready();
            });
    });

    QUnit.test('create builds POST payload and returns response.data', function (assert) {
        const ready = assert.async();
        const resourceUri = 'item://1';
        const body = 'New note';
        const created = {
            id: 'c2',
            resourceUri: resourceUri,
            resourceType: 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
            authorId: 'u2',
            authorLabel: 'Grace',
            body: body,
            createdAt: '2026-07-27T10:00:00Z',
            edited: false,
            resolved: false
        };

        assert.expect(6);
        loadService({
            requestResponse: { success: true, data: created }
        })
            .then(function (ctx) {
                return ctx.itemComments
                    .create(resourceUri, ctx.itemComments.RESOURCE_TYPE.ITEM, body)
                    .then(function (data) {
                        assert.deepEqual(data, created, 'returns response.data');
                        assert.deepEqual(
                            ctx.routeCalls[0],
                            ['index', 'RestResourceComments', 'taoItems'],
                            'route args for create'
                        );
                        assert.equal(ctx.requestCalls[0].method, 'POST', 'POST method');
                        assert.deepEqual(
                            ctx.requestCalls[0].data,
                            { resourceUri: resourceUri, resourceType: 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item', body: body },
                            'POST payload'
                        );
                        assert.equal(ctx.requestCalls[0].noToken, true, 'noToken set');
                        assert.equal(typeof ctx.requestCalls[0].url, 'string', 'url present');
                        ready();
                    });
            })
            .catch(function (err) {
                assert.ok(false, err && err.message);
                ready();
            });
    });

    QUnit.test('create propagates rejected requests', function (assert) {
        const ready = assert.async();
        const failure = new Error('create failed');

        assert.expect(1);
        loadService({ requestReject: failure })
            .then(function (ctx) {
                return ctx.itemComments.create('item://1', ctx.itemComments.RESOURCE_TYPE.ITEM, 'x').then(
                    function () {
                        assert.ok(false, 'expected rejection');
                        ready();
                    },
                    function (err) {
                        assert.strictEqual(err, failure, 'rejects with request error');
                        ready();
                    }
                );
            })
            .catch(function (err) {
                assert.ok(false, err && err.message);
                ready();
            });
    });
});

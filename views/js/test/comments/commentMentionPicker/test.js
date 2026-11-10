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
define(['jquery', 'taoItems/comments/commentMentionPicker'], function ($, mentionPickerFactory) {
    'use strict';

    const SEARCH_WAIT_MS = 230;

    function wait(ms) {
        return new Promise(function (resolve) {
            window.setTimeout(resolve, ms);
        });
    }

    function deferred() {
        let resolve;
        let reject;
        const promise = new Promise(function (promiseResolve, promiseReject) {
            resolve = promiseResolve;
            reject = promiseReject;
        });

        return { promise: promise, resolve: resolve, reject: reject };
    }

    function createHarness() {
        const selected = [];
        const requests = [];

        const picker = mentionPickerFactory.create({
            getAnchorRect: function () {
                return {
                    top: 20,
                    right: 40,
                    bottom: 36,
                    left: 20,
                    width: 20,
                    height: 16
                };
            },
            searchUsers: function (query) {
                const request = deferred();
                requests.push({
                    query: query,
                    resolve: request.resolve,
                    reject: request.reject
                });
                return request.promise;
            },
            onSelect: function (user) {
                selected.push(user);
            }
        });

        return {
            picker: picker,
            requests: requests,
            selected: selected
        };
    }

    function createEnterEvent() {
        return {
            key: 'Enter',
            preventDefault: function () {}
        };
    }

    QUnit.module('commentMentionPicker state machine', {
        beforeEach: function () {
            this.pickers = [];
        },
        afterEach: function () {
            this.pickers.forEach(function (picker) {
                picker.destroy();
            });
            $('.item-comments-mention-picker').remove();
        }
    });

    QUnit.test('uses current query options and blocks selection while new query is pending', function (assert) {
        const done = assert.async();
        const harness = createHarness();
        const picker = harness.picker;
        this.pickers.push(picker);

        picker.open('al');

        wait(SEARCH_WAIT_MS)
            .then(function () {
                assert.equal(harness.requests.length, 1, 'first query requested');
                harness.requests[0].resolve({
                    users: [{ id: 'u-alice', login: 'alice', displayName: 'Alice' }],
                    limit: 40
                });
                return wait(0);
            })
            .then(function () {
                picker.updateQuery('bo');

                assert.strictEqual(
                    picker.handleKeyDown(createEnterEvent()),
                    false,
                    'selection is blocked while new query has no results yet'
                );
                assert.equal(harness.selected.length, 0, 'nothing selected from stale options');

                return wait(SEARCH_WAIT_MS);
            })
            .then(function () {
                assert.equal(harness.requests.length, 2, 'second query requested');
                harness.requests[1].resolve({
                    users: [{ id: 'u-bob', login: 'bob', displayName: 'Bob' }],
                    limit: 40
                });
                return wait(0);
            })
            .then(function () {
                assert.strictEqual(picker.handleKeyDown(createEnterEvent()), true, 'current query user is selectable');
                assert.deepEqual(
                    harness.selected.map(function (user) {
                        return user.login;
                    }),
                    ['bob'],
                    'selected user comes from current query only'
                );
                done();
            });
    });

    QUnit.test('ignores stale success response from an older query', function (assert) {
        const done = assert.async();
        const harness = createHarness();
        const picker = harness.picker;
        this.pickers.push(picker);

        picker.open('a');

        wait(SEARCH_WAIT_MS)
            .then(function () {
                picker.updateQuery('b');
                return wait(SEARCH_WAIT_MS);
            })
            .then(function () {
                assert.equal(harness.requests.length, 2, 'both requests are in flight');

                harness.requests[1].resolve({
                    users: [{ id: 'u-bob', login: 'bob', displayName: 'Bob' }],
                    limit: 40
                });

                return wait(0);
            })
            .then(function () {
                harness.requests[0].resolve({
                    users: [{ id: 'u-alice', login: 'alice', displayName: 'Alice' }],
                    limit: 40
                });

                return wait(0);
            })
            .then(function () {
                assert.strictEqual(picker.handleKeyDown(createEnterEvent()), true, 'selection stays enabled');
                assert.deepEqual(
                    harness.selected.map(function (user) {
                        return user.login;
                    }),
                    ['bob'],
                    'stale success does not replace latest users'
                );
                done();
            });
    });

    QUnit.test('ignores stale failure response from an older query', function (assert) {
        const done = assert.async();
        const harness = createHarness();
        const picker = harness.picker;
        this.pickers.push(picker);

        picker.open('a');

        wait(SEARCH_WAIT_MS)
            .then(function () {
                picker.updateQuery('b');
                return wait(SEARCH_WAIT_MS);
            })
            .then(function () {
                assert.equal(harness.requests.length, 2, 'both requests are in flight');

                harness.requests[1].resolve({
                    users: [{ id: 'u-bob', login: 'bob', displayName: 'Bob' }],
                    limit: 40
                });

                return wait(0);
            })
            .then(function () {
                harness.requests[0].reject(new Error('stale request failed'));

                return wait(0);
            })
            .then(function () {
                assert.strictEqual(picker.handleKeyDown(createEnterEvent()), true, 'selection remains available');
                assert.deepEqual(
                    harness.selected.map(function (user) {
                        return user.login;
                    }),
                    ['bob'],
                    'stale failure does not clear latest users'
                );
                done();
            });
    });

    QUnit.test('close invalidates pending response and keeps picker closed', function (assert) {
        const done = assert.async();
        const harness = createHarness();
        const picker = harness.picker;
        this.pickers.push(picker);

        picker.open('a');

        wait(SEARCH_WAIT_MS)
            .then(function () {
                assert.equal(harness.requests.length, 1, 'request started');
                picker.close();
                harness.requests[0].resolve({
                    users: [{ id: 'u-alice', login: 'alice', displayName: 'Alice' }],
                    limit: 40
                });
                return wait(0);
            })
            .then(function () {
                assert.strictEqual(picker.isOpen(), false, 'picker remains closed');
                assert.strictEqual(picker.handleKeyDown(createEnterEvent()), false, 'no selection after close');
                assert.equal(harness.selected.length, 0, 'pending response cannot insert user');
                done();
            });
    });

    QUnit.test('destroy invalidates pending response and removes picker root', function (assert) {
        const done = assert.async();
        const harness = createHarness();
        const picker = harness.picker;
        const beforeCount = $('.item-comments-mention-picker').length;
        this.pickers.push(picker);

        picker.open('a');

        wait(SEARCH_WAIT_MS)
            .then(function () {
                assert.equal(harness.requests.length, 1, 'request started');

                this.pickers.pop();
                picker.destroy();

                assert.equal(
                    $('.item-comments-mention-picker').length,
                    beforeCount - 1,
                    'destroy removes root element immediately'
                );

                harness.requests[0].resolve({
                    users: [{ id: 'u-alice', login: 'alice', displayName: 'Alice' }],
                    limit: 40
                });

                return wait(0);
            }.bind(this))
            .then(function () {
                assert.equal(harness.selected.length, 0, 'pending response ignored after destroy');
                done();
            });
    });

    QUnit.test('multiple picker instances stay isolated', function (assert) {
        const done = assert.async();
        const first = createHarness();
        const second = createHarness();

        this.pickers.push(first.picker);
        this.pickers.push(second.picker);

        first.picker.open('a');
        second.picker.open('b');

        wait(SEARCH_WAIT_MS)
            .then(function () {
                assert.equal(first.requests.length, 1, 'first picker requested once');
                assert.equal(second.requests.length, 1, 'second picker requested once');

                first.requests[0].resolve({
                    users: [{ id: 'u-alice', login: 'alice', displayName: 'Alice' }],
                    limit: 40
                });
                second.requests[0].resolve({
                    users: [{ id: 'u-bob', login: 'bob', displayName: 'Bob' }],
                    limit: 40
                });

                return wait(0);
            })
            .then(function () {
                first.picker.close();
                assert.strictEqual(second.picker.isOpen(), true, 'closing one instance does not close the other');

                assert.strictEqual(second.picker.handleKeyDown(createEnterEvent()), true, 'second picker still works');
                assert.deepEqual(
                    second.selected.map(function (user) {
                        return user.login;
                    }),
                    ['bob'],
                    'second picker keeps its own users'
                );
                done();
            });
    });
});

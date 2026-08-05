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
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */
define(['taoItems/comments/itemCommentsStore'], function (itemCommentsStoreFactory) {
    'use strict';

    QUnit.module('API');

    QUnit.test('factory', function (assert) {
        assert.expect(3);
        assert.equal(typeof itemCommentsStoreFactory, 'function', 'module exposes a factory');
        assert.equal(typeof itemCommentsStoreFactory({ itemUri: 'i1' }), 'object', 'factory returns an object');
        assert.notStrictEqual(
            itemCommentsStoreFactory({ itemUri: 'i1' }),
            itemCommentsStoreFactory({ itemUri: 'i1' }),
            'factory returns a new instance'
        );
    });

    QUnit.module('draft');

    QUnit.test('dirty draft detection', function (assert) {
        const store = itemCommentsStoreFactory({ itemUri: 'item://1' });
        assert.expect(4);
        assert.equal(store.hasDirtyDraft(), false, 'empty draft is not dirty');
        store.setDraft('   ');
        assert.equal(store.hasDirtyDraft(), false, 'whitespace-only draft is not dirty');
        store.setDraft(' hello ');
        assert.equal(store.hasDirtyDraft(), true, 'non-whitespace draft is dirty');
        store.clearDraft();
        assert.equal(store.hasDirtyDraft(), false, 'cleared draft is not dirty');
    });

    QUnit.module('load / submit');

    QUnit.test('load maps comments and count', function (assert) {
        const ready = assert.async();
        const comments = [
            {
                id: 'c1',
                itemUri: 'item://1',
                authorId: 'u1',
                authorLabel: 'Ada',
                body: 'First',
                createdAt: '2026-07-27T09:12:00Z',
                status: 'active',
                edited: false,
                resolved: false
            }
        ];
        const store = itemCommentsStoreFactory({
            itemUri: 'item://1',
            api: {
                list() {
                    return Promise.resolve({ comments, count: 1 });
                },
                create() {
                    return Promise.reject(new Error('unused'));
                }
            }
        });

        assert.expect(3);
        store.load().then(function () {
            assert.deepEqual(store.getComments(), comments, 'comments loaded');
            assert.equal(store.getCount(), 1, 'count loaded');
            return store.load();
        }).then(function () {
            assert.equal(store.getCount(), 1, 'cached load does not refetch');
            ready();
        }).catch(function (err) {
            assert.ok(false, err.message);
            ready();
        });
    });

    QUnit.test('submit appends comment and clears draft', function (assert) {
        const ready = assert.async();
        const created = {
            id: 'c2',
            itemUri: 'item://1',
            authorId: 'u2',
            authorLabel: 'Grace',
            body: 'New note',
            createdAt: '2026-07-27T10:00:00Z',
            status: 'active',
                edited: false,
                resolved: false
        };
        const store = itemCommentsStoreFactory({
            itemUri: 'item://1',
            api: {
                list() {
                    return Promise.resolve({ comments: [], count: 0 });
                },
                create(itemUri, body) {
                    assert.equal(itemUri, 'item://1', 'create receives itemUri');
                    assert.equal(body, 'New note', 'create receives trimmed body');
                    return Promise.resolve(created);
                }
            }
        });

        assert.expect(6);
        store
            .load()
            .then(function () {
                store.setDraft('  New note  ');
                return store.submit();
            })
            .then(function () {
                assert.equal(store.getComments().length, 1, 'comment appended');
                assert.equal(store.getCount(), 1, 'count incremented');
                assert.equal(store.getDraft(), '', 'draft cleared');
                assert.equal(store.hasDirtyDraft(), false, 'draft not dirty');
                ready();
            })
            .catch(function (err) {
                assert.ok(false, err.message);
                ready();
            });
    });

    QUnit.test('submit failure keeps draft', function (assert) {
        const ready = assert.async();
        const store = itemCommentsStoreFactory({
            itemUri: 'item://1',
            api: {
                list() {
                    return Promise.resolve({ comments: [], count: 0 });
                },
                create() {
                    return Promise.reject(new Error('boom'));
                }
            }
        });

        assert.expect(3);
        store.setDraft('keep me');
        store
            .submit()
            .then(function () {
                assert.ok(false, 'should reject');
                ready();
            })
            .catch(function () {
                assert.equal(store.getDraft(), 'keep me', 'draft retained');
                assert.equal(store.getCount(), 0, 'count unchanged');
                assert.equal(store.getComments().length, 0, 'no comment appended');
                ready();
            });
    });

    QUnit.test('setItemUri clears draft and cache', function (assert) {
        const store = itemCommentsStoreFactory({
            itemUri: 'item://1',
            api: {
                list() {
                    return Promise.resolve({
                        comments: [
                            {
                                id: 'c1',
                                itemUri: 'item://1',
                                authorId: 'u1',
                                authorLabel: 'Ada',
                                body: 'A',
                                createdAt: '2026-07-27T09:12:00Z',
                                status: 'active',
                edited: false,
                resolved: false
                            }
                        ],
                        count: 1
                    });
                },
                create() {
                    return Promise.reject(new Error('unused'));
                }
            }
        });
        const ready = assert.async();

        assert.expect(3);
        store
            .load()
            .then(function () {
                store.setDraft('pending');
                store.setItemUri('item://2');
                assert.equal(store.getDraft(), '', 'draft cleared on URI change');
                assert.equal(store.getCount(), 0, 'count reset');
                assert.deepEqual(store.getComments(), [], 'comments reset');
                ready();
            })
            .catch(function (err) {
                assert.ok(false, err.message);
                ready();
            });
    });
});

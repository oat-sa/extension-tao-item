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
define(['jquery', 'core/eventifier', 'taoItems/comments/commentsPanel'], function (
    $,
    eventifier,
    commentsPanelFactory
) {
    'use strict';

    const sampleComments = [
        {
            id: 'c1',
            authorLabel: 'admin',
            createdAt: '2026-08-24T09:19:00Z',
            body: 'Hello',
            edited: false,
            editable: true,
            resolved: false
        },
        {
            id: 'c2',
            authorLabel: 'admin',
            createdAt: '2026-08-24T09:20:00Z',
            body: 'Second',
            edited: false,
            editable: true,
            resolved: false
        }
    ];

    /**
     * @param {object[]} comments
     * @param {object} [spies]
     * @returns {object}
     */
    function createStore(comments, spies) {
        const apiSpies = spies || {};
        let list = (comments || []).slice();
        let draft = '';

        return eventifier({
            getComments() {
                return list.slice();
            },
            setComments(next) {
                list = (next || []).slice();
                this.trigger('loaded');
                return this;
            },
            getDraft() {
                return draft;
            },
            setDraft(value) {
                draft = typeof value === 'string' ? value : '';
                this.trigger('draftchange', draft);
                return this;
            },
            hasDirtyDraft() {
                return /\S/.test(draft);
            },
            isSubmitting() {
                return false;
            },
            load() {
                if (typeof apiSpies.load === 'function') {
                    return apiSpies.load();
                }
                this.trigger('loaded');
                return Promise.resolve(this);
            },
            update(commentId, body) {
                if (typeof apiSpies.update === 'function') {
                    return apiSpies.update(commentId, body);
                }
                return Promise.resolve({ id: commentId, body: body });
            },
            delete(commentId) {
                if (typeof apiSpies.delete === 'function') {
                    return apiSpies.delete(commentId);
                }
                list = list.filter(comment => String(comment.id) !== String(commentId));
                this.trigger('deleted', commentId);
                this.trigger('loaded');
                return Promise.resolve(this);
            },
            resolve(commentId, resolved) {
                if (typeof apiSpies.resolve === 'function') {
                    return apiSpies.resolve(commentId, resolved);
                }
                return Promise.resolve({ id: commentId, resolved: resolved });
            },
            submit() {
                return Promise.resolve(this);
            }
        });
    }

    /**
     * @param {HTMLElement} element
     * @param {object} rect
     */
    function stubBoundingRect(element, rect) {
        element.getBoundingClientRect = function () {
            return {
                top: rect.top,
                bottom: rect.bottom,
                left: rect.left,
                right: rect.right,
                width: rect.width != null ? rect.width : rect.right - rect.left,
                height: rect.height != null ? rect.height : rect.bottom - rect.top,
                x: rect.left,
                y: rect.top
            };
        };
    }

    /**
     * @param {HTMLElement} element
     * @param {string} property
     * @param {number} value
     */
    function stubMetric(element, property, value) {
        Object.defineProperty(element, property, {
            configurable: true,
            get() {
                return value;
            }
        });
    }

    /**
     * @param {jQuery} $host
     * @param {object} store
     * @returns {object}
     */
    function createPanel($host, store) {
        return commentsPanelFactory({
            renderTo: $host.get(0),
            store: store
        });
    }

    QUnit.module('API');

    QUnit.test('factory requires renderTo and store', function (assert) {
        assert.expect(2);
        assert.throws(
            function () {
                commentsPanelFactory({});
            },
            TypeError,
            'missing config throws'
        );
        assert.equal(typeof commentsPanelFactory, 'function', 'module exposes a factory');
    });

    QUnit.module('overlay lifecycle', {
        beforeEach() {
            $('#qunit-fixture').empty().append('<div class="comments-host"></div>');
        },
        afterEach() {
            $('#qunit-fixture').empty();
        }
    });

    QUnit.test('opens menu into overlay layer and restores to host on close', function (assert) {
        const store = createStore(sampleComments);
        const $host = $('#qunit-fixture .comments-host');
        const panel = createPanel($host, store);
        const $more = $host.find('.item-comment').first().find('.item-comment-more');
        const $toggle = $more.find('.item-comment-more-toggle');
        const $layer = $host.find('.item-comments-menu-layer');

        assert.expect(8);

        assert.equal($more.find('.item-comment-more-menu').length, 1, 'menu starts in row host');
        assert.equal($layer.children('.item-comment-more-menu').length, 0, 'layer starts empty');

        $toggle.trigger('click');

        const $openMenu = $layer.children('.item-comment-more-menu');
        assert.equal($openMenu.length, 1, 'open menu moves into overlay layer');
        assert.equal($openMenu.prop('hidden'), false, 'open menu is visible');
        assert.equal($layer.attr('aria-hidden'), 'false', 'layer is interactive while open');
        assert.equal($toggle.attr('aria-expanded'), 'true', 'toggle expanded while open');
        assert.ok($openMenu.data('menuHome') && $openMenu.data('menuHome').is($more), 'menuHome points at row host');

        $(document).trigger('click');

        assert.equal(
            $more.find('.item-comment-more-menu').length,
            1,
            'closing restores menu to original host'
        );

        panel.destroy();
    });

    QUnit.test('Escape restores focus to toggle when a menu item was focused', function (assert) {
        const store = createStore([sampleComments[0]]);
        const $host = $('#qunit-fixture .comments-host');
        const panel = createPanel($host, store);
        const $more = $host.find('.item-comment-more');
        const $toggle = $more.find('.item-comment-more-toggle');
        const $layer = $host.find('.item-comments-menu-layer');

        assert.expect(4);

        $toggle.trigger('click');
        const $edit = $layer.find('.item-comment-edit');
        $edit.trigger('focus');
        assert.strictEqual(document.activeElement, $edit.get(0), 'menu item receives focus');

        $(document).trigger($.Event('keydown', { key: 'Escape' }));

        assert.strictEqual(
            document.activeElement,
            $toggle.get(0),
            'Escape returns focus to more-actions toggle'
        );
        assert.equal($layer.children('.item-comment-more-menu').length, 0, 'overlay cleared on Escape');
        assert.equal($more.find('.item-comment-more-menu').length, 1, 'menu restored to host on Escape');

        panel.destroy();
    });

    QUnit.test('bottom-row placement opens upward when space below is insufficient', function (assert) {
        const store = createStore(sampleComments);
        const $host = $('#qunit-fixture .comments-host');
        const panel = createPanel($host, store);
        const $panel = $host.find('.item-comments-panel');
        const $more = $host.find('.item-comment').last().find('.item-comment-more');
        const $toggle = $more.find('.item-comment-more-toggle');
        const panelElement = $panel.get(0);
        const toggleElement = $toggle.get(0);

        assert.expect(3);

        stubBoundingRect(panelElement, { top: 0, bottom: 400, left: 0, right: 280 });
        stubMetric(panelElement, 'clientHeight', 400);
        stubMetric(panelElement, 'clientWidth', 280);
        stubBoundingRect(toggleElement, { top: 360, bottom: 378, left: 252, right: 270 });

        $toggle.trigger('click');

        const menuElement = $host.find('.item-comments-menu-layer .item-comment-more-menu').get(0);
        stubMetric(menuElement, 'offsetHeight', 56);
        stubMetric(menuElement, 'offsetWidth', 96);

        // Re-open so positionMoreMenu reads stubbed menu metrics.
        $(document).trigger('click');
        $toggle.trigger('click');

        const $menu = $host.find('.item-comments-menu-layer .item-comment-more-menu');
        const top = parseFloat($menu.css('top'));
        const left = parseFloat($menu.css('left'));

        // openUp: top = 360 - 0 - 56 - 2 = 302
        assert.equal(top, 302, 'menu opens above bottom-row toggle');
        assert.equal(left, 174, 'menu right-aligns to toggle (270 - 96)');
        assert.equal($menu.prop('hidden'), false, 'menu remains visible');

        panel.destroy();
    });

    QUnit.test('clamps menu inside panel bounds when preferred placement overflows', function (assert) {
        const store = createStore([sampleComments[0]]);
        const $host = $('#qunit-fixture .comments-host');
        const panel = createPanel($host, store);
        const $panel = $host.find('.item-comments-panel');
        const $toggle = $host.find('.item-comment-more-toggle');
        const panelElement = $panel.get(0);
        const toggleElement = $toggle.get(0);

        assert.expect(3);

        stubBoundingRect(panelElement, { top: 0, bottom: 100, left: 0, right: 120 });
        stubMetric(panelElement, 'clientHeight', 100);
        stubMetric(panelElement, 'clientWidth', 120);
        stubBoundingRect(toggleElement, { top: 40, bottom: 58, left: 92, right: 110 });

        $toggle.trigger('click');
        const menuElement = $host.find('.item-comments-menu-layer .item-comment-more-menu').get(0);
        stubMetric(menuElement, 'offsetHeight', 80);
        stubMetric(menuElement, 'offsetWidth', 96);
        $(document).trigger('click');
        $toggle.trigger('click');

        const $menu = $host.find('.item-comments-menu-layer .item-comment-more-menu');
        const top = parseFloat($menu.css('top'));
        const left = parseFloat($menu.css('left'));

        // preferred down top would be 60; maxTop = 100 - 80 = 20 => clamp to 20
        assert.equal(top, 20, 'vertical position clamps to panel maxTop');
        // preferred left = 110 - 96 = 14; maxLeft = 120 - 96 = 24 => stays 14
        assert.equal(left, 14, 'horizontal position stays within panel');
        assert.ok(top + 80 <= 100, 'clamped menu fits inside panel height');

        panel.destroy();
    });

    QUnit.test('scroll closes overlay and restores host state', function (assert) {
        const store = createStore(sampleComments);
        const $host = $('#qunit-fixture .comments-host');
        const panel = createPanel($host, store);
        const $list = $host.find('.item-comments-list');
        const $more = $host.find('.item-comment').first().find('.item-comment-more');
        const $toggle = $more.find('.item-comment-more-toggle');
        const $layer = $host.find('.item-comments-menu-layer');

        assert.expect(4);

        $toggle.trigger('click');
        assert.equal($layer.children('.item-comment-more-menu').length, 1, 'menu open in layer');

        $list.trigger('scroll');

        assert.equal($layer.children('.item-comment-more-menu').length, 0, 'scroll empties layer');
        assert.equal($more.find('.item-comment-more-menu').length, 1, 'menu restored to host');
        assert.equal($toggle.attr('aria-expanded'), 'false', 'toggle collapsed after scroll');

        panel.destroy();
    });

    QUnit.test('rerender closes overlay and cleans floating state', function (assert) {
        const store = createStore(sampleComments);
        const $host = $('#qunit-fixture .comments-host');
        const panel = createPanel($host, store);
        const $toggle = $host.find('.item-comment').first().find('.item-comment-more-toggle');
        const $layer = $host.find('.item-comments-menu-layer');

        assert.expect(3);

        $toggle.trigger('click');
        assert.equal($layer.children('.item-comment-more-menu').length, 1, 'menu open before rerender');

        store.setComments(sampleComments);

        assert.equal($layer.children('.item-comment-more-menu').length, 0, 'rerender clears layer menus');
        assert.equal($layer.attr('aria-hidden'), 'true', 'layer marked hidden after cleanup');

        panel.destroy();
    });

    QUnit.test('destroy closes menus and removes panel', function (assert) {
        const store = createStore(sampleComments);
        const $host = $('#qunit-fixture .comments-host');
        const panel = createPanel($host, store);
        const $toggle = $host.find('.item-comment-more-toggle').first();

        assert.expect(3);

        $toggle.trigger('click');
        assert.equal($host.find('.item-comments-menu-layer .item-comment-more-menu').length, 1, 'menu open');

        panel.destroy();

        assert.equal($host.find('.item-comments-panel').length, 0, 'panel removed on destroy');
        assert.equal($host.find('.item-comment-more-menu').length, 0, 'no orphaned menus remain');
    });

    QUnit.test('delegated edit from overlay opens edit form and restores menu host', function (assert) {
        const store = createStore([sampleComments[0]]);
        const $host = $('#qunit-fixture .comments-host');
        const panel = createPanel($host, store);
        const $article = $host.find('.item-comment');
        const $more = $article.find('.item-comment-more');
        const $toggle = $more.find('.item-comment-more-toggle');

        assert.expect(5);

        $toggle.trigger('click');
        $host.find('.item-comments-menu-layer .item-comment-edit').trigger('click');

        assert.equal($article.find('[data-role="edit-form"]').prop('hidden'), false, 'edit form shown');
        assert.equal($article.find('[data-role="body"]').prop('hidden'), true, 'body hidden while editing');
        assert.equal($article.find('[data-role="actions"]').prop('hidden'), true, 'actions hidden while editing');
        assert.equal($host.find('.item-comments-menu-layer .item-comment-more-menu').length, 0, 'overlay closed');
        assert.equal($more.find('.item-comment-more-menu').length, 1, 'menu restored to host after edit');

        panel.destroy();
    });

    QUnit.test('delegated delete from overlay calls store and cleans overlay', function (assert) {
        const ready = assert.async();
        const deleteCalls = [];
        const store = createStore(sampleComments, {
            delete(commentId) {
                deleteCalls.push(commentId);
                return Promise.resolve(this);
            }
        });
        const $host = $('#qunit-fixture .comments-host');
        const panel = createPanel($host, store);
        const $toggle = $host.find('.item-comment').first().find('.item-comment-more-toggle');

        assert.expect(3);

        $toggle.trigger('click');
        $host.find('.item-comments-menu-layer .item-comment-delete').trigger('click');

        window.setTimeout(function () {
            assert.deepEqual(deleteCalls, ['c1'], 'delete delegated with comment id');
            assert.equal(
                $host.find('.item-comments-menu-layer .item-comment-more-menu').length,
                0,
                'overlay closed after delete'
            );
            assert.equal(
                $host.find('.item-comment').first().find('.item-comment-more-menu').length,
                1,
                'menu restored to host after delete'
            );
            panel.destroy();
            ready();
        }, 0);
    });

    QUnit.module('negative cases', {
        beforeEach() {
            $('#qunit-fixture').empty().append('<div class="comments-host"></div>');
        },
        afterEach() {
            $('#qunit-fixture').empty();
        }
    });

    QUnit.test('missing menu element does not throw and leaves toggle collapsed', function (assert) {
        const store = createStore([sampleComments[0]]);
        const $host = $('#qunit-fixture .comments-host');
        const panel = createPanel($host, store);
        const $more = $host.find('.item-comment-more');
        const $toggle = $more.find('.item-comment-more-toggle');

        assert.expect(3);

        $more.find('.item-comment-more-menu').remove();

        assert.equal($more.find('.item-comment-more-menu').length, 0, 'menu intentionally missing');
        $toggle.trigger('click');
        assert.equal(
            $host.find('.item-comments-menu-layer .item-comment-more-menu').length,
            0,
            'no overlay menu created when source menu is missing'
        );
        assert.equal($toggle.attr('aria-expanded'), 'false', 'toggle stays collapsed');

        panel.destroy();
    });

    QUnit.test('edit with missing comment id is a no-op', function (assert) {
        const store = createStore([sampleComments[0]]);
        const $host = $('#qunit-fixture .comments-host');
        const panel = createPanel($host, store);
        const $article = $host.find('.item-comment');
        const $toggle = $article.find('.item-comment-more-toggle');

        assert.expect(3);

        $toggle.trigger('click');
        const $edit = $host.find('.item-comments-menu-layer .item-comment-edit');
        $edit.attr('data-comment-id', 'missing-id').data('comment-id', 'missing-id');
        $edit.trigger('click');

        assert.equal($article.find('[data-role="edit-form"]').prop('hidden'), true, 'edit form stays closed');
        assert.equal($article.find('[data-role="body"]').prop('hidden'), false, 'body stays visible');
        assert.equal($article.find('[data-role="actions"]').prop('hidden'), false, 'actions stay visible');

        panel.destroy();
    });

    QUnit.test('delete with missing comment id still closes overlay safely', function (assert) {
        const ready = assert.async();
        const deleteCalls = [];
        const store = createStore([sampleComments[0]], {
            delete(commentId) {
                deleteCalls.push(commentId);
                return Promise.resolve(this);
            }
        });
        const $host = $('#qunit-fixture .comments-host');
        const panel = createPanel($host, store);
        const $more = $host.find('.item-comment-more');
        const $toggle = $more.find('.item-comment-more-toggle');

        assert.expect(3);

        $toggle.trigger('click');
        const $delete = $host.find('.item-comments-menu-layer .item-comment-delete');
        $delete.removeAttr('data-comment-id').removeData('comment-id');
        $delete.trigger('click');

        window.setTimeout(function () {
            assert.equal(deleteCalls.length, 1, 'delete still invoked');
            assert.strictEqual(deleteCalls[0], undefined, 'missing id yields undefined payload');
            assert.equal(
                $host.find('.item-comments-menu-layer .item-comment-more-menu').length,
                0,
                'overlay cleaned even when comment id is missing'
            );
            panel.destroy();
            ready();
        }, 0);
    });
});

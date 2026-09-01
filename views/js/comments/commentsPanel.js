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

/**
 * Shared authoring comments panel widget (list / draft / row actions).
 * Host shells (Item / Test) own mode tabs and mount targets.
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'core/eventifier',
    'tpl!taoItems/comments/tpl/panel',
    'tpl!taoItems/comments/tpl/comment',
    'css!taoItemsCss/comments-panel'
], function ($, _, __, eventifier, panelTpl, commentTpl) {
    'use strict';

    let instanceSeq = 0;

    /**
     * Format ISO timestamp for display (en-GB style close to mockup).
     * @param {string} iso
     * @returns {string}
     */
    function formatDisplayTime(iso) {
        if (!iso) {
            return '';
        }
        const date = new Date(iso);
        if (Number.isNaN(date.getTime())) {
            return iso;
        }
        return new Intl.DateTimeFormat('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        })
            .format(date)
            .replace(',', '');
    }

    /**
     * @param {object} config
     * @param {HTMLElement|jQuery} config.renderTo
     * @param {object} config.store authoring comments store instance
     * @param {object} [config.labels] optional message overrides
     * @returns {object}
     */
    function commentsPanelFactory(config) {
        if (!config || !config.renderTo || !config.store) {
            throw new TypeError('commentsPanel requires renderTo and store');
        }

        const store = config.store;
        const labels = config.labels || {};
        const ns = `.commentsPanel${++instanceSeq}`;
        const $host = $(config.renderTo);
        const $panel = $(panelTpl());
        const $list = $panel.find('.item-comments-list');
        const $empty = $panel.find('.item-comments-empty');
        const $error = $panel.find('.item-comments-error');
        const $form = $panel.find('.item-comments-entry');
        const $input = $panel.find('.item-comments-input');
        const $submit = $panel.find('.item-comments-submit');
        const $menuLayer = $panel.find('.item-comments-menu-layer');

        $host.empty().append($panel);

        /**
         * @param {string} message
         */
        function showError(message) {
            $error.text(message).prop('hidden', false);
        }

        function clearError() {
            $error.text('').prop('hidden', true);
        }

        function scrollToNewest() {
            const list = $list.get(0);
            if (list) {
                list.scrollTop = list.scrollHeight;
            }
        }

        function renderComments() {
            closeMoreMenus();
            const comments = store.getComments();
            $list.empty();

            if (!comments.length) {
                $empty.prop('hidden', false);
            } else {
                $empty.prop('hidden', true);
                comments.forEach(comment => {
                    const resolved = !!comment.resolved;
                    // Ownership: prefer deletable; fall back to editable for older payloads.
                    const deletable = !!(
                        typeof comment.deletable === 'boolean' ? comment.deletable : comment.editable
                    );
                    // Own active comments are editable; resolved comments never are.
                    const editable = deletable && !resolved;

                    $list.append(
                        commentTpl({
                            id: comment.id,
                            authorLabel: comment.authorLabel,
                            createdAt: comment.createdAt,
                            displayTime: formatDisplayTime(comment.createdAt),
                            body: comment.body,
                            edited: !!comment.edited,
                            editable: editable,
                            deletable: deletable,
                            resolved: resolved
                        })
                    );
                });
            }

            $input.val(store.getDraft());
            $submit.prop('disabled', !store.hasDirtyDraft() || store.isSubmitting());
        }

        /**
         * Return floating menus to their row hosts and hide them.
         * @param {jQuery} [$keep] optional .item-comment-more to leave open
         */
        function closeMoreMenus($keep) {
            $menuLayer.find('.item-comment-more-menu').each(function () {
                const $menu = $(this);
                const $home = $menu.data('menuHome');
                if ($keep && $home && $home.length && $home.is($keep)) {
                    return;
                }
                if ($home && $home.length) {
                    const menuElement = $menu.get(0);
                    const shouldRestoreFocus =
                        menuElement &&
                        (menuElement === document.activeElement ||
                            $.contains(menuElement, document.activeElement));

                    $menu
                        .prop('hidden', true)
                        .css({ top: '', left: '', right: '', bottom: '' })
                        .removeData('menuHome')
                        .appendTo($home);
                    const $toggle = $home.find('.item-comment-more-toggle');
                    $toggle.attr('aria-expanded', 'false');
                    if (shouldRestoreFocus) {
                        $toggle.trigger('focus');
                    }
                } else {
                    $menu.remove();
                }
            });

            $list.find('.item-comment-more').each(function () {
                const $more = $(this);
                if ($keep && $more.is($keep)) {
                    return;
                }
                $more
                    .find('.item-comment-more-menu')
                    .prop('hidden', true)
                    .css({ top: '', left: '', right: '', bottom: '' });
                $more.find('.item-comment-more-toggle').attr('aria-expanded', 'false');
            });

            if (!$menuLayer.children('.item-comment-more-menu').length) {
                $menuLayer.attr('aria-hidden', 'true');
            }
        }

        /**
         * Place menu in the panel overlay so it can overlay list/entry frames.
         * Prefers below the trigger; opens above when needed; clamps into panel.
         * @param {jQuery} $more
         * @param {jQuery} $menu
         */
        function positionMoreMenu($more, $menu) {
            const panelElement = $panel.get(0);
            const toggleElement = $more.find('.item-comment-more-toggle').get(0);
            const menuElement = $menu.get(0);

            if (!panelElement || !toggleElement || !menuElement) {
                return;
            }

            $menu.data('menuHome', $more);
            $menuLayer.append($menu).attr('aria-hidden', 'false');
            $menu.prop('hidden', false);

            const panelRect = panelElement.getBoundingClientRect();
            const toggleRect = toggleElement.getBoundingClientRect();
            const menuHeight = menuElement.offsetHeight;
            const menuWidth = menuElement.offsetWidth;
            const gap = 2;
            const spaceBelow = panelRect.bottom - toggleRect.bottom - gap;
            const spaceAbove = toggleRect.top - panelRect.top - gap;
            const openUp = spaceBelow < menuHeight && spaceAbove >= spaceBelow;

            let top = openUp
                ? toggleRect.top - panelRect.top - menuHeight - gap
                : toggleRect.bottom - panelRect.top + gap;
            let left = toggleRect.right - panelRect.left - menuWidth;

            const maxTop = Math.max(0, panelElement.clientHeight - menuHeight);
            const maxLeft = Math.max(0, panelElement.clientWidth - menuWidth);
            top = Math.max(0, Math.min(top, maxTop));
            left = Math.max(0, Math.min(left, maxLeft));

            $menu.css({
                top: `${top}px`,
                left: `${left}px`,
                right: 'auto',
                bottom: 'auto'
            });
        }

        function closeEditForms($keep) {
            $list.find('[data-role="edit-form"]').each(function () {
                const $editForm = $(this);
                if ($keep && $editForm.is($keep)) {
                    return;
                }
                const $article = $editForm.closest('.item-comment');
                $editForm.prop('hidden', true);
                $article.find('[data-role="body"]').prop('hidden', false);
                $article.find('[data-role="actions"]').prop('hidden', false);
            });
        }

        store
            .on(
                [
                    `loaded${ns}`,
                    `countchange${ns}`,
                    `submitted${ns}`,
                    `updated${ns}`,
                    `resolved${ns}`,
                    `deleted${ns}`
                ].join(' '),
                () => {
                    renderComments();
                }
            )
            .on(`draftchange${ns}`, draft => {
                $submit.prop('disabled', !/\S/.test(draft || ''));
            })
            .on(`submitFailed${ns}`, () => {
                showError(labels.submitFailed || __('The comment was not saved.'));
            })
            .on(`updateFailed${ns}`, () => {
                showError(labels.updateFailed || __('The comment was not updated.'));
            })
            .on(`resolveFailed${ns}`, () => {
                showError(labels.resolveFailed || __('The comment was not resolved.'));
            })
            .on(`deleteFailed${ns}`, () => {
                showError(labels.deleteFailed || __('The comment was not deleted.'));
            })
            .on(`error${ns}`, () => {
                showError(labels.loadFailed || __('Unable to load comments.'));
            })
            .on(`submitted${ns}`, () => {
                clearError();
                scrollToNewest();
            })
            .on([`updated${ns}`, `resolved${ns}`, `deleted${ns}`].join(' '), () => {
                clearError();
            });

        $input.on(`input${ns}`, () => {
            store.setDraft($input.val());
        });

        $form.on(`submit${ns}`, e => {
            e.preventDefault();
            if ($submit.prop('disabled')) {
                return;
            }
            store
                .submit()
                .then(() => {
                    $input.val('');
                })
                .catch(_.noop);
        });

        $list.on(`click${ns}`, '.item-comment-more-toggle', e => {
            e.preventDefault();
            e.stopPropagation();
            const $toggle = $(e.currentTarget);
            const $more = $toggle.closest('.item-comment-more');
            let $menu = $more.find('.item-comment-more-menu');
            if (!$menu.length) {
                $menu = $menuLayer.children('.item-comment-more-menu').filter(function () {
                    const $home = $(this).data('menuHome');
                    return $home && $home.is($more);
                });
            }
            const isOpen = $menu.length && !$menu.prop('hidden') && $menu.parent().is($menuLayer);
            if (isOpen) {
                closeMoreMenus();
                return;
            }
            closeMoreMenus($more);
            if (!$menu.length) {
                return;
            }
            positionMoreMenu($more, $menu);
            $toggle.attr('aria-expanded', 'true');
        });

        $list.on(`scroll${ns}`, () => {
            closeMoreMenus();
        });

        $panel.on(`click${ns}`, '.item-comment-edit', e => {
            e.preventDefault();
            e.stopPropagation();
            const $button = $(e.currentTarget);
            const commentId = $button.data('comment-id');
            const $article = $list.find('.item-comment').filter(function () {
                return String($(this).data('comment-id')) === String(commentId);
            });
            if (!$article.length) {
                return;
            }
            const $editForm = $article.find('[data-role="edit-form"]');
            closeMoreMenus();
            closeEditForms($editForm);
            $article.find('[data-role="body"]').prop('hidden', true);
            $article.find('[data-role="actions"]').prop('hidden', true);
            $editForm.prop('hidden', false);
            $editForm.find('.item-comment-edit-input').trigger('focus');
        });

        $list.on(`click${ns}`, '.item-comment-cancel', e => {
            e.preventDefault();
            closeEditForms();
        });

        $list.on(`click${ns}`, '.item-comment-save', e => {
            e.preventDefault();
            const $button = $(e.currentTarget);
            const commentId = $button.data('comment-id');
            const $article = $button.closest('.item-comment');
            const body = $article.find('.item-comment-edit-input').val();
            $button.prop('disabled', true);
            store
                .update(commentId, body)
                .catch(_.noop)
                .then(() => {
                    $button.prop('disabled', false);
                });
        });

        $list.on(`click${ns}`, '.item-comment-resolve-link', e => {
            e.preventDefault();
            closeMoreMenus();
            const $link = $(e.currentTarget);
            if ($link.attr('aria-disabled') === 'true') {
                return;
            }
            const commentId = $link.data('comment-id');
            const resolved = $link.data('action') === 'resolve';
            $link.attr('aria-disabled', 'true');
            store
                .resolve(commentId, resolved)
                .catch(_.noop)
                .then(() => {
                    $link.attr('aria-disabled', 'false');
                });
        });

        $panel.on(`click${ns}`, '.item-comment-delete', e => {
            e.preventDefault();
            e.stopPropagation();
            closeMoreMenus();
            const commentId = $(e.currentTarget).data('comment-id');
            store.delete(commentId).catch(_.noop);
        });

        $(document).on(`click${ns}`, () => {
            closeMoreMenus();
        });

        $(document).on(`keydown${ns}`, e => {
            if (e.key === 'Escape') {
                closeMoreMenus();
                closeEditForms();
            }
        });

        const api = {
            render() {
                renderComments();
                return this;
            },

            refresh() {
                renderComments();
                scrollToNewest();
                return store.load().catch(_.noop);
            },

            scrollToNewest() {
                scrollToNewest();
                return this;
            },

            destroy() {
                closeMoreMenus();
                $(document).off(ns);
                $form.off(ns);
                $input.off(ns);
                $list.off(ns);
                $panel.off(ns);
                store.off(ns);
                $panel.remove();
                return this;
            }
        };

        renderComments();

        return eventifier(api);
    }

    return commentsPanelFactory;
});

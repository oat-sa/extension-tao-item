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
    'taoItems/comments/commentRichTextEditor',
    'taoItems/services/itemComments',
    'tpl!taoItems/comments/tpl/panel',
    'tpl!taoItems/comments/tpl/comment',
    'css!taoItemsCss/comments-panel'
], function ($, _, __, eventifier, richTextEditor, itemCommentsApi, panelTpl, commentTpl) {
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
        const $draftEditorHost = $panel.find('[data-role="draft-editor"]');
        const $draftToolbar = $panel.find('[data-role="draft-toolbar"]');
        const $submit = $panel.find('.item-comments-submit');
        const $menuLayer = $('<div class="item-comments-menu-layer" aria-hidden="true"></div>');

        const editEditors = {};

        $host.empty().append($panel);
        $panel.prepend($menuLayer);

        function searchMentionUsers(query, limit) {
            return itemCommentsApi.searchMentionUsers(
                store.getResourceUri(),
                store.getResourceType ? store.getResourceType() : itemCommentsApi.RESOURCE_TYPE.ITEM,
                query,
                { limit: limit || 40, offset: 0 }
            );
        }

        const draftEditor = richTextEditor.create({
            host: $draftEditorHost,
            toolbar: $draftToolbar,
            placeholder: labels.placeholder || __('Add a comment'),
            initialValue: store.getDraft(),
            searchUsers: searchMentionUsers,
            mentionInfoMessage: labels.mentionInfo || __('Only users with access to this item can be mentioned.'),
            onChange(value) {
                store.setDraft(value);
            }
        });

        $panel.on('click' + ns, '[data-role="mention-guidance"]', function (event) {
            event.preventDefault();
            draftEditor.startMention();
        });

        $panel.on('click' + ns, '[data-role="mention-guidance-edit"]', function (event) {
            event.preventDefault();
            const commentId = String($(event.currentTarget).data('comment-id') || '');
            const editor = getEditEditor(commentId);
            if (editor && typeof editor.startMention === 'function') {
                editor.startMention();
            }
        });

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
            Object.keys(editEditors).forEach(commentId => {
                editEditors[commentId].destroy();
                delete editEditors[commentId];
            });

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

                    const $commentNode = $(
                        commentTpl({
                            id: comment.id,
                            authorLabel: comment.authorLabel,
                            createdAt: comment.createdAt,
                            displayTime: formatDisplayTime(comment.createdAt),
                            edited: !!comment.edited,
                            editable: editable,
                            deletable: deletable,
                            resolved: resolved
                        })
                    );

                    $commentNode
                        .find('[data-role="body"]')
                        .html(richTextEditor.sanitizeHtml(comment.body));

                    $list.append($commentNode);
                });
            }

            if (draftEditor.getData() !== store.getDraft()) {
                draftEditor.setData(store.getDraft());
            }
            $submit.prop('disabled', !store.hasDirtyDraft() || store.isSubmitting());
        }

        function findComment(commentId) {
            return store.getComments().find(comment => comment.id === commentId) || null;
        }

        /**
         * Create (or recreate) the edit CKEditor for a comment row.
         * Call only when opening Edit — not on Save (that would wipe in-progress edits).
         */
        function openEditEditor($article, commentId) {
            const comment = findComment(commentId);
            const body = comment ? comment.body : '';
            const $editorHost = $article.find('[data-role="edit-editor"]');
            const $toolbar = $article.find('[data-role="edit-toolbar"]');

            if (editEditors[commentId]) {
                editEditors[commentId].destroy();
                delete editEditors[commentId];
            }

            $editorHost.empty();
            editEditors[commentId] = richTextEditor.create({
                host: $editorHost,
                toolbar: $toolbar,
                initialValue: body,
                searchUsers: searchMentionUsers,
                mentionInfoMessage:
                    labels.mentionInfo || __('Only users with access to this item can be mentioned.')
            });

            return editEditors[commentId];
        }

        function getEditEditor(commentId) {
            return editEditors[commentId] || null;
        }

        function findMenuForMore($more) {
            const $inlineMenu = $more.find('.item-comment-more-menu').first();
            if ($inlineMenu.length) {
                return $inlineMenu;
            }

            return $menuLayer
                .children('.item-comment-more-menu')
                .filter(function () {
                    const $home = $(this).data('menuHome');
                    return $home && $home.length && $home.is($more);
                })
                .first();
        }

        function closeLayerMenus($keep) {
            $menuLayer.children('.item-comment-more-menu').each(function () {
                const $menu = $(this);
                const $home = $menu.data('menuHome');
                if ($keep && $home && $home.length && $home.is($keep)) {
                    return;
                }

                $menu
                    .prop('hidden', true)
                    .removeClass('item-comment-more-menu--up item-comment-more-menu--down')
                    .css({ top: '', left: '', right: '', bottom: '' });

                if ($home && $home.length) {
                    $home.find('.item-comment-more-toggle').attr('aria-expanded', 'false');
                    $home.append($menu);
                }
            });

            $menuLayer.attr('aria-hidden', $menuLayer.children('.item-comment-more-menu').length ? 'false' : 'true');
        }

        function closeMoreMenus($keep) {
            closeLayerMenus($keep);

            $list.find('.item-comment-more').each(function () {
                const $more = $(this);
                if ($keep && $more.is($keep)) {
                    return;
                }
                $more.find('.item-comment-more-menu')
                    .prop('hidden', true)
                    .removeClass('item-comment-more-menu--up item-comment-more-menu--down');
                $more.find('.item-comment-more-toggle').attr('aria-expanded', 'false');
            });
        }

        function positionMoreMenu($more, $menu) {
            const listElement = $panel.get(0);
            const listViewportElement = $list.get(0);
            const moreElement = $more.get(0);
            const toggleElement = $more.find('.item-comment-more-toggle').get(0);
            const menuElement = $menu.get(0);

            if (!listElement || !moreElement || !menuElement) {
                return;
            }

            const listRect = listElement.getBoundingClientRect();
            const listViewportRect = listViewportElement ? listViewportElement.getBoundingClientRect() : listRect;
            const moreRectCandidate = moreElement.getBoundingClientRect();
            const toggleRectCandidate = toggleElement ? toggleElement.getBoundingClientRect() : null;

            function hasRectPosition(rect) {
                return !!rect && (
                    rect.top !== 0 ||
                    rect.bottom !== 0 ||
                    rect.left !== 0 ||
                    rect.right !== 0
                );
            }

            const placementRect = hasRectPosition(moreRectCandidate)
                ? moreRectCandidate
                : (hasRectPosition(toggleRectCandidate) ? toggleRectCandidate : moreRectCandidate);
            const anchorRect = hasRectPosition(toggleRectCandidate) ? toggleRectCandidate : placementRect;
            const menuHeight = menuElement.offsetHeight;
            const menuWidth = menuElement.offsetWidth;
            const listHeight = listElement.clientHeight || (listRect.bottom - listRect.top);
            const listWidth = listElement.clientWidth || (listRect.right - listRect.left);
            const referenceRect = hasRectPosition(listViewportRect) ? listViewportRect : listRect;
            const spaceBelow = referenceRect.bottom - placementRect.bottom;
            const spaceAbove = placementRect.top - referenceRect.top;
            const openUp = spaceBelow < menuHeight && spaceAbove > spaceBelow;
            const preferredTop = openUp
                ? anchorRect.top - listRect.top - menuHeight - 2
                : anchorRect.bottom - listRect.top + 2;
            const preferredLeft = anchorRect.right - listRect.left - menuWidth;
            const maxTop = Math.max(0, listHeight - menuHeight);
            const maxLeft = Math.max(0, listWidth - menuWidth);
            const top = Math.min(Math.max(0, preferredTop), maxTop);
            const left = Math.min(Math.max(0, preferredLeft), maxLeft);

            $menu
                .removeClass('item-comment-more-menu--up item-comment-more-menu--down')
                .addClass(openUp ? 'item-comment-more-menu--up' : 'item-comment-more-menu--down')
                .css({
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
                $submit.prop('disabled', !store.hasDirtyDraft());
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
                draftEditor.setData('');
            })
            .on([`updated${ns}`, `resolved${ns}`, `deleted${ns}`].join(' '), () => {
                clearError();
            });

        $form.on(`submit${ns}`, e => {
            e.preventDefault();
            if ($submit.prop('disabled')) {
                return;
            }
            store
                .submit()
                .catch(_.noop);
        });

        $list.on(`click${ns}`, '.item-comment-more-toggle', e => {
            e.preventDefault();
            e.stopPropagation();
            const $toggle = $(e.currentTarget);
            const $more = $toggle.closest('.item-comment-more');
            const $menu = findMenuForMore($more);

            if (!$menu.length) {
                $toggle.attr('aria-expanded', 'false');
                closeMoreMenus();
                return;
            }

            const isOpen = !$menu.prop('hidden') && $menu.parent().is($menuLayer);
            const willOpen = !isOpen;

            closeMoreMenus(willOpen ? $more : null);

            if (willOpen) {
                $menu.data('menuHome', $more);
                $menuLayer.append($menu);
                $menu.prop('hidden', false);
                positionMoreMenu($more, $menu);
                $menuLayer.attr('aria-hidden', 'false');
            }
            $toggle.attr('aria-expanded', willOpen ? 'true' : 'false');
        });

        $panel.on(`click${ns}`, '.item-comment-edit', e => {
            e.preventDefault();
            const $button = $(e.currentTarget);
            const commentId = String($button.data('comment-id') || '');
            const $article = $list.find(`.item-comment[data-comment-id="${commentId}"]`).first();

            if (!$article.length || !commentId) {
                closeMoreMenus();
                return;
            }

            const $editForm = $article.find('[data-role="edit-form"]');
            closeMoreMenus();
            closeEditForms($editForm);
            $article.find('[data-role="body"]').prop('hidden', true);
            $article.find('[data-role="actions"]').prop('hidden', true);
            $editForm.prop('hidden', false);
            const editor = openEditEditor($article, commentId);
            editor.focus();
        });

        $list.on(`click${ns}`, '.item-comment-cancel', e => {
            e.preventDefault();
            closeEditForms();
        });

        $list.on(`click${ns}`, '.item-comment-save', e => {
            e.preventDefault();
            const $button = $(e.currentTarget);
            const commentId = String($button.data('comment-id') || '');
            const editor = getEditEditor(commentId);
            if (!editor) {
                showError(labels.updateFailed || __('The comment was not updated.'));
                return;
            }
            const body = editor.getData();
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
            closeMoreMenus();
            const commentId = $(e.currentTarget).data('comment-id');
            store.delete(commentId).catch(_.noop);
        });

        $list.on(`scroll${ns}`, () => {
            closeMoreMenus();
        });

        $(document).on(`click${ns}`, () => {
            closeMoreMenus();
        });

        $(document).on(`keydown${ns}`, e => {
            if (e.key === 'Escape') {
                const $focusedMenuItem = $(document.activeElement).closest('.item-comment-more-menu');
                const $focusedMenuHome = $focusedMenuItem.length ? $focusedMenuItem.data('menuHome') : null;
                closeMoreMenus();
                closeEditForms();
                if ($focusedMenuHome && $focusedMenuHome.length) {
                    $focusedMenuHome.find('.item-comment-more-toggle').trigger('focus');
                }
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
                $list.off(ns);
                $panel.off(ns);
                store.off(ns);
                draftEditor.destroy();
                Object.keys(editEditors).forEach(commentId => {
                    editEditors[commentId].destroy();
                    delete editEditors[commentId];
                });
                $panel.remove();
                return this;
            }
        };

        renderComments();

        return eventifier(api);
    }

    return commentsPanelFactory;
});

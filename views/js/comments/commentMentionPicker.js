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
define(['jquery', 'lodash', 'i18n'], function ($, _, __) {
    'use strict';

    const VISIBLE_ROWS = 5;
    const ROW_HEIGHT_PX = 32;
    const SEARCH_DEBOUNCE_MS = 200;
    const FETCH_LIMIT = 40;
    const PICKER_WIDTH_PX = 280;
    const VIEWPORT_GAP_PX = 8;
    const CARET_GAP_PX = 4;

    /**
     * Floating @mention picker positioned next to the caret (GitHub-style).
     *
     * @param {object} config
     * @param {function(): ({top:number,right:number,bottom:number,left:number,width:number,height:number}|null)} config.getAnchorRect
     * @param {function(string, number): Promise<{users: Array<{id:string,login:string,displayName:string}>, total:number}>} config.searchUsers
     * @param {function({id:string,login:string,displayName:string}): void} config.onSelect
     * @param {string} [config.infoMessage]
     * @returns {object}
     */
    function createMentionPicker(config) {
        const getAnchorRect = config.getAnchorRect;
        const searchUsers = config.searchUsers;
        const onSelect = config.onSelect;
        const infoMessage =
            config.infoMessage || __('Only users with access to this item can be mentioned.');

        const $root = $(
            '<div class="item-comments-mention-picker" role="listbox" hidden>' +
                '<div class="item-comments-mention-picker__info"></div>' +
                '<div class="item-comments-mention-picker__empty" hidden></div>' +
                '<ul class="item-comments-mention-picker__list"></ul>' +
                '</div>'
        );
        const $info = $root.find('.item-comments-mention-picker__info').text(infoMessage);
        const $empty = $root.find('.item-comments-mention-picker__empty');
        const $list = $root.find('.item-comments-mention-picker__list');

        // Body mount + fixed positioning so the list sits next to @, not at panel top.
        $(document.body).append($root);
        $list.css('max-height', VISIBLE_ROWS * ROW_HEIGHT_PX + 'px');

        let open = false;
        let activeIndex = -1;
        let users = [];
        let currentQuery = null;
        let requestSeq = 0;

        function positionNearCaret() {
            if (!open) {
                return;
            }

            const rect =
                typeof getAnchorRect === 'function' ? getAnchorRect() : null;
            if (!rect) {
                return;
            }

            const pickerHeight = $root.outerHeight() || VISIBLE_ROWS * ROW_HEIGHT_PX + 40;
            const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

            let top = rect.bottom + CARET_GAP_PX;
            if (top + pickerHeight > viewportHeight - VIEWPORT_GAP_PX) {
                top = rect.top - pickerHeight - CARET_GAP_PX;
            }
            top = Math.max(
                VIEWPORT_GAP_PX,
                Math.min(top, viewportHeight - pickerHeight - VIEWPORT_GAP_PX)
            );

            let left = rect.left;
            left = Math.max(
                VIEWPORT_GAP_PX,
                Math.min(left, viewportWidth - PICKER_WIDTH_PX - VIEWPORT_GAP_PX)
            );

            $root.css({
                top: Math.round(top) + 'px',
                left: Math.round(left) + 'px',
                width: PICKER_WIDTH_PX + 'px'
            });
        }

        const runSearch = _.debounce(function (query) {
            const seq = ++requestSeq;
            currentQuery = query;

            searchUsers(query, FETCH_LIMIT)
                .then(function (result) {
                    if (seq !== requestSeq || !open) {
                        return;
                    }

                    users = (result && result.users) || [];
                    renderList();
                    positionNearCaret();
                })
                .catch(function () {
                    if (seq !== requestSeq || !open) {
                        return;
                    }
                    users = [];
                    renderList(true);
                    positionNearCaret();
                });
        }, SEARCH_DEBOUNCE_MS);

        function renderList(isError) {
            $list.empty();
            activeIndex = users.length ? 0 : -1;

            if (!users.length) {
                $empty
                    .text(
                        isError
                            ? __('Unable to load users')
                            : __('No matching users')
                    )
                    .prop('hidden', false);
                $list.prop('hidden', true);
                return;
            }

            $empty.prop('hidden', true).text('');
            $list.prop('hidden', false);

            users.forEach(function (user, index) {
                const displayName = (user.displayName || '').trim();
                const label = displayName
                    ? displayName + ' (@' + user.login + ')'
                    : '@' + user.login;
                const $item = $(
                    '<li class="item-comments-mention-picker__item" role="option"></li>'
                )
                    .attr('data-index', String(index))
                    .attr('id', 'mention-option-' + index)
                    .text(label);

                if (index === activeIndex) {
                    $item.addClass('item-comments-mention-picker__item--active');
                    $item.attr('aria-selected', 'true');
                }

                $list.append($item);
            });
        }

        function setActive(index) {
            if (!users.length) {
                activeIndex = -1;
                return;
            }

            activeIndex = Math.max(0, Math.min(index, users.length - 1));
            $list.children().each(function () {
                const $item = $(this);
                const itemIndex = Number($item.attr('data-index'));
                const isActive = itemIndex === activeIndex;
                $item
                    .toggleClass('item-comments-mention-picker__item--active', isActive)
                    .attr('aria-selected', isActive ? 'true' : 'false');
                if (isActive && this.scrollIntoView) {
                    this.scrollIntoView({ block: 'nearest' });
                }
            });
        }

        function selectActive() {
            if (activeIndex < 0 || !users[activeIndex]) {
                return false;
            }
            onSelect(users[activeIndex]);
            close();
            return true;
        }

        function openPicker(query) {
            open = true;
            $root.prop('hidden', false);
            $info.prop('hidden', false);
            positionNearCaret();
            runSearch(query || '');
        }

        function close() {
            open = false;
            currentQuery = null;
            users = [];
            activeIndex = -1;
            $root.prop('hidden', true);
            $list.empty();
            $empty.prop('hidden', true).text('');
        }

        $list.on('mousedown', '.item-comments-mention-picker__item', function (event) {
            // Prevent editor blur before selection is applied.
            event.preventDefault();
            const index = Number($(event.currentTarget).attr('data-index'));
            if (!Number.isNaN(index) && users[index]) {
                onSelect(users[index]);
                close();
            }
        });

        $(document).on('mousedown.commentMentionPicker', function (event) {
            if (!open) {
                return;
            }
            if ($(event.target).closest('.item-comments-mention-picker, .item-comments-rich-editor').length) {
                return;
            }
            close();
        });

        $(window).on('resize.commentMentionPicker', function () {
            positionNearCaret();
        });

        // Capture scroll from nested panels (comments list / composer), not only window.
        document.addEventListener('scroll', positionNearCaret, true);

        return {
            isOpen() {
                return open;
            },

            updateQuery(query) {
                if (!open) {
                    openPicker(query);
                    return;
                }
                positionNearCaret();
                if (query === currentQuery) {
                    return;
                }
                runSearch(query || '');
            },

            open: openPicker,
            close: close,
            reposition: positionNearCaret,

            handleKeyDown(event) {
                if (!open) {
                    return false;
                }

                if (event.key === 'Escape') {
                    event.preventDefault();
                    close();
                    return true;
                }

                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    setActive(activeIndex + 1);
                    return true;
                }

                if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    setActive(activeIndex - 1);
                    return true;
                }

                if (event.key === 'Enter' || event.key === 'Tab') {
                    if (selectActive()) {
                        event.preventDefault();
                        return true;
                    }
                }

                return false;
            },

            destroy() {
                $(document).off('.commentMentionPicker');
                $(window).off('.commentMentionPicker');
                document.removeEventListener('scroll', positionNearCaret, true);
                $list.off();
                $root.remove();
            }
        };
    }

    return {
        create: createMentionPicker,
        VISIBLE_ROWS: VISIBLE_ROWS
    };
});

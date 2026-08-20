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
define(['require', 'jquery', 'lodash', 'ckeditor', 'lib/dompurify/purify'], function (require, $, _, CKEDITOR, DOMPurify) {
    'use strict';

    let editorSeq = 0;
    const editorContentsCss = require.toUrl('taoItemsCss/comments-editor-content.css');

    const SANITIZE_OPTIONS = {
        ALLOWED_TAGS: ['strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'a', 'br'],
        ALLOWED_ATTR: ['href'],
        ALLOWED_URI_REGEXP: /^(?:(?:https?|mailto):|[^a-z]|[a-z+.-]+(?:[^a-z+.-:]|$))/i
    };

    function sanitizeHtml(value) {
        const html = typeof value === 'string' ? value : '';

        return DOMPurify.sanitize(html, SANITIZE_OPTIONS).trim();
    }

    function hasMeaningfulText(value) {
        const plainText = $('<div/>').html(sanitizeHtml(value)).text().replace(/\u00a0/g, ' ').trim();

        return plainText.length > 0;
    }

    function create(config) {
        const $host = $(config.host);
        const $toolbar = $(config.toolbar);
        const onChange = _.isFunction(config.onChange) ? config.onChange : _.noop;

        if (!$host.length || !$toolbar.length) {
            throw new TypeError('commentRichTextEditor requires host and toolbar elements');
        }

        const editorId = config.editorId || `comment-rich-editor-${Date.now()}-${++editorSeq}`;
        const placeholder = config.placeholder || '';

        $host.empty().append(
            $('<textarea/>', {
                id: editorId,
                class: 'item-comments-rich-textarea',
                'data-role': 'rich-textarea',
                placeholder: placeholder
            })
        );

        const textareaElement = $host.find('[data-role="rich-textarea"]').get(0);
        const editor = textareaElement
            ? CKEDITOR.replace(textareaElement, {
            toolbar: [],
            extraPlugins: 'basicstyles,list,indent,link',
            removePlugins: 'elementspath,magicline,maximize,resize,floatingspace',
            autoParagraph: false,
            enterMode: CKEDITOR.ENTER_BR,
            shiftEnterMode: CKEDITOR.ENTER_BR,
            height: 78,
            contentsCss: [editorContentsCss],
            bodyClass: 'item-comments-editor-body',
            allowedContent: 'strong b em i u ul ol li a[!href] br',
            disallowedContent: '*[on*]; script; style; img; svg; iframe; object; embed; table; span{*}; *[style]'
            })
            : null;

        if (!editor) {
            throw new Error('Unable to initialize CKEditor for comment input');
        }

        function getData() {
            return sanitizeHtml(editor.getData());
        }

        function setData(value) {
            editor.setData(sanitizeHtml(value || ''));
            onChange(getData());
        }

        const toolbarCommandMap = {
            bold: 'bold',
            italic: 'italic',
            underline: 'underline',
            bulletedlist: 'bulletedlist',
            numberedlist: 'numberedlist'
        };

        const nativeCommandMap = {
            bold: 'bold',
            italic: 'italic',
            underline: 'underline',
            bulletedlist: 'insertUnorderedList',
            numberedlist: 'insertOrderedList'
        };

        function syncToolbarState() {
            $toolbar.find('.item-comments-rich-tool').each(function () {
                const $button = $(this);
                const commandName = String($button.data('command') || '');

                if (!toolbarCommandMap[commandName]) {
                    return;
                }

                let isActive = false;
                const command = editor.getCommand(toolbarCommandMap[commandName]);

                if (command) {
                    isActive = command.state === CKEDITOR.TRISTATE_ON;
                }

                if (editor.document && editor.document.$ && nativeCommandMap[commandName]) {
                    try {
                        isActive = isActive || !!editor.document.$.queryCommandState(nativeCommandMap[commandName]);
                    } catch (error) {
                        // keep current isActive state from CK command API
                    }
                }

                $button
                    .toggleClass('item-comments-rich-tool--active', isActive)
                    .attr('aria-pressed', isActive ? 'true' : 'false');
            });
        }

        function executeCommand(commandName) {
            if (!commandName) {
                return;
            }

            function runNativeCommand(name) {
                if (!editor.document || !editor.document.$ || !name) {
                    return false;
                }

                try {
                    return !!editor.document.$.execCommand(name, false, null);
                } catch (error) {
                    return false;
                }
            }

            function runCkCommand(name) {
                const command = editor.getCommand(name);
                if (!command || command.state === CKEDITOR.TRISTATE_DISABLED) {
                    return false;
                }

                editor.execCommand(name);

                return true;
            }

            if (commandName === 'bulletedlist') {
                editor.focus();
                if (!runCkCommand('bulletedlist')) {
                    runNativeCommand(nativeCommandMap.bulletedlist);
                }
                onChange(getData());
                syncToolbarState();

                return;
            }

            if (commandName === 'numberedlist') {
                editor.focus();
                if (!runCkCommand('numberedlist')) {
                    runNativeCommand(nativeCommandMap.numberedlist);
                }
                onChange(getData());
                syncToolbarState();

                return;
            }

            if (commandName === 'link') {
                editor.focus();

                const selection = editor.getSelection();
                const startElement = selection && selection.getStartElement();
                const anchorElement = startElement && startElement.getAscendant('a', true);

                if (anchorElement) {
                    editor.execCommand('unlink');
                    onChange(getData());

                    return;
                }

                if (typeof editor.openDialog === 'function') {
                    editor.openDialog('link');

                    return;
                }

                editor.execCommand('link');
                syncToolbarState();

                return;
            }

            editor.focus();
            if (!runCkCommand(commandName)) {
                runNativeCommand(nativeCommandMap[commandName]);
            }
            onChange(getData());
            syncToolbarState();
        }

        $toolbar.on('mousedown.commentRichTextEditor', '.item-comments-rich-tool', function (event) {
            event.preventDefault();
        });

        $toolbar.on('click.commentRichTextEditor', '.item-comments-rich-tool', function (event) {
            event.preventDefault();
            executeCommand($(event.currentTarget).data('command'));
        });

        const notifyChange = _.throttle(function () {
            onChange(getData());
            syncToolbarState();
        }, 50);

        editor.on('instanceReady', function () {
            if (typeof config.initialValue === 'string' && config.initialValue !== '') {
                setData(config.initialValue);
            }

            notifyChange();

            editor.on('change', notifyChange);
            editor.on('key', function () {
                setTimeout(notifyChange, 0);
            });
            editor.on('selectionChange', syncToolbarState);
            editor.on('focus', syncToolbarState);

            syncToolbarState();
        });

        editor.on('paste', function () {
            setTimeout(function () {
                setData(getData());
            }, 0);
        });

        return {
            getData() {
                return getData();
            },

            setData(value) {
                setData(value);
            },

            focus() {
                editor.focus();
            },

            destroy() {
                $toolbar.off('.commentRichTextEditor');
                if (editor && editor.status !== 'destroyed') {
                    editor.destroy();
                }
                $host.empty();
            }
        };
    }

    return {
        create: create,
        sanitizeHtml: sanitizeHtml,
        hasMeaningfulText: hasMeaningfulText
    };
});

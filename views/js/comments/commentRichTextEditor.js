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
        ALLOWED_URI_REGEXP: /^(?:(?:https?|mailto):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i
    };

    /**
     * Normalize block markup to <br> and drop literal source newlines.
     * Consecutive <br> (intentional blank lines) are preserved.
     */
    function normalizeLineBreaks(html) {
        return String(html)
            .replace(/<\s*br\s*\/?\s*>/gi, '<br>')
            .replace(/<\/\s*p\s*>\s*<\s*p(?:\s[^>]*)?>/gi, '<br>')
            .replace(/<\/?\s*p(?:\s[^>]*)?>/gi, '')
            .replace(/<\/\s*div\s*>\s*<\s*div(?:\s[^>]*)?>/gi, '<br>')
            .replace(/<\/?\s*div(?:\s[^>]*)?>/gi, '')
            .replace(/[\r\n\t]+/g, '');
    }

    /**
     * Map style-based spans (browser/CKEditor defaults) to semantic tags before sanitize.
     * Otherwise DOMPurify drops span[style] and formatting is lost on getData/setData.
     */
    function semanticizeInlineStyles(html) {
        const $container = $('<div/>').html(typeof html === 'string' ? html : '');
        const $spans = $container.find('span[style]');

        // Innermost first so nested spans convert cleanly.
        for (let i = $spans.length - 1; i >= 0; i -= 1) {
            const $span = $spans.eq(i);
            const style = String($span.attr('style') || '').toLowerCase();
            const tagNames = [];

            if (/font-weight\s*:\s*(bold|[5-9]00)/.test(style)) {
                tagNames.push('strong');
            }

            if (/font-style\s*:\s*italic/.test(style)) {
                tagNames.push('em');
            }

            if (/text-decoration[^;]*underline/.test(style)) {
                tagNames.push('u');
            }

            if (!tagNames.length) {
                $span.replaceWith($span.contents());
                continue;
            }

            let $wrapped = $span.contents();

            for (let wrapperIndex = tagNames.length - 1; wrapperIndex >= 0; wrapperIndex -= 1) {
                $wrapped = $('<' + tagNames[wrapperIndex] + '/>').append($wrapped);
            }

            $span.replaceWith($wrapped);
        }

        return $container.html();
    }

    function sanitizeHtml(value) {
        const html = typeof value === 'string' ? value : '';

        return DOMPurify.sanitize(
            normalizeLineBreaks(semanticizeInlineStyles(html)),
            SANITIZE_OPTIONS
        ).trim();
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
        // Keep raw initial HTML for setData after instanceReady — do not seed via textarea
        // attribute (CKEditor may read attr vs .value inconsistently with markup).
        const initialHtml = sanitizeHtml(config.initialValue || '');

        $host.empty().append(
            $('<textarea/>', {
                id: editorId,
                class: 'item-comments-rich-textarea',
                'data-role': 'rich-textarea',
                placeholder: placeholder
            })
        );

        const textareaElement = $host.find('[data-role="rich-textarea"]').get(0);
        // Custom setup (not ckConfigurator/htmlEditor): external toolbar, ENTER_BR, semantic tags, tight allow-list.
        const editor = textareaElement
            ? CKEDITOR.replace(textareaElement, {
            toolbar: [], // driven by .item-comments-rich-toolbar via execCommand
            extraPlugins: 'basicstyles,list,indent,link',
            removePlugins: 'elementspath,magicline,maximize,resize,floatingspace',
            autoParagraph: false,
            enterMode: CKEDITOR.ENTER_BR,
            shiftEnterMode: CKEDITOR.ENTER_BR,
            height: 78,
            contentsCss: [editorContentsCss],
            bodyClass: 'item-comments-editor-body',
            // Semantic tags (ckConfigurator underline → span.txt-underline would fail sanitize).
            coreStyles_bold: { element: 'strong', overrides: 'b' },
            coreStyles_italic: { element: 'em', overrides: 'i' },
            coreStyles_underline: { element: 'u', overrides: 'span' },
            allowedContent: {
                'strong b em i u br': true,
                ul: true,
                ol: true,
                li: true,
                a: {
                    attributes: '!href'
                }
            },
            disallowedContent: '*[on*]; script; style; img; svg; iframe; object; embed; table'
            })
            : null;

        if (!editor) {
            throw new Error('Unable to initialize CKEditor for comment input');
        }

        function getData() {
            return sanitizeHtml(editor.getData());
        }

        function setData(value, callback) {
            const html = sanitizeHtml(value || '');
            const done = _.isFunction(callback) ? callback : _.noop;

            editor.setData(html, {
                callback: function () {
                    onChange(getData());
                    done();
                }
            });
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

            // Prefer CK semantic commands only for basic styles — document.execCommand
            // often emits span[style] which sanitizeHtml would strip.
            editor.focus();
            runCkCommand(commandName);
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
            // Apply initial HTML via setData after ACF/coreStyles are active.
            if (initialHtml !== '') {
                setData(initialHtml, function () {
                    notifyChange();
                    syncToolbarState();
                });
            } else {
                notifyChange();
                syncToolbarState();
            }

            editor.on('change', notifyChange);
            editor.on('key', function () {
                setTimeout(notifyChange, 0);
            });
            editor.on('selectionChange', syncToolbarState);
            editor.on('focus', syncToolbarState);
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
                if (editor.status === 'ready' || editor.status === 'loaded') {
                    editor.focus();
                } else {
                    editor.on('instanceReady', function () {
                        editor.focus();
                    });
                }
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

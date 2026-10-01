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
define(['taoItems/comments/commentRichTextEditor'], function (commentRichTextEditor) {
    'use strict';

    QUnit.module('commentRichTextEditor sanitizeHtml');

    QUnit.test('preserves combined supported span styles', function (assert) {
        const input = '<span style="font-weight:bold; font-style:italic">Hello</span>';
        const output = commentRichTextEditor.sanitizeHtml(input);

        assert.equal(output, '<strong><em>Hello</em></strong>', 'keeps both bold and italic wrappers');
    });

    QUnit.test('strips dangerous markup before semanticize .html()', function (assert) {
        const input =
            '<img src=x onerror=alert(1)><span style="font-weight:bold">Hi</span><script>alert(1)</script>';
        const output = commentRichTextEditor.sanitizeHtml(input);

        assert.equal(output, '<strong>Hi</strong>', 'keeps only semantic safe content');
        assert.strictEqual(output.indexOf('script'), -1, 'script markup removed');
        assert.strictEqual(output.indexOf('img'), -1, 'img markup removed');
        assert.strictEqual(output.indexOf('onerror'), -1, 'event handler removed');
    });

    QUnit.module('commentRichTextEditor buildMentionHtml');

    QUnit.test('uses non-empty displayName in visible label', function (assert) {
        const html = commentRichTextEditor.buildMentionHtml({
            id: 'user-1',
            login: 'jdoe',
            displayName: 'Jane Doe'
        });

        assert.ok(html.indexOf('@Jane Doe') !== -1, 'visible label uses displayName');
        assert.ok(html.indexOf('data-user-login="jdoe"') !== -1, 'login stored in data attribute');
    });

    QUnit.test('falls back to login when displayName is whitespace only', function (assert) {
        const html = commentRichTextEditor.buildMentionHtml({
            id: 'user-2',
            login: 'jdoe',
            displayName: '   '
        });

        assert.ok(html.indexOf('@jdoe') !== -1, 'visible label falls back to login');
        assert.strictEqual(html.indexOf('@   '), -1, 'whitespace displayName not shown');
    });

    QUnit.test('escapes HTML in visible label and attributes', function (assert) {
        const html = commentRichTextEditor.buildMentionHtml({
            id: 'u<script>',
            login: 'a<b>',
            displayName: 'Evil <img>'
        });

        assert.ok(html.indexOf('@Evil &lt;img&gt;') !== -1, 'displayName HTML escaped in label');
        assert.ok(html.indexOf('data-user-id="u&lt;script&gt;"') !== -1, 'id escaped in attribute');
        assert.ok(html.indexOf('data-user-login="a&lt;b&gt;"') !== -1, 'login escaped in attribute');
        assert.strictEqual(html.indexOf('<img'), -1, 'raw img tag not present');
    });
});

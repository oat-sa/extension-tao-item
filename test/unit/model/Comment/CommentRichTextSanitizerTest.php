<?php

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

declare(strict_types=1);

namespace oat\taoItems\test\unit\model\Comment;

use oat\taoItems\model\Comment\CommentRichTextSanitizer;
use PHPUnit\Framework\TestCase;

class CommentRichTextSanitizerTest extends TestCase
{
    /**
     * @dataProvider sanitizeProvider
     */
    public function testSanitize(string $inputBody, string $expectedBody): void
    {
        $this->assertSame($expectedBody, CommentRichTextSanitizer::sanitize($inputBody));
    }

    /**
     * @dataProvider meaningfulTextProvider
     */
    public function testHasMeaningfulText(string $inputBody, bool $expected): void
    {
        $this->assertSame($expected, CommentRichTextSanitizer::hasMeaningfulText($inputBody));
    }

    public function sanitizeProvider(): array
    {
        return [
            'keeps supported rich tags' => [
                '<b>Hello</b> <i>World</i><ul><li>One</li></ul>',
                '<b>Hello</b> <i>World</i><ul><li>One</li></ul>',
            ],
            'drops script element and content' => [
                '<script>alert(1)</script>Hello',
                'Hello',
            ],
            'drops image with event handler' => [
                '<img src=x onerror=alert(1)>Hello',
                'Hello',
            ],
            'drops javascript href' => [
                '<a href="javascript:alert(1)">Click</a>',
                '<a>Click</a>',
            ],
            'keeps safe href and removes unsafe attributes' => [
                '<a href="https://safe.test" onclick="alert(1)">Link</a>',
                '<a href="https://safe.test">Link</a>',
            ],
            'unwraps unsupported tags' => [
                '<div><strong>Safe</strong></div>',
                '<strong>Safe</strong>',
            ],
            'converts adjacent paragraphs to single breaks' => [
                '<p><strong>Bold</strong></p><p><em>Italic</em></p><p><u>Underline</u></p>',
                '<strong>Bold</strong><br><em>Italic</em><br><u>Underline</u>',
            ],
            'collapses br plus source newlines' => [
                "<strong>Bold</strong><br />\n<em>Italic</em><br />\n<u>Underline</u>",
                '<strong>Bold</strong><br><em>Italic</em><br><u>Underline</u>',
            ],
            'preserves duplicate breaks' => [
                'A<br><br><br>B',
                'A<br><br><br>B',
            ],
        ];
    }

    public function meaningfulTextProvider(): array
    {
        return [
            'empty string' => ['', false],
            'whitespace' => ['   ', false],
            'only tags and spaces' => ['<b> </b><i></i>', false],
            'line break only' => ['<br>', false],
            'text in formatting' => ['<u>Hello</u>', true],
            'link text without href' => ['<a>Text</a>', true],
        ];
    }
}

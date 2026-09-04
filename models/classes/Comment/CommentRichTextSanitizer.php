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

namespace oat\taoItems\model\Comment;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Comment HTML sanitizer via HTMLPurifier (same stack as Display::sanitizeXssHtml / Htmlarea).
 * Allow-list is stricter and must stay aligned with commentRichTextEditor.js (FE).
 */
final class CommentRichTextSanitizer
{
    private HTMLPurifier $htmlPurifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $config->set('HTML.DefinitionID', 'tao-items-comment-richtext-mentions-v1');
        $config->set('HTML.DefinitionRev', 1);
        $config->set(
            'HTML.Allowed',
            'strong,b,em,i,u,ul,ol,li,br,a[href],span[class|data-user-id|data-user-login|contenteditable]'
        );
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);

        if ($def = $config->maybeGetRawHTMLDefinition()) {
            $def->addAttribute('span', 'data-user-id', 'Text');
            $def->addAttribute('span', 'data-user-login', 'Text');
            $def->addAttribute('span', 'contenteditable', 'Enum#false,true');
        }

        $this->htmlPurifier = new HTMLPurifier($config);
    }

    public function sanitize(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return $this->unwrapNonMentionSpans($this->htmlPurifier->purify($value));
    }

    /**
     * Span is allowed only for recognised mentions. Unwrap any other span leftovers
     * (e.g. style spans with attributes stripped by HTMLPurifier).
     */
    private function unwrapNonMentionSpans(string $html): string
    {
        if ($html === '' || stripos($html, '<span') === false) {
            return $html;
        }

        $previous = null;
        $current = $html;

        // Repeat until stable: nested spans may require multiple passes.
        while ($previous !== $current) {
            $previous = $current;
            $current = preg_replace_callback(
                '/<span\b([^>]*)>(.*?)<\/span>/is',
                static function (array $matches): string {
                    $attrs = $matches[1];
                    $inner = $matches[2];
                    if (
                        preg_match('/\bclass\s*=\s*(["\'])([^"\']*\bcomment-mention\b[^"\']*)\1/i', $attrs)
                        || preg_match('/\bclass\s*=\s*comment-mention\b/i', $attrs)
                    ) {
                        return '<span' . $attrs . '>' . $inner . '</span>';
                    }

                    return $inner;
                },
                $current
            );
            if (!is_string($current)) {
                return $previous;
            }
        }

        return $current;
    }

    /**
     * Whether the value has non-empty plain text after stripping HTML.
     *
     * Used by create/update to reject markup-only bodies (e.g. <br>, empty tags).
     * NBSP (U+00A0 / UTF-8 \xc2\xa0) is normalized to a regular space because trim()
     * does not treat it as whitespace.
     *
     * @param string $value Sanitized or raw HTML comment body
     * @return bool
     */
    public function hasMeaningfulText(string $value): bool
    {
        $text = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xc2\xa0", ' ', $text);

        return trim($text) !== '';
    }
}

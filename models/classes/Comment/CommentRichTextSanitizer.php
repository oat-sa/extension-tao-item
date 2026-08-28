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

use DOMDocument;
use DOMElement;
use DOMNode;

final class CommentRichTextSanitizer
{
    private const BLOCKED_TAGS = [
        'script',
        'style',
        'svg',
        'iframe',
        'object',
        'embed',
        'img',
        'video',
        'audio',
        'source',
        'meta',
        'link',
        'base',
        'math',
    ];

    private const ALLOWED_TAGS = [
        'strong',
        'b',
        'em',
        'i',
        'u',
        'ul',
        'ol',
        'li',
        'a',
        'br',
    ];

    public static function sanitize(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $value = self::removeBlockedElements($value);
        $value = self::normalizeBlocksToBreaks($value);
        $value = strip_tags($value, '<' . implode('><', self::ALLOWED_TAGS) . '>');

        if ($value === '') {
            return '';
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);

        $wrapper = '<div>' . $value . '</div>';
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $wrapper, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $dom->getElementsByTagName('div')->item(0);
        if (!$root instanceof DOMElement) {
            return trim($value);
        }

        self::sanitizeTree($root);

        return trim(self::innerHtml($root));
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
    public static function hasMeaningfulText(string $value): bool
    {
        $text = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xc2\xa0", ' ', $text);

        return trim($text) !== '';
    }

    private static function removeBlockedElements(string $value): string
    {
        $blocked = implode('|', self::BLOCKED_TAGS);

        $value = (string) preg_replace(
            '#<\s*(' . $blocked . ')[^>]*>.*?<\s*/\s*\1\s*>#is',
            '',
            $value
        );

        return (string) preg_replace(
            '#<\s*(' . $blocked . ')[^>]*?/?>#is',
            '',
            $value
        );
    }

    /**
     * Map paragraph/div boundaries to <br>.
     * Remaining CR/LF/tabs become a single space so plain text like "Hello\nWorld"
     * keeps word separation; spaces next to <br> are trimmed.
     * Consecutive <br> (intentional blank lines) are preserved.
     */
    private static function normalizeBlocksToBreaks(string $value): string
    {
        $value = (string) preg_replace('#<\s*br\s*/?\s*>#i', '<br>', $value);
        $value = (string) preg_replace('#</\s*p\s*>\s*<\s*p(?:\s[^>]*)?>#i', '<br>', $value);
        $value = (string) preg_replace('#</?\s*p(?:\s[^>]*)?>#i', '', $value);
        $value = (string) preg_replace('#</\s*div\s*>\s*<\s*div(?:\s[^>]*)?>#i', '<br>', $value);
        $value = (string) preg_replace('#</?\s*div(?:\s[^>]*)?>#i', '', $value);
        $value = (string) preg_replace('#[\r\n\t]+#', ' ', $value);
        $value = (string) preg_replace('#(<br>)\s+#', '$1', $value);

        return (string) preg_replace('#\s+(<br>)#', '$1', $value);
    }

    private static function sanitizeTree(DOMElement $root): void
    {
        /** @var DOMNode $node */
        foreach (iterator_to_array($root->childNodes) as $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }

            $tagName = strtolower($node->tagName);

            if (!in_array($tagName, self::ALLOWED_TAGS, true)) {
                self::unwrap($node);
                continue;
            }

            self::sanitizeAttributes($node, $tagName);
            self::sanitizeTree($node);
        }
    }

    private static function sanitizeAttributes(DOMElement $node, string $tagName): void
    {
        $href = $tagName === 'a' ? $node->getAttribute('href') : '';

        foreach (iterator_to_array($node->attributes) as $attribute) {
            $node->removeAttribute($attribute->name);
        }

        if ($tagName !== 'a') {
            return;
        }

        if ($href === '') {
            return;
        }

        $safeHref = self::sanitizeHref($href);
        if ($safeHref !== null) {
            $node->setAttribute('href', $safeHref);
        }
    }

    private static function sanitizeHref(string $href): ?string
    {
        $trimmedHref = trim($href);
        if ($trimmedHref === '') {
            return null;
        }

        $decodedHref = html_entity_decode($trimmedHref, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalizedHref = strtolower((string) preg_replace('/[\x00-\x20]+/u', '', $decodedHref));

        if (str_starts_with($normalizedHref, 'javascript:') || str_starts_with($normalizedHref, 'data:')) {
            return null;
        }

        $scheme = parse_url($trimmedHref, PHP_URL_SCHEME);
        if ($scheme === null || $scheme === false) {
            return $trimmedHref;
        }

        $scheme = strtolower($scheme);

        return in_array($scheme, ['http', 'https', 'mailto'], true)
            ? $trimmedHref
            : null;
    }

    private static function unwrap(DOMElement $element): void
    {
        $parent = $element->parentNode;
        if (!$parent instanceof DOMNode) {
            return;
        }

        while ($element->firstChild !== null) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    private static function innerHtml(DOMElement $element): string
    {
        $html = '';

        foreach ($element->childNodes as $childNode) {
            $html .= $element->ownerDocument->saveHTML($childNode);
        }

        return $html;
    }
}

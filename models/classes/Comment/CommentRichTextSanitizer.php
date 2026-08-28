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
 * Comment-body XSS sanitizer — thin HTMLPurifier wrapper (same stack as
 * {@see \tao_helpers_Display::sanitizeXssHtml} / Htmlarea), with a stricter allow-list.
 */
final class CommentRichTextSanitizer
{
    public static function sanitize(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $config->set('HTML.Allowed', 'strong,b,em,i,u,ul,ol,li,br,a[href]');
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);

        return (new HTMLPurifier($config))->purify($value);
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
}

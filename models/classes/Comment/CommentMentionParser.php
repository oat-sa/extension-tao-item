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

/**
 * Extracts recognised @mention chips from sanitized comment HTML.
 */
final class CommentMentionParser
{
    /**
     * @return list<array{id: string, login: string}>
     */
    public function parse(string $html): array
    {
        if ($html === '' || stripos($html, 'comment-mention') === false) {
            return [];
        }

        if (!preg_match_all(
            '/<span\b[^>]*\bclass\s*=\s*(["\'])[^"\']*\bcomment-mention\b[^"\']*\1[^>]*>/i',
            $html,
            $spanMatches,
            PREG_OFFSET_CAPTURE
        )) {
            return [];
        }

        $mentionsById = [];

        foreach ($spanMatches[0] as $spanMatch) {
            $tag = $spanMatch[0];
            $id = $this->attribute($tag, 'data-user-id');
            $login = $this->attribute($tag, 'data-user-login');

            if ($id === null || $login === null) {
                continue;
            }

            $mentionsById[$id] = [
                'id' => $id,
                'login' => $login,
            ];
        }

        return array_values($mentionsById);
    }

    /**
     * @param list<array{id: string, login: string}> $mentions
     * @return list<string>
     */
    public function ids(array $mentions): array
    {
        return array_values(array_map(
            static fn (array $mention): string => $mention['id'],
            $mentions
        ));
    }

    private function attribute(string $tag, string $name): ?string
    {
        $pattern = sprintf('/\b%s\s*=\s*(["\'])(.*?)\1/i', preg_quote($name, '/'));
        if (!preg_match($pattern, $tag, $matches)) {
            return null;
        }

        $value = html_entity_decode(trim($matches[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $value !== '' ? $value : null;
    }
}

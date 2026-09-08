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

use oat\taoItems\model\Comment\CommentMentionParser;
use PHPUnit\Framework\TestCase;

class CommentMentionParserTest extends TestCase
{
    public function testParseExtractsUniqueMentions(): void
    {
        $html = 'Hello '
            . '<span class="comment-mention" data-user-id="http://u#1" data-user-login="alice" contenteditable="false">@alice</span> '
            . 'and '
            . '<span class="comment-mention" data-user-id="http://u#2" data-user-login="bob">@bob</span> '
            . 'again '
            . '<span class="comment-mention" data-user-id="http://u#1" data-user-login="alice">@alice</span>';

        $mentions = (new CommentMentionParser())->parse($html);

        $this->assertCount(2, $mentions);
        $this->assertSame(
            [
                ['id' => 'http://u#1', 'login' => 'alice'],
                ['id' => 'http://u#2', 'login' => 'bob'],
            ],
            $mentions
        );
    }

    public function testParseIgnoresOrdinaryAtText(): void
    {
        $this->assertSame([], (new CommentMentionParser())->parse('Ping @alice please'));
    }
}

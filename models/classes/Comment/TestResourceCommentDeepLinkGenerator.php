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

use oat\tao\model\TaskOrchestrator\CommentMentionDeepLinkBuilder;
use oat\tao\model\TaoOntology;

/**
 * Deep link for test comments → Tests structure / manage_tests.
 */
final class TestResourceCommentDeepLinkGenerator implements ResourceCommentDeepLinkGeneratorInterface
{
    private CommentMentionDeepLinkBuilder $deepLinkBuilder;

    public function __construct(CommentMentionDeepLinkBuilder $deepLinkBuilder)
    {
        $this->deepLinkBuilder = $deepLinkBuilder;
    }

    public function resourceType(): string
    {
        return ResourceCommentType::TEST;
    }

    public function build(string $resourceUri): string
    {
        return $this->deepLinkBuilder->build(TaoOntology::CLASS_URI_TEST, $resourceUri);
    }
}

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

use InvalidArgumentException;
use oat\tao\model\TaskOrchestrator\CommentMentionDeepLinkBuilder;

/**
 * Registry of commentable resourceType → ontology root class URI for Backoffice deep links.
 *
 * Owning extensions register their types from their ServiceProviders, e.g.:
 * - taoItems → item
 * - taoTests → test
 * - taoMediaManager → asset
 */
final class ResourceCommentDeepLinkGenerator
{
    private CommentMentionDeepLinkBuilder $deepLinkBuilder;

    /** @var array<string, string> resourceType => rootClassUri */
    private array $rootClassUrisByType = [];

    public function __construct(CommentMentionDeepLinkBuilder $deepLinkBuilder)
    {
        $this->deepLinkBuilder = $deepLinkBuilder;
    }

    public function register(string $resourceType, string $rootClassUri): void
    {
        $resourceType = ResourceCommentType::assertValid($resourceType);
        $rootClassUri = trim($rootClassUri);
        if ($rootClassUri === '') {
            throw new InvalidArgumentException(sprintf(
                'Root class URI is required to register comment deep link for "%s".',
                $resourceType
            ));
        }

        $this->rootClassUrisByType[$resourceType] = $rootClassUri;
    }

    public function build(string $resourceType, string $resourceUri): string
    {
        $resourceType = ResourceCommentType::assertValid($resourceType);

        if (!isset($this->rootClassUrisByType[$resourceType])) {
            throw new InvalidArgumentException(sprintf(
                'No comment deep-link root class registered for resourceType "%s".',
                $resourceType
            ));
        }

        return $this->deepLinkBuilder->build(
            $this->rootClassUrisByType[$resourceType],
            $resourceUri
        );
    }
}

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

/**
 * Dispatches deep-link generation to the generator registered for a resourceType.
 */
final class ResourceCommentDeepLinkGenerator
{
    /** @var array<string, ResourceCommentDeepLinkGeneratorInterface> */
    private array $generatorsByType = [];

    /**
     * @param iterable<ResourceCommentDeepLinkGeneratorInterface> $generators
     */
    public function __construct(iterable $generators)
    {
        foreach ($generators as $generator) {
            $type = ResourceCommentType::assertValid($generator->resourceType());
            $this->generatorsByType[$type] = $generator;
        }
    }

    public function build(string $resourceType, string $resourceUri): string
    {
        $resourceType = ResourceCommentType::assertValid($resourceType);

        if (!isset($this->generatorsByType[$resourceType])) {
            throw new InvalidArgumentException(sprintf(
                'No comment deep-link generator registered for resourceType "%s".',
                $resourceType
            ));
        }

        return $this->generatorsByType[$resourceType]->build($resourceUri);
    }
}

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
use oat\tao\model\TaoOntology;

/**
 * Supported authoring comment target types (wire values for REST / persistence).
 */
final class ResourceCommentType
{
    public const ITEM = 'item';
    public const TEST = 'test';
    public const ASSET = 'asset';

    /**
     * Media root class URI (taoMediaManager). Kept as literal to avoid a hard
     * dependency from taoItems on the media extension.
     */
    private const CLASS_URI_ASSET = 'http://www.tao.lu/Ontologies/TAOMedia.rdf#Media';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::ITEM,
            self::TEST,
            self::ASSET,
        ];
    }

    public static function assertValid(string $resourceType): string
    {
        $resourceType = trim($resourceType);
        if (!in_array($resourceType, self::all(), true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'resourceType must be one of: %s',
                    implode(', ', self::all())
                )
            );
        }

        return $resourceType;
    }

    public static function ontologyClassUri(string $resourceType): string
    {
        $resourceType = self::assertValid($resourceType);

        if ($resourceType === self::ITEM) {
            return TaoOntology::CLASS_URI_ITEM;
        }

        if ($resourceType === self::TEST) {
            return TaoOntology::CLASS_URI_TEST;
        }

        return self::CLASS_URI_ASSET;
    }
}

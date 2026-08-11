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
 * Supported authoring comment target types (RDF class URIs).
 *
 * Wire / persisted resourceType values are these class URIs — the same pattern
 * as resourceType elsewhere in TAO (e.g. Items action responses).
 */
final class ResourceCommentType
{
    public const ITEM = TaoOntology::CLASS_URI_ITEM;
    public const TEST = TaoOntology::CLASS_URI_TEST;

    /**
     * Same value as \oat\taoMediaManager\model\TaoMediaOntology::CLASS_URI_MEDIA_ROOT.
     * Literal kept here so taoItems does not hard-depend on taoMediaManager.
     */
    public const ASSET = 'http://www.tao.lu/Ontologies/TAOMedia.rdf#Media';

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
}

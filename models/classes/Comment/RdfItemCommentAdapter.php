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

use core_kernel_classes_Resource;
use oat\generis\model\data\Ontology;

class RdfItemCommentAdapter implements ItemCommentPersistenceInterface
{
    private Ontology $ontology;

    public function __construct(Ontology $ontology)
    {
        $this->ontology = $ontology;
    }

    public function create(ItemComment $comment): ItemComment
    {
        $resource = $this->ontology->getClass(ItemCommentOntology::CLASS_URI)->createInstanceWithProperties([
            ItemCommentOntology::PROPERTY_RESOURCE_URI => $comment->getResourceUri(),
            ItemCommentOntology::PROPERTY_RESOURCE_TYPE => $comment->getResourceType(),
            ItemCommentOntology::PROPERTY_AUTHOR_ID => $comment->getAuthorId(),
            ItemCommentOntology::PROPERTY_AUTHOR_LABEL => $comment->getAuthorLabel(),
            ItemCommentOntology::PROPERTY_BODY => $comment->getBody(),
            ItemCommentOntology::PROPERTY_CREATED_AT => $comment->getCreatedAt(),
            ItemCommentOntology::PROPERTY_EDITED => $this->boolToLiteral($comment->isEdited()),
            ItemCommentOntology::PROPERTY_RESOLVED => $this->boolToLiteral($comment->isResolved()),
        ]);

        return new ItemComment(
            $resource->getUri(),
            $comment->getResourceUri(),
            $comment->getResourceType(),
            $comment->getAuthorId(),
            $comment->getAuthorLabel(),
            $comment->getBody(),
            $comment->getCreatedAt(),
            $comment->isEdited(),
            $comment->isResolved()
        );
    }

    public function findByResource(string $resourceUri, string $resourceType): array
    {
        $resourceType = ResourceCommentType::assertValid($resourceType);

        $resources = $this->ontology->getClass(ItemCommentOntology::CLASS_URI)->searchInstances(
            [
                ItemCommentOntology::PROPERTY_RESOURCE_URI => $resourceUri,
                ItemCommentOntology::PROPERTY_RESOURCE_TYPE => $resourceType,
            ],
            [
                'recursive' => false,
                'like' => false,
            ]
        );

        $comments = [];
        foreach ($resources as $resource) {
            $comments[] = $this->mapResource($resource);
        }

        usort(
            $comments,
            static function (ItemComment $left, ItemComment $right): int {
                return strcmp($left->getCreatedAt(), $right->getCreatedAt());
            }
        );

        return $comments;
    }

    private function mapResource(core_kernel_classes_Resource $resource): ItemComment
    {
        return new ItemComment(
            $resource->getUri(),
            $this->propertyValueToString(
                $resource->getOnePropertyValue(
                    $this->ontology->getProperty(ItemCommentOntology::PROPERTY_RESOURCE_URI)
                )
            ),
            $this->propertyValueToString(
                $resource->getOnePropertyValue(
                    $this->ontology->getProperty(ItemCommentOntology::PROPERTY_RESOURCE_TYPE)
                )
            ),
            $this->propertyValueToString(
                $resource->getOnePropertyValue(
                    $this->ontology->getProperty(ItemCommentOntology::PROPERTY_AUTHOR_ID)
                )
            ),
            $this->propertyValueToString(
                $resource->getOnePropertyValue(
                    $this->ontology->getProperty(ItemCommentOntology::PROPERTY_AUTHOR_LABEL)
                ),
                false
            ),
            $this->propertyValueToString(
                $resource->getOnePropertyValue(
                    $this->ontology->getProperty(ItemCommentOntology::PROPERTY_BODY)
                ),
                false
            ),
            $this->propertyValueToString(
                $resource->getOnePropertyValue(
                    $this->ontology->getProperty(ItemCommentOntology::PROPERTY_CREATED_AT)
                ),
                false
            ),
            $this->literalToBool(
                $resource->getOnePropertyValue(
                    $this->ontology->getProperty(ItemCommentOntology::PROPERTY_EDITED)
                )
            ),
            $this->literalToBool(
                $resource->getOnePropertyValue(
                    $this->ontology->getProperty(ItemCommentOntology::PROPERTY_RESOLVED)
                )
            )
        );
    }

    private function boolToLiteral(bool $value): string
    {
        return $value ? '1' : '0';
    }

    /**
     * Generis Resource::__toString() is "uri\\nlabel"; for URI properties prefer getUri().
     *
     * @param mixed $value
     */
    private function propertyValueToString($value, bool $preferResourceUri = true): string
    {
        if ($value === null) {
            return '';
        }

        if ($preferResourceUri && $value instanceof core_kernel_classes_Resource) {
            return $value->getUri();
        }

        return trim((string) $value);
    }

    /**
     * @param mixed $value
     */
    private function literalToBool($value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if ($value instanceof core_kernel_classes_Resource) {
            $value = $value->getUri();
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes'], true);
    }
}

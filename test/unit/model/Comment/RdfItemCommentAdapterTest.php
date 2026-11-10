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

use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use oat\generis\model\data\Ontology;
use oat\taoItems\model\Comment\ItemCommentOntology;
use oat\taoItems\model\Comment\RdfItemCommentAdapter;
use oat\taoItems\model\Comment\ResourceCommentType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RdfItemCommentAdapterTest extends TestCase
{
    private const COMMENT_URI = 'http://example.test/comment#1';

    public function testFindByIdMapsResourceAndLiteralValues(): void
    {
        $resourceUriValue = $this->createMock(core_kernel_classes_Resource::class);
        $resourceUriValue->method('getUri')->willReturn('http://example.test/item#resource');

        $sut = $this->createSut([
            ItemCommentOntology::PROPERTY_RESOURCE_URI => $resourceUriValue,
            ItemCommentOntology::PROPERTY_AUTHOR_ID => "\n  admin-1  ",
            ItemCommentOntology::PROPERTY_AUTHOR_LABEL => '  Alice Admin  ',
            ItemCommentOntology::PROPERTY_EDITED => ' YES ',
            ItemCommentOntology::PROPERTY_RESOLVED => ' false ',
        ]);

        $comment = $sut->findById(self::COMMENT_URI);

        $this->assertNotNull($comment);
        $this->assertSame('http://example.test/item#resource', $comment->getResourceUri());
        $this->assertSame('admin-1', $comment->getAuthorId());
        $this->assertSame('Alice Admin', $comment->getAuthorLabel());
        $this->assertTrue($comment->isEdited());
        $this->assertFalse($comment->isResolved());
    }

    public function testFindByIdMapsNullPropertyValuesToEmptyStrings(): void
    {
        $sut = $this->createSut([
            ItemCommentOntology::PROPERTY_AUTHOR_LABEL => null,
            ItemCommentOntology::PROPERTY_BODY => null,
            ItemCommentOntology::PROPERTY_CREATED_AT => null,
        ]);

        $comment = $sut->findById(self::COMMENT_URI);

        $this->assertNotNull($comment);
        $this->assertSame('', $comment->getAuthorLabel());
        $this->assertSame('', $comment->getBody());
        $this->assertSame('', $comment->getCreatedAt());
    }

    public function testFindByIdMapsBooleanResourcesUsingResourceUri(): void
    {
        $editedValue = $this->createMock(core_kernel_classes_Resource::class);
        $editedValue->method('getUri')->willReturn('true');

        $resolvedValue = $this->createMock(core_kernel_classes_Resource::class);
        $resolvedValue->method('getUri')->willReturn('no');

        $sut = $this->createSut([
            ItemCommentOntology::PROPERTY_EDITED => $editedValue,
            ItemCommentOntology::PROPERTY_RESOLVED => $resolvedValue,
        ]);

        $comment = $sut->findById(self::COMMENT_URI);

        $this->assertNotNull($comment);
        $this->assertTrue($comment->isEdited());
        $this->assertFalse($comment->isResolved());
    }

    /**
     * @param array<string, mixed> $propertyValueMap
     */
    private function createSut(array $propertyValueMap): RdfItemCommentAdapter
    {
        $ontology = $this->createMock(Ontology::class);

        $propertiesByUri = [];
        $valuesByPropertyId = [];

        foreach ($this->defaultPropertyValueMap($propertyValueMap) as $propertyUri => $value) {
            $property = $this->createMock(core_kernel_classes_Property::class);
            $propertiesByUri[$propertyUri] = $property;
            $valuesByPropertyId[spl_object_id($property)] = $value;
        }

        $resourceClass = $this->createMock(core_kernel_classes_Resource::class);
        $resourceClass->method('getUri')->willReturn(ItemCommentOntology::CLASS_URI);

        $commentResource = $this->createMock(core_kernel_classes_Resource::class);
        $commentResource->method('exists')->willReturn(true);
        $commentResource->method('getUri')->willReturn(self::COMMENT_URI);
        $commentResource->method('getTypes')->willReturn([$resourceClass]);
        $commentResource
            ->method('getOnePropertyValue')
            ->willReturnCallback(static function (core_kernel_classes_Property $property) use ($valuesByPropertyId) {
                return $valuesByPropertyId[spl_object_id($property)] ?? null;
            });

        $ontology
            ->method('getProperty')
            ->willReturnCallback(
                static function (string $propertyUri) use ($propertiesByUri): core_kernel_classes_Property {
                    return $propertiesByUri[$propertyUri];
                }
            );

        $ontology
            ->expects($this->once())
            ->method('getResource')
            ->with(self::COMMENT_URI)
            ->willReturn($commentResource);

        return new RdfItemCommentAdapter($ontology);
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function defaultPropertyValueMap(array $overrides): array
    {
        return array_replace(
            [
                ItemCommentOntology::PROPERTY_RESOURCE_URI => 'http://example.test/item#1',
                ItemCommentOntology::PROPERTY_RESOURCE_TYPE => ResourceCommentType::ITEM,
                ItemCommentOntology::PROPERTY_AUTHOR_ID => 'author-1',
                ItemCommentOntology::PROPERTY_AUTHOR_LABEL => 'Author',
                ItemCommentOntology::PROPERTY_BODY => 'Body',
                ItemCommentOntology::PROPERTY_CREATED_AT => '2026-08-14T08:00:00+00:00',
                ItemCommentOntology::PROPERTY_EDITED => '0',
                ItemCommentOntology::PROPERTY_RESOLVED => '0',
            ],
            $overrides
        );
    }
}

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

use InvalidArgumentException;
use oat\generis\test\TestCase;
use oat\tao\model\TaoOntology;
use oat\taoItems\model\Comment\ResourceCommentType;

class ResourceCommentTypeTest extends TestCase
{
    public function testAssertValidAcceptsShortWireValues(): void
    {
        $this->assertSame(ResourceCommentType::ITEM, ResourceCommentType::assertValid('item'));
        $this->assertSame(ResourceCommentType::TEST, ResourceCommentType::assertValid(' test '));
        $this->assertSame(ResourceCommentType::ASSET, ResourceCommentType::assertValid('asset'));
    }

    public function testAssertValidRejectsClassUris(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('resourceType must be one of');

        ResourceCommentType::assertValid(TaoOntology::CLASS_URI_ITEM);
    }

    public function testClassUriMapsShortTypes(): void
    {
        $this->assertSame(TaoOntology::CLASS_URI_ITEM, ResourceCommentType::classUri(ResourceCommentType::ITEM));
        $this->assertSame(TaoOntology::CLASS_URI_TEST, ResourceCommentType::classUri(ResourceCommentType::TEST));
        $this->assertSame(
            'http://www.tao.lu/Ontologies/TAOMedia.rdf#Media',
            ResourceCommentType::classUri(ResourceCommentType::ASSET)
        );
    }
}

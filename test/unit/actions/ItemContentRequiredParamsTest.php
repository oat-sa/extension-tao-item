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
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA.
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\test\unit\actions;

use common_exception_BadRequest as BadRequestException;
use oat\generis\test\TestCase;
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\MediaBrowser;
use oat\taoItems\model\media\AssetSearchQuery;
use ReflectionMethod;

class ItemContentRequiredParamsTest extends TestCase
{
    public function testMissingOrBlankQueryParamTreatsZeroAsPresent(): void
    {
        $this->assertFalse($this->invokeIsMissingOrBlankQueryParam(['path' => '0'], 'path'));
        $this->assertFalse($this->invokeIsMissingOrBlankQueryParam(['path' => 0], 'path'));
    }

    public function testMissingOrBlankQueryParamRejectsMissingBlankAndWhitespace(): void
    {
        $this->assertTrue($this->invokeIsMissingOrBlankQueryParam([], 'uri'));
        $this->assertTrue($this->invokeIsMissingOrBlankQueryParam(['uri' => ''], 'uri'));
        $this->assertTrue($this->invokeIsMissingOrBlankQueryParam(['uri' => null], 'uri'));
        $this->assertTrue($this->invokeIsMissingOrBlankQueryParam(['uri' => '   '], 'uri'));
        $this->assertTrue($this->invokeIsMissingOrBlankQueryParam(['uri' => ['bad']], 'uri'));
    }

    public function testAssetSearchQueryNormalizesMetadataCriteria(): void
    {
        $propertyUri = 'http://www.tao.lu/Ontologies/TAO.rdf#Keywords';
        $mediaSource = $this->createMock(MediaBrowser::class);
        $query = new AssetSearchQuery(new MediaAsset($mediaSource, '/'), 'item', 'en-US');
        $query->setMetadataCriteria([
            $propertyUri => 'science',
            'http://example.com/empty' => '',
            123 => 'ignored',
            'http://example.com/arr' => ['Diagram', 'other'],
        ]);

        $this->assertTrue($query->hasMetadataCriteria());
        $this->assertSame([
            $propertyUri => 'science',
            'http://example.com/arr' => 'Diagram',
        ], $query->getMetadataCriteria());
    }

    public function testAssertOptionalScalarQueryParamsRejectsArrays(): void
    {
        $this->expectException(BadRequestException::class);
        $this->invokeAssertOptionalScalarQueryParams(['sortBy' => ['label']], 'sortBy');
    }

    public function testAssertOptionalScalarQueryParamsAllowsScalars(): void
    {
        $this->invokeAssertOptionalScalarQueryParams(
            ['sortBy' => 'label', 'page' => 1, 'query' => 'cat'],
            'sortBy',
            'page',
            'query'
        );
        $this->addToAssertionCount(1);
    }

    private function invokeIsMissingOrBlankQueryParam(array $params, string $key): bool
    {
        $controller = new \taoItems_actions_ItemContent();
        $method = new ReflectionMethod($controller, 'isMissingOrBlankQueryParam');
        $method->setAccessible(true);

        return (bool)$method->invoke($controller, $params, $key);
    }

    private function invokeAssertOptionalScalarQueryParams(array $params, string ...$keys): void
    {
        $controller = new \taoItems_actions_ItemContent();
        $method = new ReflectionMethod($controller, 'assertOptionalScalarQueryParams');
        $method->setAccessible(true);
        $method->invoke($controller, $params, ...$keys);
    }
}

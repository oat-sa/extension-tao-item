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

use oat\generis\test\TestCase;
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

    public function testBuildMetadataCriteriaParsesPropertyValuePairs(): void
    {
        $propertyUri = 'http://www.tao.lu/Ontologies/TAO.rdf#Keywords';
        $criteria = $this->invokeBuildMetadataCriteria([
            'metadata' => [
                '  ' . $propertyUri . '  ' => 'science',
                'http://example.com/empty' => '   ',
                123 => 'ignored',
            ],
        ]);

        $this->assertSame([$propertyUri => 'science'], $criteria);
    }

    private function invokeIsMissingOrBlankQueryParam(array $params, string $key): bool
    {
        $controller = new \taoItems_actions_ItemContent();
        $method = new ReflectionMethod($controller, 'isMissingOrBlankQueryParam');
        $method->setAccessible(true);

        return (bool)$method->invoke($controller, $params, $key);
    }

    /**
     * @return array<string, string>
     */
    private function invokeBuildMetadataCriteria(array $params): array
    {
        $controller = new \taoItems_actions_ItemContent();
        $method = new ReflectionMethod($controller, 'buildMetadataCriteria');
        $method->setAccessible(true);

        return $method->invoke($controller, $params);
    }
}

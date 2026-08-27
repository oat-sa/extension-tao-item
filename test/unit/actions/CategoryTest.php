<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; version 2.
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

namespace oat\taoItems\test\unit\actions;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CategoryTest extends TestCase
{
    /**
     * @dataProvider itemUriProvider
     */
    public function testIsValidItemUri(mixed $itemUri, bool $expected): void
    {
        $reflection = new ReflectionClass('taoItems_actions_Category');
        $sut = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('isValidItemUri');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invoke($sut, $itemUri));
    }

    public function itemUriProvider(): array
    {
        return [
            'URI' => ['http://example.test/tao.rdf#item', true],
            'URI with surrounding whitespace' => [' http://example.test/tao.rdf#item ', true],
            'empty string' => ['', false],
            'whitespace only' => [" \t\n", false],
            'array' => [['http://example.test/tao.rdf#item'], false],
            'null' => [null, false],
        ];
    }
}

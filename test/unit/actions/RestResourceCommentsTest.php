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

namespace oat\taoItems\test\unit\actions;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RestResourceCommentsTest extends TestCase
{
    /**
     * @dataProvider strictBooleanValuesProvider
     */
    public function testParseBoolStrict(mixed $inputValue, ?bool $expectedResult): void
    {
        $reflection = new ReflectionClass('taoItems_actions_RestResourceComments');
        $sut = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('parseBoolStrict');
        $method->setAccessible(true);

        $result = $method->invoke($sut, $inputValue);

        $this->assertSame($expectedResult, $result);
    }

    public function strictBooleanValuesProvider(): array
    {
        return [
            'bool true' => [true, true],
            'bool false' => [false, false],
            'int one' => [1, true],
            'int zero' => [0, false],
            'string true' => ['true', true],
            'string false' => ['false', false],
            'string one' => ['1', true],
            'string zero' => ['0', false],
            'trimmed uppercase true' => ['  TRUE  ', true],
            'trimmed uppercase false' => ['  FALSE  ', false],
            'yes is invalid' => ['yes', null],
            'two is invalid' => ['2', null],
            'float is invalid' => [1.0, null],
            'array is invalid' => [[], null],
            'null is invalid' => [null, null],
        ];
    }
}

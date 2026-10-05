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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA ;
 */

declare(strict_types=1);

namespace oat\taoItems\test\unit\models\classes\media;

use oat\taoItems\model\media\AssetUpdatedAtNormalizer;
use PHPUnit\Framework\TestCase;

class AssetUpdatedAtNormalizerTest extends TestCase
{
    public function testNormalizeReturnsNullForMissingOrEmptyValues(): void
    {
        $this->assertNull(AssetUpdatedAtNormalizer::normalize(null));
        $this->assertNull(AssetUpdatedAtNormalizer::normalize(''));
        $this->assertNull(AssetUpdatedAtNormalizer::normalize('   '));
        $this->assertNull(AssetUpdatedAtNormalizer::normalize([]));
        $this->assertNull(AssetUpdatedAtNormalizer::normalize(['', null]));
    }

    public function testNormalizeMicrotimeFloatString(): void
    {
        $this->assertSame(
            '2026-08-01T10:00:00Z',
            AssetUpdatedAtNormalizer::normalize('1785578400.452')
        );
    }

    public function testNormalizeUnixTimestampSeconds(): void
    {
        $this->assertSame(
            '2026-08-01T10:00:00Z',
            AssetUpdatedAtNormalizer::normalize('1785578400')
        );
    }

    public function testNormalizeUnixTimestampMilliseconds(): void
    {
        $this->assertSame(
            '2026-08-01T10:00:00Z',
            AssetUpdatedAtNormalizer::normalize('1785578400000')
        );
    }

    public function testNormalizeIsoString(): void
    {
        $this->assertSame(
            '2026-08-01T10:00:00Z',
            AssetUpdatedAtNormalizer::normalize('2026-08-01T10:00:00Z')
        );
    }

    public function testNormalizeLegacyDisplayFormat(): void
    {
        $normalized = AssetUpdatedAtNormalizer::normalize('01/08/2026 - 10:00');
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', (string)$normalized);
    }

    public function testNormalizeUsesFirstScalarFromArray(): void
    {
        $this->assertSame(
            '2026-08-01T10:00:00Z',
            AssetUpdatedAtNormalizer::normalize(['1785578400', 'ignored'])
        );
    }
}

<?php

/**
 * SPDX-FileCopyrightText: 2026-2026 Open Assessment Technologies S.A.
 * Copyright (C) 2026 (original work) Open Assessment Technologies S.A.
 *
 * SPDX-License-Identifier: AGPL-3.0-only OR LicenseRef-TAO-Commercial-License
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

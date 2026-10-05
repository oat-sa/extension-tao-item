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
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\media;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

/**
 * Normalizes asset "last modified" values to ISO-8601 UTC for API consumers.
 */
final class AssetUpdatedAtNormalizer
{
    private const ISO_UTC = 'Y-m-d\TH:i:s\Z';

    /**
     * @param mixed $value Raw updated_at / updatedAt from index or filesystem sources
     */
    public static function normalize($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            foreach ($value as $candidate) {
                $normalized = self::normalize($candidate);
                if ($normalized !== null) {
                    return $normalized;
                }
            }

            return null;
        }

        if (is_float($value)) {
            return self::fromUnixTimestamp((int)floor($value));
        }

        if (!is_scalar($value)) {
            return null;
        }

        $string = trim((string)$value);
        if ($string === '') {
            return null;
        }

        if (preg_match('/^\d+(\.\d+)?$/', $string) === 1) {
            return self::fromUnixTimestamp((int)floor((float)$string));
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $string) === 1) {
            return self::fromIsoLikeString($string);
        }

        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})\s*-\s*(\d{2}):(\d{2})/', $string, $matches) === 1) {
            return self::fromDayMonthYearString($matches);
        }

        $timestamp = strtotime($string);
        if ($timestamp !== false && $timestamp > 0) {
            return gmdate(self::ISO_UTC, $timestamp);
        }

        return null;
    }

    private static function fromUnixTimestamp(int $timestamp): ?string
    {
        if ($timestamp > 9999999999) {
            $timestamp = (int)floor($timestamp / 1000);
        }

        if ($timestamp <= 0) {
            return null;
        }

        return gmdate(self::ISO_UTC, $timestamp);
    }

    private static function fromIsoLikeString(string $value): ?string
    {
        try {
            $dateTime = new DateTimeImmutable($value);

            return $dateTime->setTimezone(new DateTimeZone('UTC'))->format(self::ISO_UTC);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param array<int, string> $matches
     */
    private static function fromDayMonthYearString(array $matches): ?string
    {
        try {
            $dateTime = new DateTimeImmutable(sprintf(
                '%s-%s-%s %s:%s:00',
                $matches[3],
                $matches[2],
                $matches[1],
                $matches[4],
                $matches[5]
            ));

            return $dateTime->setTimezone(new DateTimeZone('UTC'))->format(self::ISO_UTC);
        } catch (Exception $exception) {
            return null;
        }
    }
}

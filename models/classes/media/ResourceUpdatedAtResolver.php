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

namespace oat\taoItems\model\media;

use core_kernel_classes_Literal;
use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\filesystem\FilesystemException;
use oat\tao\model\TaoOntology;
use oat\taoMediaManager\model\fileManagement\FileManagement;
use oat\taoMediaManager\model\fileManagement\FileSourceUnserializer;
use oat\taoMediaManager\model\fileManagement\FlySystemManagement;
use oat\taoMediaManager\model\TaoMediaOntology;
use tao_helpers_Uri;

/**
 * Resolves a non-empty ISO-8601 updatedAt for Resource Manager assets.
 *
 * @license GPL-2.0-only
 * @copyright 2026 Open Assessment Technologies SA
 */
final class ResourceUpdatedAtResolver
{
    private const FALLBACK_ISO = '1970-01-01T00:00:00Z';

    /** @see \oat\taoMediaManager\model\MediaSource::SCHEME_NAME */
    private const MEDIA_BROWSER_SCHEME = 'taomedia://mediamanager/';

    /** @var FileManagement */
    private $fileManagement;

    /** @var FileSourceUnserializer */
    private $fileSourceUnserializer;

    /** @var FileSystemService */
    private $fileSystemService;

    public function __construct(
        FileManagement $fileManagement,
        FileSourceUnserializer $fileSourceUnserializer,
        FileSystemService $fileSystemService
    ) {
        $this->fileManagement = $fileManagement;
        $this->fileSourceUnserializer = $fileSourceUnserializer;
        $this->fileSystemService = $fileSystemService;
    }

    /**
     * @param array<string, mixed> $asset
     */
    public function resolveForAsset(array $asset): string
    {
        $fromFields = AssetUpdatedAtNormalizer::normalize(
            $asset['updatedAt'] ?? $asset['updated_at'] ?? null
        );
        if ($fromFields !== null) {
            return $fromFields;
        }

        $resourceUri = $this->resolveResourceUri($asset);
        if ($resourceUri !== '') {
            return $this->resolve(null, $resourceUri);
        }

        return self::FALLBACK_ISO;
    }

    /**
     * @param mixed $indexedValue
     */
    public function resolve($indexedValue, string $resourceUri): string
    {
        $fromIndex = AssetUpdatedAtNormalizer::normalize($indexedValue);
        if ($fromIndex !== null) {
            return $fromIndex;
        }

        if ($resourceUri === '') {
            return self::FALLBACK_ISO;
        }

        $fromOntology = AssetUpdatedAtNormalizer::normalize(
            $this->readOntologyUpdatedAtRaw($resourceUri)
        );
        if ($fromOntology !== null) {
            return $fromOntology;
        }

        $fromFile = AssetUpdatedAtNormalizer::normalize(
            $this->readMediaFileTimestamp($resourceUri)
        );
        if ($fromFile !== null) {
            return $fromFile;
        }

        return self::FALLBACK_ISO;
    }

    /**
     * @param array<string, mixed> $asset
     */
    private function resolveResourceUri(array $asset): string
    {
        $uri = trim((string)($asset['uri'] ?? ''));
        if ($uri === '') {
            return '';
        }

        if (strpos($uri, self::MEDIA_BROWSER_SCHEME) === 0) {
            $encoded = substr($uri, strlen(self::MEDIA_BROWSER_SCHEME));

            return tao_helpers_Uri::decode($encoded);
        }

        if (preg_match('#^https?://#i', $uri) === 1) {
            return $uri;
        }

        return '';
    }

    /**
     * @return int|float|string|null
     */
    private function readOntologyUpdatedAtRaw(string $resourceUri)
    {
        $resource = $this->loadExistingResource($resourceUri);
        if ($resource === null) {
            return null;
        }

        try {
            $raw = $resource->getOnePropertyValue(
                new core_kernel_classes_Property(TaoOntology::PROPERTY_UPDATED_AT)
            );
            if ($raw instanceof core_kernel_classes_Literal) {
                return $raw->literal;
            }

            return $raw;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    /**
     * @return int|null Unix seconds
     */
    private function readMediaFileTimestamp(string $resourceUri): ?int
    {
        $resource = $this->loadExistingResource($resourceUri);
        if ($resource === null) {
            return null;
        }

        try {
            $fileLinkRaw = $resource->getOnePropertyValue(
                new core_kernel_classes_Property(TaoMediaOntology::PROPERTY_LINK)
            );
            $fileLink = $this->resolveFileLinkFromPropertyValue($fileLinkRaw);
            if ($fileLink === null) {
                return null;
            }

            if (!$this->fileManagement instanceof FlySystemManagement) {
                return null;
            }

            return $this->readLastModifiedOnFilesystem($fileLink, $this->fileManagement);
        } catch (FilesystemException $exception) {
            return null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function loadExistingResource(string $resourceUri): ?core_kernel_classes_Resource
    {
        try {
            $resource = new core_kernel_classes_Resource($resourceUri);
            if (!$resource->exists()) {
                return null;
            }

            return $resource;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    /**
     * @param mixed $fileLinkRaw
     */
    private function resolveFileLinkFromPropertyValue($fileLinkRaw): ?string
    {
        if ($fileLinkRaw === null || $fileLinkRaw === '') {
            return null;
        }

        if ($fileLinkRaw instanceof core_kernel_classes_Resource) {
            $serialized = $fileLinkRaw->getUri();
        } elseif ($fileLinkRaw instanceof core_kernel_classes_Literal) {
            $serialized = (string)$fileLinkRaw->literal;
        } else {
            $serialized = (string)$fileLinkRaw;
        }

        $fileLink = $this->fileSourceUnserializer->unserialize($serialized);

        return $fileLink !== '' ? $fileLink : null;
    }

    private function readLastModifiedOnFilesystem(
        string $fileLink,
        FlySystemManagement $fileManagement
    ): ?int {
        $filesystem = $this->fileSystemService
            ->getFileSystem($fileManagement->getOption(FlySystemManagement::OPTION_FS));

        return $filesystem->lastModified($fileLink);
    }
}

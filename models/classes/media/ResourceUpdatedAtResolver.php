<?php

/**
 * SPDX-FileCopyrightText: 2026-2026 Open Assessment Technologies S.A.
 * Copyright (C) 2026 (original work) Open Assessment Technologies S.A.
 *
 * SPDX-License-Identifier: AGPL-3.0-only OR LicenseRef-TAO-Commercial-License
 */

declare(strict_types=1);

namespace oat\taoItems\model\media;

use core_kernel_classes_Literal;
use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use oat\oatbox\filesystem\FilesystemException;
use oat\oatbox\service\ServiceManager;
use oat\tao\model\TaoOntology;
use oat\taoMediaManager\model\fileManagement\FileManagement;
use oat\taoMediaManager\model\fileManagement\FileSourceUnserializer;
use oat\taoMediaManager\model\fileManagement\FlySystemManagement;
use oat\taoMediaManager\model\TaoMediaOntology;
use tao_helpers_Uri;

/**
 * Resolves a non-empty ISO-8601 updatedAt for Resource Manager assets.
 */
final class ResourceUpdatedAtResolver
{
    private const FALLBACK_ISO = '1970-01-01T00:00:00Z';

    /** @see \oat\taoMediaManager\model\MediaSource::SCHEME_NAME */
    private const MEDIA_BROWSER_SCHEME = 'taomedia://mediamanager/';

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

            return \tao_helpers_Uri::decode($encoded);
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
        try {
            $resource = new Resource($resourceUri);
            $raw = $resource->getOnePropertyValue(
                new Property(TaoOntology::PROPERTY_UPDATED_AT)
            );
            if ($raw instanceof Literal) {
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
        try {
            $resource = new Resource($resourceUri);
            $fileLinkRaw = $resource->getOnePropertyValue(
                new Property(TaoMediaOntology::PROPERTY_LINK)
            );
            if ($fileLinkRaw === null || $fileLinkRaw === '') {
                return null;
            }

            $fileLink = $fileLinkRaw instanceof Resource ? $fileLinkRaw->getUri() : (string)$fileLinkRaw;
            $fileLink = $this->getFileSourceUnserializer()->unserialize($fileLink);
            if ($fileLink === '') {
                return null;
            }

            $fileManagement = $this->getFileManagement();
            if (!$fileManagement instanceof FlySystemManagement) {
                return null;
            }

            $filesystem = ServiceManager::getServiceManager()
                ->get(\oat\oatbox\filesystem\FileSystemService::SERVICE_ID)
                ->getFileSystem($fileManagement->getOption(FlySystemManagement::OPTION_FS));

            return $filesystem->lastModified($fileLink);
        } catch (FilesystemException $exception) {
            return null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function getFileManagement(): FileManagement
    {
        return ServiceManager::getServiceManager()->get(FileManagement::SERVICE_ID);
    }

    private function getFileSourceUnserializer(): FileSourceUnserializer
    {
        return ServiceManager::getServiceManager()->get(FileSourceUnserializer::class);
    }
}

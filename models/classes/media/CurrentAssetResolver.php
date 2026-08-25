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

use core_kernel_classes_Resource;
use oat\tao\model\accessControl\AccessControlEnablerInterface;
use oat\tao\model\accessControl\PermissionCheckerInterface;
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\MediaBrowser;
use oat\taoMediaManager\model\MediaSource;
use tao_helpers_Uri;
use Throwable;

/**
 * Resolves a current-asset identity to its parent folder path and selectable
 * picker row data for Resource Manager replacement contexts.
 */
final class CurrentAssetResolver
{
    /** @var PermissionCheckerInterface */
    private $permissionChecker;

    public function __construct(PermissionCheckerInterface $permissionChecker)
    {
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * @param array<int, string> $mimeFilters
     * @return array{parentPath: ?string, currentAsset: ?array<string, mixed>}
     */
    public function resolve(
        string $itemUri,
        string $itemLang,
        string $currentAssetUrl,
        array $mimeFilters = []
    ): array {
        $currentAssetUrl = trim($currentAssetUrl);
        if ($currentAssetUrl === '') {
            return $this->emptyResult();
        }

        try {
            $item = new core_kernel_classes_Resource($itemUri);
            $asset = (new ItemMediaResolver($item, $itemLang))->resolve($currentAssetUrl);

            return $this->resolveFromAsset($asset, $mimeFilters);
        } catch (Throwable $exception) {
            return $this->emptyResult();
        }
    }

    /**
     * @param array<int, string> $mimeFilters
     * @return array{parentPath: ?string, currentAsset: ?array<string, mixed>}
     */
    public function resolveFromAsset(MediaAsset $asset, array $mimeFilters = []): array
    {
        $mediaSource = $asset->getMediaSource();

        if ($mediaSource instanceof AccessControlEnablerInterface) {
            $mediaSource->enableAccessControl();
        }

        $fileInfo = $mediaSource->getFileInfo($asset->getMediaIdentifier());
        $parentPath = $this->resolveParentPath($asset, $mediaSource, $fileInfo);
        $resourceUri = $this->resolvePermissionUri($asset, $mediaSource, $fileInfo);
        $selectable = $this->isSelectable($fileInfo, $resourceUri, $mimeFilters);

        return [
            'parentPath' => $parentPath,
            'currentAsset' => $selectable
                ? $this->mapPickerItem($fileInfo, $parentPath)
                : null,
        ];
    }

    /**
     * @return array{parentPath: null, currentAsset: null}
     */
    private function emptyResult(): array
    {
        return [
            'parentPath' => null,
            'currentAsset' => null,
        ];
    }

    /**
     * @param array<string, mixed> $fileInfo
     */
    private function resolveParentPath(MediaAsset $asset, MediaBrowser $mediaSource, array $fileInfo): ?string
    {
        if ($mediaSource instanceof MediaSource) {
            $resourceUri = $this->decodeMediaIdentifier($asset->getMediaIdentifier());
            $resource = new core_kernel_classes_Resource($resourceUri);
            $types = $resource->getTypes();
            $parent = reset($types);
            if ($parent instanceof core_kernel_classes_Resource) {
                return MediaSource::SCHEME_NAME . tao_helpers_Uri::encode($parent->getUri());
            }

            return null;
        }

        $link = (string)($fileInfo['uri'] ?? $asset->getMediaIdentifier());
        $parent = dirname(str_replace('\\', '/', $link));
        if ($parent === '.' || $parent === '') {
            return '/';
        }

        return $parent;
    }

    /**
     * @param array<string, mixed> $fileInfo
     */
    private function resolvePermissionUri(MediaAsset $asset, MediaBrowser $mediaSource, array $fileInfo): string
    {
        if ($mediaSource instanceof MediaSource) {
            return $this->decodeMediaIdentifier($asset->getMediaIdentifier());
        }

        return (string)($fileInfo['uri'] ?? $asset->getMediaIdentifier());
    }

    private function decodeMediaIdentifier(string $identifier): string
    {
        $withoutScheme = str_replace(MediaSource::SCHEME_NAME, '', $identifier);

        return tao_helpers_Uri::decode($withoutScheme);
    }

    /**
     * @param array<string, mixed> $fileInfo
     * @param array<int, string> $mimeFilters
     */
    private function isSelectable(array $fileInfo, string $resourceUri, array $mimeFilters): bool
    {
        if ($resourceUri !== '' && !$this->permissionChecker->hasReadAccess($resourceUri)) {
            return false;
        }

        $mime = trim((string)($fileInfo['mime'] ?? ''));
        $normalizedFilters = array_values(array_filter($mimeFilters, static function ($value): bool {
            return is_string($value) && $value !== '';
        }));

        if ($normalizedFilters === []) {
            return true;
        }

        return $mime !== '' && in_array($mime, $normalizedFilters, true);
    }

    /**
     * @param array<string, mixed> $fileInfo
     * @return array<string, mixed>
     */
    private function mapPickerItem(array $fileInfo, ?string $parentPath): array
    {
        $label = (string)($fileInfo['name'] ?? $fileInfo['label'] ?? '');

        $item = [
            'uri' => (string)($fileInfo['uri'] ?? ''),
            'label' => $label,
            'name' => $label,
            'mime' => (string)($fileInfo['mime'] ?? ''),
            'location' => $parentPath ?? '',
            'updatedAt' => $fileInfo['updatedAt'] ?? null,
        ];

        if (isset($fileInfo['permissions']) && is_array($fileInfo['permissions'])) {
            $item['permissions'] = $fileInfo['permissions'];
        }

        return $item;
    }
}

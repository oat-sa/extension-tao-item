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

use oat\tao\model\accessControl\ActionAccessControl;
use oat\tao\model\accessControl\Context;
use oat\tao\model\accessControl\PermissionCheckerInterface;
use oat\tao\model\media\MediaBrowser;
use oat\taoMediaManager\model\MediaSource;
use taoItems_actions_ItemContent;
use tao_helpers_Uri;

/**
 * Resource Manager listing read gate aligned with MediaSourcePermissionsMapper rules.
 */
final class AssetListingReadAccessChecker
{
    /** @var PermissionCheckerInterface */
    private $permissionChecker;

    /** @var ActionAccessControl */
    private $actionAccessControl;

    public function __construct(
        PermissionCheckerInterface $permissionChecker,
        ActionAccessControl $actionAccessControl
    ) {
        $this->permissionChecker = $permissionChecker;
        $this->actionAccessControl = $actionAccessControl;
    }

    /**
     * @param array<string, mixed> $assetRow normalized picker/search row
     */
    public function canReadListedAsset(MediaBrowser $mediaSource, array $assetRow, string $itemUri): bool
    {
        $resourceUri = $this->resolvePermissionUri($mediaSource, $assetRow, $itemUri);
        if ($resourceUri === '') {
            return false;
        }

        if (!$this->permissionChecker->hasReadAccess($resourceUri)) {
            return false;
        }

        if ($mediaSource instanceof LocalItemSource) {
            return true;
        }

        return $this->actionAccessControl->contextHasReadAccess(
            new Context(
                [
                    Context::PARAM_CONTROLLER => taoItems_actions_ItemContent::class,
                    Context::PARAM_ACTION => 'viewAsset',
                ]
            )
        );
    }

    /**
     * @param array<string, mixed> $assetRow
     */
    private function resolvePermissionUri(MediaBrowser $mediaSource, array $assetRow, string $itemUri): string
    {
        if ($mediaSource instanceof MediaSource) {
            $link = trim((string)($assetRow['uri'] ?? ''));

            return $link !== '' ? $this->decodeMediaIdentifier($link) : '';
        }

        if ($mediaSource instanceof LocalItemSource) {
            return trim($itemUri);
        }

        return trim((string)($assetRow['uri'] ?? ''));
    }

    private function decodeMediaIdentifier(string $identifier): string
    {
        $withoutScheme = str_starts_with($identifier, MediaSource::SCHEME_NAME)
            ? substr($identifier, strlen(MediaSource::SCHEME_NAME))
            : $identifier;

        return tao_helpers_Uri::decode($withoutScheme);
    }
}

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

namespace oat\taoItems\test\unit\models\classes\media;

use oat\generis\test\TestCase;
use oat\tao\model\accessControl\ActionAccessControl;
use oat\tao\model\accessControl\Context;
use oat\tao\model\accessControl\PermissionCheckerInterface;
use oat\tao\model\media\MediaBrowser;
use oat\taoItems\model\media\AssetListingReadAccessChecker;
use oat\taoItems\model\media\LocalItemSource;
use taoItems_actions_ItemContent;

class AssetListingReadAccessCheckerTest extends TestCase
{
    /** @var PermissionCheckerInterface */
    private $permissionChecker;

    /** @var ActionAccessControl */
    private $actionAccessControl;

    /** @var AssetListingReadAccessChecker */
    private $subject;

    protected function setUp(): void
    {
        $this->permissionChecker = $this->createMock(PermissionCheckerInterface::class);
        $this->actionAccessControl = $this->createMock(ActionAccessControl::class);
        $this->subject = new AssetListingReadAccessChecker(
            $this->permissionChecker,
            $this->actionAccessControl
        );
    }

    public function testCanReadListedAssetRequiresResourceReadAndViewAssetContext(): void
    {
        $mediaSource = $this->createMock(MediaBrowser::class);
        $resourceUri = 'http://example.com/media/1';

        $this->permissionChecker
            ->expects($this->once())
            ->method('hasReadAccess')
            ->with($resourceUri)
            ->willReturn(true);

        $this->actionAccessControl
            ->expects($this->once())
            ->method('contextHasReadAccess')
            ->with($this->callback(function (Context $context): bool {
                return $context->getParameter(Context::PARAM_CONTROLLER) === taoItems_actions_ItemContent::class
                    && $context->getParameter(Context::PARAM_ACTION) === 'viewAsset';
            }))
            ->willReturn(true);

        $this->assertTrue(
            $this->subject->canReadListedAsset(
                $mediaSource,
                ['uri' => $resourceUri],
                'http://example.com/item/1'
            )
        );
    }

    public function testCanReadListedAssetDeniesWhenViewAssetContextMissing(): void
    {
        $mediaSource = $this->createMock(MediaBrowser::class);

        $this->permissionChecker->method('hasReadAccess')->willReturn(true);
        $this->actionAccessControl->method('contextHasReadAccess')->willReturn(false);

        $this->assertFalse(
            $this->subject->canReadListedAsset(
                $mediaSource,
                ['uri' => 'http://example.com/media/1'],
                'http://example.com/item/1'
            )
        );
    }

    public function testCanReadListedAssetUsesItemUriForLocalGallery(): void
    {
        $itemUri = 'http://example.com/item/1';
        $mediaSource = $this->createMock(LocalItemSource::class);

        $this->permissionChecker
            ->expects($this->once())
            ->method('hasReadAccess')
            ->with($itemUri)
            ->willReturn(true);

        $this->actionAccessControl->expects($this->never())->method('contextHasReadAccess');

        $this->assertTrue(
            $this->subject->canReadListedAsset(
                $mediaSource,
                ['uri' => 'images/a.png'],
                $itemUri
            )
        );
    }
}

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
use oat\tao\model\accessControl\PermissionCheckerInterface;
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\MediaBrowser;
use oat\taoItems\model\media\CurrentAssetResolver;

class CurrentAssetResolverTest extends TestCase
{
    /** @var PermissionCheckerInterface */
    private $permissionChecker;

    /** @var CurrentAssetResolver */
    private $subject;

    protected function setUp(): void
    {
        $this->permissionChecker = $this->createMock(PermissionCheckerInterface::class);
        $this->subject = new CurrentAssetResolver($this->permissionChecker);
    }

    public function testResolveReturnsNullsForBlankIdentity(): void
    {
        $this->permissionChecker->expects($this->never())->method('hasReadAccess');

        $result = $this->subject->resolve('http://example/item', 'en-US', '   ');

        $this->assertNull($result['parentPath']);
        $this->assertNull($result['currentAsset']);
    }

    public function testResolveReturnsNullsWhenAssetCannotBeResolved(): void
    {
        $this->permissionChecker->expects($this->never())->method('hasReadAccess');

        $result = $this->subject->resolve(
            'http://example/item-that-does-not-exist',
            'en-US',
            'taomedia://mediamanager/missing'
        );

        $this->assertNull($result['parentPath']);
        $this->assertNull($result['currentAsset']);
    }

    public function testResolveFromLocalAssetReturnsParentFolderAndSelectableItem(): void
    {
        $mediaSource = $this->createMock(MediaBrowser::class);
        $mediaSource->expects($this->once())
            ->method('getFileInfo')
            ->with('images/cat.png')
            ->willReturn([
                'name' => 'cat.png',
                'uri' => 'images/cat.png',
                'mime' => 'image/png',
            ]);

        $this->permissionChecker->expects($this->once())
            ->method('hasReadAccess')
            ->with('images/cat.png')
            ->willReturn(true);

        $asset = new MediaAsset($mediaSource, 'images/cat.png');
        $result = $this->subject->resolveFromAsset($asset, ['image/png']);

        $this->assertSame('images', $result['parentPath']);
        $this->assertSame('images/cat.png', $result['currentAsset']['uri']);
        $this->assertSame('cat.png', $result['currentAsset']['label']);
        $this->assertSame('image/png', $result['currentAsset']['mime']);
        $this->assertSame('images', $result['currentAsset']['location']);
    }

    public function testResolveFromLocalAssetKeepsParentWhenMimeIncompatible(): void
    {
        $mediaSource = $this->createMock(MediaBrowser::class);
        $mediaSource->method('getFileInfo')->willReturn([
            'name' => 'clip.mp4',
            'uri' => 'media/clip.mp4',
            'mime' => 'video/mp4',
        ]);

        $this->permissionChecker->method('hasReadAccess')->willReturn(true);

        $asset = new MediaAsset($mediaSource, 'media/clip.mp4');
        $result = $this->subject->resolveFromAsset($asset, ['image/png']);

        $this->assertSame('media', $result['parentPath']);
        $this->assertNull($result['currentAsset']);
    }

    public function testResolveFromLocalAssetKeepsParentWhenAccessDenied(): void
    {
        $mediaSource = $this->createMock(MediaBrowser::class);
        $mediaSource->method('getFileInfo')->willReturn([
            'name' => 'secret.png',
            'uri' => 'images/secret.png',
            'mime' => 'image/png',
        ]);

        $this->permissionChecker->method('hasReadAccess')->willReturn(false);

        $asset = new MediaAsset($mediaSource, 'images/secret.png');
        $result = $this->subject->resolveFromAsset($asset, ['image/png']);

        $this->assertSame('images', $result['parentPath']);
        $this->assertNull($result['currentAsset']);
    }

    public function testResolveFromLocalRootFileUsesRootParentPath(): void
    {
        $mediaSource = $this->createMock(MediaBrowser::class);
        $mediaSource->method('getFileInfo')->willReturn([
            'name' => 'root.png',
            'uri' => 'root.png',
            'mime' => 'image/png',
        ]);
        $this->permissionChecker->method('hasReadAccess')->willReturn(true);

        $asset = new MediaAsset($mediaSource, 'root.png');
        $result = $this->subject->resolveFromAsset($asset);

        $this->assertSame('/', $result['parentPath']);
        $this->assertSame('root.png', $result['currentAsset']['uri']);
    }
}

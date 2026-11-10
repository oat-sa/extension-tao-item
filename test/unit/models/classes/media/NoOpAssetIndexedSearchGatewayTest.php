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
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\MediaBrowser;
use oat\taoItems\model\media\AssetSearchQuery;
use oat\taoItems\model\media\AssetSearchUnavailableException;
use oat\taoItems\model\media\NoOpAssetIndexedSearchGateway;

class NoOpAssetIndexedSearchGatewayTest extends TestCase
{
    public function testIsAvailableReturnsFalse(): void
    {
        $this->assertFalse((new NoOpAssetIndexedSearchGateway())->isAvailable());
    }

    public function testSearchThrowsUnavailableException(): void
    {
        $this->expectException(AssetSearchUnavailableException::class);

        $mediaSource = $this->createMock(MediaBrowser::class);
        $query = new AssetSearchQuery(new MediaAsset($mediaSource, '/'), 'item', 'en-US');

        (new NoOpAssetIndexedSearchGateway())->search($query);
    }
}

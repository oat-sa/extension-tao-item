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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA.
 *
 * @author Andrei Shapiro <andrei.shapiro@taotesting.com>
 */

declare(strict_types=1);

namespace oat\taoItems\test\unit\models\classes\Copier;

use PHPUnit\Framework\TestCase;
use oat\oatbox\event\EventManager;
use taoItems_models_classes_ItemsService;
use PHPUnit\Framework\MockObject\MockObject;
use oat\taoItems\model\Copier\ItemContentCopier;
use oat\generis\model\fileReference\FileReferenceSerializer;

class ItemContentCopierTest extends TestCase
{
    /** @var ItemContentCopier */
    private $sut;

    /** @var FileReferenceSerializer|MockObject */
    private $fileReferenceSerializer;

    /** @var taoItems_models_classes_ItemsService|MockObject */
    private $itemsService;

    /** @var EventManager|MockObject */
    private $eventManager;

    protected function setUp(): void
    {
        $this->fileReferenceSerializer = $this->createMock(FileReferenceSerializer::class);
        $this->itemsService = $this->createMock(taoItems_models_classes_ItemsService::class);
        $this->eventManager = $this->createMock(EventManager::class);

        $this->sut = new ItemContentCopier(
            $this->fileReferenceSerializer,
            $this->itemsService,
            $this->eventManager
        );
    }

    public function testCopy(): void
    {
        $this->markTestIncomplete();
    }
}

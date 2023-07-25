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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA
 */

declare(strict_types=1);

namespace oat\taoItems\test\unit\model\event;

use oat\taoItems\model\event\ItemRemovedEvent;
use PHPUnit\Framework\TestCase;

class ItemRemovedEventTest extends TestCase
{
    public function testGetters(): void
    {
        $event = new ItemRemovedEvent(
            'itemUri',
            [
                ItemRemovedEvent::PAYLOAD_KEY_DELETE_RELATED_ASSETS => true,
            ]
        );

        $this->assertSame(ItemRemovedEvent::class, $event->getName());
        $this->assertSame(
            [
                ItemRemovedEvent::PAYLOAD_KEY_ITEM_URI => 'itemUri',
                ItemRemovedEvent::PAYLOAD_KEY_DELETE_RELATED_ASSETS => true,
            ],
            $event->jsonSerialize()
        );
    }
}

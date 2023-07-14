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
 * Copyright (c) 2016-2023 (original work) Open Assessment Technologies SA.
 */

namespace oat\taoItems\model\event;

use oat\oatbox\event\Event;
use JsonSerializable;

class ItemRemovedEvent implements Event, JsonSerializable
{
    public const PAYLOAD_KEY_DELETE_RELATED_ASSETS = 'deleteRelatedAssets';
    public const PAYLOAD_KEY_ITEM_URI = 'itemUri';

    protected string $itemUri;

    private array $payload;

    public function __construct(string $itemUri, array $payload = [])
    {
        $this->itemUri = $itemUri;
        $this->payload = $payload;
    }

    public function getName()
    {
        return get_class($this);
    }

    public function jsonSerialize()
    {
        return array_merge(
            [self::PAYLOAD_KEY_ITEM_URI => $this->itemUri],
            $this->payload
        );
    }
}

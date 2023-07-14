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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\taoItems\model\Command;

use core_kernel_classes_Resource;

class DeleteItemCommand
{
    private core_kernel_classes_Resource $resource;
    private bool $deleteRelatedAssets;

    public function __construct(core_kernel_classes_Resource $resource, bool $deleteRelatedAssets = false)
    {
        $this->resource = $resource;
        $this->deleteRelatedAssets = $deleteRelatedAssets;
    }

    public function getResource(): core_kernel_classes_Resource
    {
        return $this->resource;
    }

    public function mustDeleteRelatedAssets(): bool
    {
        return $this->deleteRelatedAssets;
    }
}

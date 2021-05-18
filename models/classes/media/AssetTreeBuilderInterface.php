<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable].
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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\media;

use oat\tao\model\media\mediaSource\DirectorySearchQuery;

interface AssetTreeBuilderInterface
{
    /**
     * @example Returns:
     *  [
     *      "path": "https_2_taotesting_0_com_1_ontologies_1_tao_0_rdf_3_i5123456",
     *      "label": "Assets",
     *      "childrenLimit": 15,
     *      "permissions": [
     *          "READ",
     *          "WRITE",
     *      ],
     *      "children": [
     *          [
     *              "path": "https_2_taotesting_0_com_1_ontologies_1_tao_0_rdf_3_i5123456",
     *              "label": "test",
     *              "childrenLimit": 15,
     *              "permissions": [
     *                  "READ",
     *                  "WRITE",
     *              ],
     *              "url": "https://taotesting.com/taoItems/ItemContent/files",
     *          ]
     *      ]
     *   ]
     */
    public function build(DirectorySearchQuery $search): array;
}

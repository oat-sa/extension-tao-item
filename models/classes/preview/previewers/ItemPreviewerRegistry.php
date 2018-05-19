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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoItems\model\preview\previewers;

use common_ext_ExtensionsManager;
use oat\tao\model\modules\AbstractModuleRegistry;

/**
 * Store the <b>available</b> item previewers, even if not activated previewers have to be registered.
 *
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
class ItemPreviewerRegistry extends AbstractModuleRegistry
{
    /**
     * @see \oat\oatbox\AbstractRegistry::getConfigId()
     */
    protected function getConfigId()
    {
        return 'item_previewer_registry';
    }

    /**
     * @see \oat\oatbox\AbstractRegistry::getExtension()
     */
    protected function getExtension()
    {
        return common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
    }
}

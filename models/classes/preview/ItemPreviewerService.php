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

namespace oat\taoItems\model\preview;

use oat\oatbox\service\ConfigurableService;
use oat\tao\model\ClientLibConfigRegistry;
use oat\tao\model\modules\DynamicModule;

/**
 * Manage item previewers
 *
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
class ItemPreviewerService extends ConfigurableService
{
    const SERVICE_ID = 'taoItems/ItemPreviewer';
    const REGISTRY_ID = 'taoItems/previewer/factory';
    const PREVIEWERS_KEY = 'previewers';

    /**
     * @return \oat\oatbox\AbstractRegistry
     */
    public function getRegistry()
    {
        return ClientLibConfigRegistry::getRegistry();
    }

    /**
     * Register a previewer adapter
     * @param DynamicModule $module the plugin to register
     * @return boolean true if registered
     */
    public function registerAdapter(DynamicModule $module)
    {
        if(!is_null($module) && ! empty($module->getModule()) ) {

            $registry = $this->getRegistry();
            $config = [];
            if ($registry->isRegistered(self::REGISTRY_ID)) {
                $config = $registry->get(self::REGISTRY_ID);
            }

            $config[self::PREVIEWERS_KEY][$module->getModule()] = $module->toArray();
            $registry->set(self::REGISTRY_ID, $config);
            return true;
        }
        return false;
    }

    /**
     * Unregister a previewer adapter
     * @param string $moduleId
     * @return boolean true if unregistered
     */
    public function unregisterAdapter($moduleId)
    {

        $registry = $this->getRegistry();
        $config = [];
        if ($registry->isRegistered(self::REGISTRY_ID)) {
            $config = $registry->get(self::REGISTRY_ID);
        }

        if (isset($config[self::PREVIEWERS_KEY][$moduleId])) {
            unset($config[self::PREVIEWERS_KEY][$moduleId]);
            $registry->set(self::REGISTRY_ID, $config);
            return true;
        }
        return false;
    }
}

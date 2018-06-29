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

use oat\oatbox\AbstractRegistry;
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
    const REGISTRY_ENTRY_KEY = 'taoItems/previewer/factory';
    const PREVIEWERS_KEY = 'previewers';
    
    private $registry;

    /**
     * Sets the registry that contains the list of adapters
     * @param AbstractRegistry $registry
     */
    public function setRegistry(AbstractRegistry $registry) 
    {
        $this->registry = $registry;    
    }

    /**
     * Gets the registry that contains the list of adapters
     * @return AbstractRegistry
     */
    public function getRegistry()
    {
        if (!$this->registry) {
            $this->registry = ClientLibConfigRegistry::getRegistry();
        }
        return $this->registry;
    }

    /**
     * Gets the list of adapters
     * @return array
     */
    public function getAdapters()
    {
        $registry = $this->getRegistry();
        $config = [];
        if ($registry->isRegistered(self::REGISTRY_ENTRY_KEY)) {
            $config = $registry->get(self::REGISTRY_ENTRY_KEY);
        }

        if (isset($config[self::PREVIEWERS_KEY])) {
            return $config[self::PREVIEWERS_KEY];
        }
        return [];
    }

    /**
     * Registers a previewer adapter
     * @param DynamicModule $module the plugin to register
     * @return boolean true if registered
     */
    public function registerAdapter(DynamicModule $module)
    {
        if(!is_null($module) && ! empty($module->getModule()) ) {

            $registry = $this->getRegistry();
            $config = [];
            if ($registry->isRegistered(self::REGISTRY_ENTRY_KEY)) {
                $config = $registry->get(self::REGISTRY_ENTRY_KEY);
            }

            $config[self::PREVIEWERS_KEY][$module->getModule()] = $module->toArray();
            $registry->set(self::REGISTRY_ENTRY_KEY, $config);
            return true;
        }
        return false;
    }

    /**
     * Unregisters a previewer adapter
     * @param string $moduleId
     * @return boolean true if unregistered
     */
    public function unregisterAdapter($moduleId)
    {

        $registry = $this->getRegistry();
        $config = [];
        if ($registry->isRegistered(self::REGISTRY_ENTRY_KEY)) {
            $config = $registry->get(self::REGISTRY_ENTRY_KEY);
        }

        if (isset($config[self::PREVIEWERS_KEY]) && isset($config[self::PREVIEWERS_KEY][$moduleId])) {
            unset($config[self::PREVIEWERS_KEY][$moduleId]);
            $registry->set(self::REGISTRY_ENTRY_KEY, $config);
            return true;
        }
        return false;
    }
}

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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

/**
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class taoItems_scripts_update_Updater extends \common_ext_ExtensionUpdater {
    
    /**
     * 
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {
        
        $currentVersion = $initialVersion;
        
        //migrate from 2.6 to 2.6.1
        if ($currentVersion == '2.6') {
        
            $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
            $file = $ext->getDir().'models'.DIRECTORY_SEPARATOR.'ontology'.DIRECTORY_SEPARATOR.'indexation.rdf';
        
            $adapter = new tao_helpers_data_GenerisAdapterRdf();
            if($adapter->import($file)){
                $currentVersion = '2.6.1';
            } else{
                common_Logger::w('Import failed for '.$file);
            }
        }
        
        return $currentVersion;
        
        return $currentVersion;
    }
}

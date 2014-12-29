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
        
            $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'indexation_2_6_1.rdf';
        
            $adapter = new tao_helpers_data_GenerisAdapterRdf();
            if($adapter->import($file)){
                $currentVersion = '2.6.1';
            } else{
                common_Logger::w('Import failed for '.$file);
            }
        }
        
        if ($currentVersion == '2.6.1') {
            
            // double check
            $index = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContentIndex');
            $default = $index->getPropertiesValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAO.rdf#IndexDefaultSearch'));
            
            if (count($default) == 0) {
                
                //no default search set, import
                $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'indexation_2_6_2.rdf';
            
                $adapter = new tao_helpers_data_GenerisAdapterRdf();
                if($adapter->import($file)){
                    $currentVersion = '2.6.2';
                } else{
                    common_Logger::w('Import failed for '.$file);
                }
                
            } else {
                common_Logger::w('Defautl Search already set');
                $currentVersion = '2.6.2';
            }
        }
        
        return $currentVersion;
    }
}

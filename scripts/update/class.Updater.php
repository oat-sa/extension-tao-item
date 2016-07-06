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

use oat\tao\scripts\update\OntologyUpdater;
use oat\taoItems\model\ontology\ItemAuthorRole;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\accessControl\func\AccessRule;

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
        
     
        
        //migrate from 2.6 to 2.6.1
        if ($this->isVersion('2.6')) {
        
            $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'indexation_2_6_1.rdf';
        
            $adapter = new tao_helpers_data_GenerisAdapterRdf();
            if($adapter->import($file)){
                $$this->setVerion('2.6.1');
            } else{
                common_Logger::w('Import failed for '.$file);
            }
        }
        
        if ($this->isVersion('2.6.1')) {
            
            // double check
            $index = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContentIndex');
            $default = $index->getPropertyValues(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAO.rdf#IndexDefaultSearch'));
            
            if (count($default) == 0) {
                
                //no default search set, import
                $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'indexation_2_6_2.rdf';
            
                $adapter = new tao_helpers_data_GenerisAdapterRdf();
                if($adapter->import($file)){
                    $this->setVerion('2.6.2');
                } else{
                    common_Logger::w('Import failed for '.$file);
                }
                
            } else {
                common_Logger::w('Defautl Search already set');
                $$this->setVerion('2.6.2');
            }
        }
        
        if ($this->isVersion('2.6.2')) {
            
            OntologyUpdater::correctModelId(dirname(__FILE__).DIRECTORY_SEPARATOR.'indexation_2_6_1.rdf');
            OntologyUpdater::correctModelId(dirname(__FILE__).DIRECTORY_SEPARATOR.'indexation_2_6_2.rdf');
            $this->setVerion('2.6.3');
        
        }
        
        if ($this->isVersion('2.6.3')) {
            // update user roles
            $class = new core_kernel_classes_Class(CLASS_TAO_USER);
            $itemManagers = $class->searchInstances(array(
	               PROPERTY_USER_ROLES => 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemsManagerRole'
                ),array('recursive' => true, 'like' => false)
            );
            foreach ($itemManagers as $user) {
                $user->setPropertyValue(new core_kernel_classes_Property(PROPERTY_USER_ROLES),ItemAuthorRole::INSTANCE_URI);
            }
            $this->setVerion('2.6.4');
        
        }
        
        if ($this->isBetween('2.6.4','2.8')) {
            $this->setVersion('2.8');
        }

        // fix itemModelLabelProp
        if ($this->isVersion('2.8')) {
            $fakeProperty = new core_kernel_classes_Property('itemModelLabel');
            $iterator = new core_kernel_classes_ResourceIterator(array(taoItems_models_classes_ItemsService::singleton()->getRootClass()));
            foreach ($iterator as $resource) {
                $resource->removePropertyValues($fakeProperty);
            }
            $this->setVersion('2.8.1');
        }

        $this->skip('2.8.1','2.14.0');
        
        if ($this->isVersion('2.14.0')) {
            OntologyUpdater::syncModels();
            AclProxy::applyRule(new AccessRule('grant', 'http://www.tao.lu/Ontologies/TAOItem.rdf#AbstractItemAuthor', 'taoItems_actions_ItemContent'));
            $this->setVersion('2.15.0');
        }

        $this->skip('2.15.0', '2.18.1');
    }
}

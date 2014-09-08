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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA
 * 
 */

/**
 * Crud services implements basic CRUD services, orginally intended for
 * REST controllers/ HTTP exception handlers.
 * Consequently the signatures and behaviors is closer to REST and throwing HTTP like exceptions
 *
 * @author Patrick Plichart, patrick@taotesting.com
 *        
 */
class taoItems_models_classes_CrudItemsService extends tao_models_classes_CrudService
{

    /**
     * (non-PHPdoc)
     * 
     * @see tao_models_classes_CrudService::getClassService()
     */
    public function getClassService()
    {
        return taoItems_models_classes_ItemsService::singleton();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see tao_models_classes_CrudService::delete()
     */
    public function delete($resource)
    {
        taoItems_models_classes_ItemsService::singleton()->deleteItem(new core_kernel_classes_Resource($resource));
        // parent::delete($resource)
        return true;
    }

    /**
     *
     * @param array parameters an array of property uri and values
     * @author Patrick Plichart, patrick@taotesting.com
     */
    public function createFromArray(array $propertiesValues)
    {
        if (! isset($propertiesValues[RDFS_LABEL])) {
            $propertiesValues[RDFS_LABEL] = "";
        }
        
        /* if (isset($propertiesValues[TAO_ITEM_CONTENT_PROPERTY])) */
        $type = isset($propertiesValues[RDF_TYPE]) ? $propertiesValues[RDF_TYPE] : $this->getRootClass();
        $label = $propertiesValues[RDFS_LABEL];
        unset($propertiesValues[RDFS_LABEL]);
        unset($propertiesValues[RDF_TYPE]);
        
        $itemContent = null;
        if (isset($propertiesValues[TAO_ITEM_CONTENT_PROPERTY])) {
            $itemContent = $propertiesValues[TAO_ITEM_CONTENT_PROPERTY];
            unset($propertiesValues[TAO_ITEM_CONTENT_PROPERTY]);
        }
        $resource = parent::create($label, $type, $propertiesValues);
        if (isset($itemContent)) {
            $this->itemsServices->setItemContent($resource, $itemContent);
        }
        return $resource;
    }
}

?>

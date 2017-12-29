<?php
/*  
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
 * 
 */

use oat\tao\model\TaoOntology;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use \oat\taoQtiItem\model\qti\Service;

/**
 * .Crud services implements basic CRUD services, orginally intended for REST controllers/ HTTP exception handlers
 *  Consequently the signatures and behaviors is closer to REST and throwing HTTP like exceptions
 *  
 *
 * 
 */
class taoItems_models_classes_CrudItemsService
    extends tao_models_classes_CrudService
{
   protected $itemClass = null;
   
   protected $itemsServices = null;

    public function __construct(){
		parent::__construct();
		$this->itemClass = new core_kernel_classes_Class(TaoOntology::ITEM_CLASS_URI);
		$this->itemsServices = taoItems_models_classes_ItemsService::singleton();
    }

    public function getRootClass(){
		return $this->itemClass;
	}

	protected function getClassService(){
	    return $this->itemsServices;
	}
    
    public function delete( $resource){
         taoItems_models_classes_ItemsService::singleton()->deleteItem(new core_kernel_classes_Resource($resource));
	//parent::delete($resource)
         return true;
    }


    /**
     * @param array parameters an array of property uri and values
     */
    public function createFromArray(array $propertiesValues){
	
	    if (!isset($propertiesValues[OntologyRdfs::RDFS_LABEL])) {
			$propertiesValues[OntologyRdfs::RDFS_LABEL] = "";
		}
		
		$type = isset($propertiesValues[OntologyRdf::RDF_TYPE]) ? $propertiesValues[OntologyRdf::RDF_TYPE] : $this->getRootClass();
		$label = $propertiesValues[OntologyRdfs::RDFS_LABEL];
		unset($propertiesValues[OntologyRdfs::RDFS_LABEL]);
		unset($propertiesValues[OntologyRdf::RDF_TYPE]);

		$itemContent = null;
		if (isset($propertiesValues[taoItems_models_classes_ItemsService::PROPERTY_ITEM_CONTENT])) {
		    $itemContent = $propertiesValues[taoItems_models_classes_ItemsService::PROPERTY_ITEM_CONTENT];
		    unset($propertiesValues[taoItems_models_classes_ItemsService::PROPERTY_ITEM_CONTENT]);
		}
		$resource =  parent::create($label, $type, $propertiesValues);
		if (isset($itemContent)) {
            Service::singleton()->saveXmlItemToRdfItem($itemContent, $resource);
		}
		return $resource;
    }


    
}

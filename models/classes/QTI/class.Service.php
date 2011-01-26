<?php

error_reporting(E_ALL);

/**
 * The QTI_Service gives you a central access to the managment methods of the
 * objects
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_models_classes_Service
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.Service.php');

/* user defined includes */
// section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024A8-includes begin
// section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024A8-includes end

/* user defined constants */
// section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024A8-constants begin
// section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024A8-constants end

/**
 * The QTI_Service gives you a central access to the managment methods of the
 * objects
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Service
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Load a QTI_Item from an, RDF Item using the itemContent property of the
     * Item as the QTI xml
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @return taoItems_models_classes_QTI_Item
     */
    public function getDataItemByRdfItem( core_kernel_classes_Resource $item)
    {
        $returnValue = null;

        // section 127-0-1-1-4232f639:12ba47885af:-8000:00000000000025D5 begin
        
        if(!is_null($item)){
        	
        	try{
        		
        		$itemService = tao_models_classes_ServiceFactory::get('items');
        		
        		//check if the item is QTI item
        		if($itemService->hasItemModel($item, array(TAO_ITEM_MODEL_QTI))){
        			
        			//get the QTI xml
        			$itemContent = $itemService->getItemContent($item);
					
        			if(!empty($itemContent)){
	        			//Parse it and build the QTI_Data_Item
	        			$qtiParser = new taoItems_models_classes_QTI_Parser($itemContent);
						$returnValue = $qtiParser->load();
						
						if(!$returnValue->getOption('lang')){
	    					$returnValue->setOption('lang', core_kernel_classes_Session::singleton()->getLg());
						}
        			}
        		}
				
        	}catch(common_Exception $ce){
        		print $ce;
        	}
        }
        
        // section 127-0-1-1-4232f639:12ba47885af:-8000:00000000000025D5 end

        return $returnValue;
    }

    /**
     * Save a QTI_Item into an RDF Item, by exporting the QTI_Item to QTI xml
     * and saving it in the itemContent prioperty of the RDF Item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Item qtiItem
     * @param  Resource rdfItem
     * @return boolean
     */
    public function saveDataItemToRdfItem( taoItems_models_classes_QTI_Item $qtiItem,  core_kernel_classes_Resource $rdfItem)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4232f639:12ba47885af:-8000:00000000000025D8 begin
        
    	if(!is_null($rdfItem) && !is_null($qtiItem)){
        	
        	try{
        		
        		$itemService = tao_models_classes_ServiceFactory::get('Items');
        		
        		//check if the item is QTI item
        		if($itemService->hasItemModel($rdfItem, array(TAO_ITEM_MODEL_QTI))){
        			
        			//set the current data lang in the item content to keep the integrity
    				$qtiItem->setOption('lang', core_kernel_classes_Session::singleton()->getLg());
        			
        			//get the QTI xml
        			$returnValue = $itemService->setItemContent($rdfItem, $qtiItem->toQTI());
        		}
				
        	}catch(common_Exception $ce){
        		print $ce;
        	}
        }
        
        // section 127-0-1-1-4232f639:12ba47885af:-8000:00000000000025D8 end

        return (bool) $returnValue;
    }

    /**
     * Load a QTI item from a qti file in parameter.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string file
     * @return taoItems_models_classes_QTI_Item
     */
    public function loadItemFromFile($file)
    {
        $returnValue = null;

        // section 127-0-1-1-47db9c49:12bc8da1be4:-8000:00000000000026E6 begin
        
        if(is_string($file) && !empty($file)){
       		
        	//validate the file to import
			try{
				$qtiParser = new taoItems_models_classes_QTI_Parser($file);
				$qtiParser->validate();

				if(!$qtiParser->isValid()){
					throw new Exception($qtiParser->displayErrors());
				}
				
				$returnValue = $qtiParser->load();
			}catch(Exception $e){
				throw new Exception("Unable to load file ${file}.");
			}
		}
        
        // section 127-0-1-1-47db9c49:12bc8da1be4:-8000:00000000000026E6 end

        return $returnValue;
    }

    /**
     * Retrive a QTI_Item instance by it's id
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string serial
     * @return taoItems_models_classes_QTI_Item
     */
    public function getItemBySerial($serial)
    {
        $returnValue = null;

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024A9 begin
        
        $returnValue = $this->getDataBySerial($serial, 'taoItems_models_classes_QTI_Item');
        
        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024A9 end

        return $returnValue;
    }

    /**
     * Retrive a QTI_Interaction instance by it's id
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string serial
     * @return doc_Interaction
     */
    public function getInteractionBySerial($serial)
    {
        $returnValue = null;

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024C3 begin
        
        $returnValue = $this->getDataBySerial($serial, 'taoItems_models_classes_QTI_Interaction');
        
        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:00000000000024C3 end

        return $returnValue;
    }

    /**
     * Retrive a QTI_Response instance by it's id
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string serial
     * @return taoItems_models_classes_QTI_Response
     */
    public function getResponseBySerial($serial)
    {
        $returnValue = null;

        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024D1 begin
        
         $returnValue = $this->getDataBySerial($serial, 'taoItems_models_classes_QTI_Response');
        
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024D1 end

        return $returnValue;
    }

    /**
     * Retrive a QTI_Data child instance by it's id
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string serial
     * @param  string type
     * @return taoItems_models_classes_QTI_Data
     */
    public function getDataBySerial($serial, $type = '')
    {
        $returnValue = null;

        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024E1 begin
        
    	if(!empty($serial)){
    		$key = taoItems_models_classes_QTI_Data::PREFIX . $serial;
	        if(Session::hasAttribute($key)){

	        	$data = @unserialize(Session::getAttribute($key));
	        
	        	if($data === false){
	        		throw new Exception("Unable to unserialize session entry identified by $serial");
	        	}
	        	if(!empty($type)){
	        		if( ! $data instanceof $type) {
	        			throw new Exception("object retrieved is a ".get_class($data)." instead of {$type}.");
	        		}
	        	}
	        	
	        	$returnValue = $data;
	        }
        }
        
        
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024E1 end

        return $returnValue;
    }

    /**
     * Enable you to retrieve the element containing the composed element.  
     * For example, you can retrieve the item from an interaction.
     * It works only of the objects are in the persistancy.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Data composed
     * @return taoItems_models_classes_QTI_Data
     */
    public function getComposingData( taoItems_models_classes_QTI_Data $composed)
    {
        $returnValue = null;

        // section 127-0-1-1-fba198:12b7c55f735:-8000:00000000000025B8 begin
        
        if(taoItems_models_classes_QTI_Data::$persist == false){
        	throw new Exception("The composing data are got from the persistance!");
        }
        
        if(!is_null($composed)){
        	$tokens = explode('_', get_class($composed));
        	$objectType = strtolower($tokens[count($tokens) - 1]);
        	$propertyName = $objectType . 's';
        	$methodName = 'get'.ucfirst($propertyName);
			
			//singular property name:
			$singularPropertyName = $objectType;
        	$singularMethodName = 'get'.ucfirst($singularPropertyName);
        }
        
		$instances = taoItems_models_classes_QTI_Data::$_instances;
		
        foreach(Session::getAttributeNames() as $attrKey){
        	if(preg_match("/^".taoItems_models_classes_QTI_Data::PREFIX."/", $attrKey)){
				$attrKey = str_replace(taoItems_models_classes_QTI_Data::PREFIX,'',$attrKey);
        		if(!in_array($attrKey, $instances)){
        			$instances[] = $attrKey;
        		}
        	}
        }
        
        foreach($instances as $serial){
			$instance = null;
        	try{
				$instance = $this->getDataBySerial($serial);
			}catch(Exception $e){}
			
			if(is_null($instance)){
				// newly constructed object that has not been saved into session yet or wrong variable that failed to be unserialized:
				continue;
			}
			
        	$rObject  = new ReflectionObject($instance);
        	if($rObject->hasProperty($propertyName)){
        		foreach($instance->$methodName() as $attribute){
					if($attribute instanceof taoItems_models_classes_QTI_Data){
						if($attribute->getSerial() == $composed->getSerial()){
							$returnValue = $instance;
							break;
						}
					}
        		}
        	}
			if($rObject->hasProperty($singularPropertyName)){
				$attribute = $instance->$singularMethodName();
				if($attribute instanceof taoItems_models_classes_QTI_Data){
					if($attribute->getSerial() == $composed->getSerial()){
						$returnValue = $instance;
					}
				}
			}
			
        	if(!is_null($returnValue)){
        		break;
        	}
        }
        
        // section 127-0-1-1-fba198:12b7c55f735:-8000:00000000000025B8 end

        return $returnValue;
    }

    /**
     * force the saving of the object in the persistancy. Usually an object is
     * by destruction.
     * Use this method if you know what your are doing.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Data qtiObject
     * @return boolean
     */
    public static function saveDataToSession( taoItems_models_classes_QTI_Data $qtiObject)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-11450a84:12b8101447d:-8000:00000000000028DE begin
		if(!is_null($qtiObject)){
			if(taoItems_models_classes_QTI_Data::$persist == false){
				throw new Exception("Cannot save data to session when persistence is disabled");
			}else{
				Session::setAttribute(taoItems_models_classes_QTI_Data::PREFIX . $qtiObject->getSerial(), serialize($qtiObject));
				$returnValue = true;
				
				//need to wakup the object to allow reuse in the rest of the script
				$qtiObject->__wakeup();
			}
		}
        // section 10-13-1-39-11450a84:12b8101447d:-8000:00000000000028DE end

        return (bool) $returnValue;
    }

    /**
     * Build the XHTML/CSS/JS from a QTI_Item to be rendered.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Item item the item to render
     * @return string
     */
    public function renderItem( taoItems_models_classes_QTI_Item $item)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-49582216:12ba4862c6b:-8000:00000000000025E4 begin
        
        if(!is_null($item)){
        	$returnValue =  $item->toXHTML();
        }
			
        // section 127-0-1-1-49582216:12ba4862c6b:-8000:00000000000025E4 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Service */

?>
<?php

error_reporting(E_ALL);

/**
 * The QTI_Service gives you a central access to the managment methods of the
 * objects
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Service is the base class of all services, and implements the singleton
 * for derived services
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/class.Service.php');

/**
 * include taoItems_models_classes_itemModelService
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/interface.itemModelService.php');

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
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Service
    extends tao_models_classes_Service
        implements taoItems_models_classes_itemModelService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method render
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource item
     * @return string
     */
    public function render( core_kernel_classes_Resource $item)
    {
        $returnValue = (string) '';

        // section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C26 begin
		$qtiItem = $this->getDataItemByRdfItem($item);

		if(!is_null($qtiItem)) {
			$returnValue = $this->renderQTIItem($qtiItem);
		}
        // section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C26 end

        return (string) $returnValue;
    }

    /**
     * Load a QTI_Item from an, RDF Item using the itemContent property of the
     * Item as the QTI xml
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource item
     * @return taoItems_models_classes_QTI_Item
     */
    public function getDataItemByRdfItem( core_kernel_classes_Resource $item)
    {
        $returnValue = null;

        // section 127-0-1-1-4232f639:12ba47885af:-8000:00000000000025D5 begin
        
        if(!is_null($item)){
        	
        	try{
        		
        		$itemService = taoItems_models_classes_ItemsService::singleton();
        		
        		//check if the item is QTI item
        		if($itemService->hasItemModel($item, array(TAO_ITEM_MODEL_QTI))){
        			
        			//get the QTI xml
        			$itemContent = $itemService->getItemContent($item);
					
        			if(!empty($itemContent)){
	        			//Parse it and build the QTI_Data_Item
	        			$qtiParser = new taoItems_models_classes_QTI_Parser($itemContent);
						$returnValue = $qtiParser->load();
						
						if(!$returnValue->getOption('lang')){
	    					$returnValue->setOption('lang', core_kernel_classes_Session::singleton()->getDataLanguage());
						}
						
						//load Measures
						$measurements = $itemService->getItemMeasurements($item);
	        			foreach ($returnValue->getOutcomes() as $outcome) {
	        				foreach ($measurements as $measurement) {
	        					if ($measurement->getIdentifier() == $outcome->getIdentifier()
	        						&& !is_null($measurement->getScale())) {
	        						$outcome->setScale($measurement->getScale());
	        						break;
	        					}
	        				}
	        			}
        			} else {
        				// fail silently, since file might not have been created yet
						common_Logger::d('item('.$item->getUri().') is empty, newly created?');
        			}
        		} else {
        			throw new common_Exception('Non QTI item('.$item->getUri().') opened via QTI Service');
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
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item qtiItem
     * @param  Resource rdfItem
     * @param  string commitMessage
     * @param  Repository fileSource
     * @return boolean
     */
    public function saveDataItemToRdfItem( taoItems_models_classes_QTI_Item $qtiItem,  core_kernel_classes_Resource $rdfItem, $commitMessage = '',  core_kernel_versioning_Repository $fileSource = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4232f639:12ba47885af:-8000:00000000000025D8 begin
        
    	if(!is_null($rdfItem) && !is_null($qtiItem)){
        	
        	try{
        		
        		$itemService = taoItems_models_classes_ItemsService::singleton();
        		
        		//check if the item is QTI item
        		if($itemService->hasItemModel($rdfItem, array(TAO_ITEM_MODEL_QTI))){
        			
        			//set the current data lang in the item content to keep the integrity
    				$qtiItem->setOption('lang', core_kernel_classes_Session::singleton()->getDataLanguage());
    				
        			//get the QTI xml
        			$itemsaved = $itemService->setItemContent($rdfItem, $qtiItem->toQTI(), '', $commitMessage, $fileSource);
					
					//update RDF item's label:
        			$rdfItem->setLabel($qtiItem->getOption('title'));
					
        			if ($itemsaved) {
        			// extract the measurements
        				$measurements = array();
	        			foreach ($qtiItem->getOutcomes() as $outcome) {
	        				$measurements[] = $outcome->toMeasurement($qtiItem);
	        			}
	        			$returnValue = $itemService->setItemMeasurements($rdfItem, $measurements);
        			} 
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
					throw new taoItems_models_classes_QTI_ParsingException($qtiParser->displayErrors());
				}
				
				$returnValue = $qtiParser->load();
				
			}
			catch(taoItems_models_classes_QTI_ParsingException $pe){
				throw new taoItems_models_classes_QTI_ParsingException($pe->getMessage());
			}
			catch(Exception $e){
				throw new Exception("Unable to load file {$file} caused  by {$e->getMessage()}");
			}
		}
        
        // section 127-0-1-1-47db9c49:12bc8da1be4:-8000:00000000000026E6 end

        return $returnValue;
    }

    /**
     * Retrive a QTI_Item instance by it's id
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string serial
     * @param  string type
     * @return taoItems_models_classes_QTI_Data
     */
    public function getDataBySerial($serial, $type = '')
    {
        $returnValue = null;

        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024E1 begin
        try {
        	$returnValue = taoItems_models_classes_QTI_QTISessionCache::singleton()->get($serial);
	    	if(!empty($type) && !$returnValue instanceof $type) {
	       		throw new common_Exception("object retrieved is a ".get_class($returnValue)." instead of {$type}.");
	    	}
	    } catch (tao_models_classes_cache_NotFoundException $e) {
        	// do nothing, return null
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
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Data composed
     * @return taoItems_models_classes_QTI_Data
     */
    public function getComposingData( taoItems_models_classes_QTI_Data $composed)
    {
        $returnValue = null;

        // section 127-0-1-1-fba198:12b7c55f735:-8000:00000000000025B8 begin
        
        if(taoItems_models_classes_QTI_Data::$persist == false){
        	throw new common_Exception("The composing data are got from the persistance!");
        } elseif (!is_null($composed)) {
        	$predecessor = $composed->getPredecessors();
        	if (count($predecessor) == 1) {
        		$returnValue = array_pop($predecessor);
        	} else {
        		throw new common_exception_Error("Called getComposingData on a non tree, ".count($predecessor)." predecessors");
        	}
        }
        /*
        if(!is_null($composed)){
        	$tokens = explode('_', get_class($composed));
        	$objectType = strtolower($tokens[count($tokens) - 1]);
        	$propertyName = $objectType . 's';
        	$methodName = 'get'.ucfirst($propertyName);
			
			//singular property name:
			$singularPropertyName = $objectType;
        	$singularMethodName = 'get'.ucfirst($singularPropertyName);
        }
        
		foreach (taoItems_models_classes_QTI_QTISessionCache::singleton()->getAll() as $serial => $instance) {
			
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
        */
        // section 127-0-1-1-fba198:12b7c55f735:-8000:00000000000025B8 end

        return $returnValue;
    }

    /**
     * force the saving of the object in the persistancy. Usually an object is
     * by destruction.
     * Use this method if you know what your are doing.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Data qtiObject
     * @return boolean
     */
    public function saveDataToSession( taoItems_models_classes_QTI_Data $qtiObject)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-11450a84:12b8101447d:-8000:00000000000028DE begin
        taoItems_models_classes_QTI_QTISessionCache::singleton()->put($qtiObject);
        $returnValue = true;
        // section 10-13-1-39-11450a84:12b8101447d:-8000:00000000000028DE end

        return (bool) $returnValue;
    }

    /**
     * Build the XHTML/CSS/JS from a QTI_Item to be rendered.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item the item to render
     * @return string
     */
    public function renderQTIItem( taoItems_models_classes_QTI_Item $item)
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
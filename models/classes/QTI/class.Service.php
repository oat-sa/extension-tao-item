<?php

error_reporting(E_ALL);

/**
 * The QTI_Service gives you a central access to the managment methods of the
 * objects
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_models_classes_Service
 *
 * @author firstname and lastname of author, <author@example.org>
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
 * @author firstname and lastname of author, <author@example.org>
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
     * Retrive a QTI_Item instance by it's id
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
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
	        		throw new Exception("Unable to unserialie  session entry identified by $serial");
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
     * Short description of method getComposingData
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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
        }
        
		$instances = taoItems_models_classes_QTI_Data::$_instances;
        foreach(Session::getAttributeNames() as $attrKey){
        	if(preg_match("/^".taoItems_models_classes_QTI_Data::PREFIX."/", $attrKey)){
        		if(!in_array($attrKey, $instances)){
        			$instances[] = $attrKey;
        		}
        	}
        }
        
        foreach($instances as $serial){
        	$instance = $this->getDataBySerial($serial);
			
			if(is_null($instance)){
				//newly constructed object, that has not been saved into session yet.
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
        	if(!is_null($returnValue)){
        		break;
        	}
        }
        
        // section 127-0-1-1-fba198:12b7c55f735:-8000:00000000000025B8 end

        return $returnValue;
    }

    /**
     * Short description of method saveDataToSession
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Data qtiObject
     * @return boolean
     */
    public function saveDataToSession( taoItems_models_classes_QTI_Data $qtiObject)
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

} /* end of class taoItems_models_classes_QTI_Service */

?>
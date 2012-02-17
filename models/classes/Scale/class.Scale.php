<?php

error_reporting(E_ALL);

/**
 * A scale for the measurements of an item
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Scale
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037F5-includes begin
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037F5-includes end

/* user defined constants */
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037F5-constants begin
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037F5-constants end

/**
 * A scale for the measurements of an item
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Scale
 */
abstract class taoItems_models_classes_Scale_Scale
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Builds a Scale Object from the properties of the knowledge base
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource ressource
     * @return taoItems_models_classes_Scale_Scale
     */
    public static function buildFromRessource( core_kernel_classes_Resource $ressource)
    {
        $returnValue = null;

        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003824 begin
        foreach ($ressource->getType() as $type) {
			switch ($type->uriResource) {
	        	case taoItems_models_classes_Scale_Discrete::CLASS_URI:
	        		$scaleClass = 'taoItems_models_classes_Scale_Discrete';
	        		break;
	        	case taoItems_models_classes_Scale_Numerical::CLASS_URI:
	        		$scaleClass = 'taoItems_models_classes_Scale_Numerical';
	        		break;
	        	case taoItems_models_classes_Scale_Enumeration::CLASS_URI:
	        		$scaleClass = 'taoItems_models_classes_Scale_Enumeration';
	        		break;
	        	default:
	        		common_Logger::w('Unknown type '.$type->uriResource);
	        }
        }
        if (!isset($scaleClass)) {
        	throw new common_exception_Error('Unknown Scale Type for '.$ressource->uriResource);	
        }
    	$returnValue = new $scaleClass();
    	
    	if ($returnValue instanceof taoItems_models_classes_Scale_Numerical) {
    		$returnValue->lowerBound = $ressource->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_LOWER_BOUND_PROPERTY));
    		$returnValue->upperBound = $ressource->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_UPPER_BOUND_PROPERTY));
    	}
    	if ($returnValue instanceof taoItems_models_classes_Scale_Discrete) {
    		$returnValue->distance = $ressource->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_DISCRETE_SCALE_DISTANCE_PROPERTY));
    	}
        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003824 end

        return $returnValue;
    }

    /**
     * Prepares the properties for the knowledge base
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function toProperties()
    {
        $returnValue = array();

        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003829 begin
        if ($this instanceof taoItems_models_classes_Scale_NumericalScale) {
        	$returnValue[TAO_ITEM_LOWER_BOUND_PROPERTY] = $this->lowerBound;
        	$returnValue[TAO_ITEM_UPPER_BOUND_PROPERTY] = $this->upperBound;
        };
        if ($this instanceof taoItems_models_classes_Scale_DiscreteScale) {
        	$returnValue[TAO_ITEM_DISCRETE_SCALE_DISTANCE_PROPERTY] = $this->distance;
        };
        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003829 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getClassUri
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getClassUri()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003827 begin
        if (!defined(get_class($this).'::CLASS_URI')) {
        	throw new common_exception_Error('Missing CLASS_URI for Scale Implementation '.get_class($this));
        }
        $returnValue = static::CLASS_URI;
        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003827 end

        return (string) $returnValue;
    }

} /* end of abstract class taoItems_models_classes_Scale_Scale */

?>
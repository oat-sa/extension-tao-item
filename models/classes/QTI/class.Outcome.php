<?php

error_reporting(E_ALL);

/**
 * An outcome is a data build in item output. The SCORE is one of the most
 * outcomes.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10091
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The QTI_Data class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistance methods.
 * And give the interface for the rendering.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/**
 * The QTI_Item object represent the assessmentItem.
 * It's the main QTI object, it contains all the other objects and is the main
 * point
 * to render a complete item.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#section10042
 */
require_once('taoItems/models/classes/QTI/class.Item.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002347-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002347-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002347-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002347-constants end

/**
 * An outcome is a data build in item output. The SCORE is one of the most
 * outcomes.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10091
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Outcome
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute defaultValue
     *
     * @access protected
     * @var string
     */
    protected $defaultValue = '';

    /**
     * The scale to used for this outcome, this is NOT supported in the QTI. It
     * be serialized in the session but excluded by extractVariables()
     *
     * @access protected
     * @var Scale
     */
    protected $scale = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getDefaultValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getDefaultValue()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002418 begin
        
        $returnValue = $this->defaultValue;
        
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002418 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setDefaultValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string value
     * @return mixed
     */
    public function setDefaultValue($value)
    {
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:000000000000241A begin
        
    	$this->defaultValue = $value;
    	
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:000000000000241A end
    }

    /**
     * get the outcome in JSON format
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function toJSON()
    {
        $returnValue = null;

        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A1D begin
        
        $outcomeValue = null;
        if ($this->defaultValue != '') {
            $outcomeValue = Array($this->defaultValue);
        } else if ($this->options['baseType'] == 'integer' || $this->options['baseType'] == 'float'){
            $outcomeValue = Array(0);
        } else {
            $outcomeValue = null;
        }
        
        $returnValue = taoItems_models_classes_Matching_VariableFactory::createJSONVariableFromQTIData(
    		$this->getIdentifier()
    		, $this->options['cardinality']
    		, $this->options['baseType']
    		, $outcomeValue
    	);
        
        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A1D end

        return $returnValue;
    }

    /**
     * Short description of method destroy
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function destroy()
    {
        // section 127-0-1-1--7c75b87:1355db19059:-8000:00000000000037B3 begin
        foreach (taoItems_models_classes_QTI_QTISessionCache::singleton()->getAll() as $instance) {
        	if ($instance instanceof taoItems_models_classes_QTI_Item) {
        		$found = false;
        		foreach ($instance->getOutcomes() as $outcome) {
        			if ($outcome == $this) {
        				$found = true;
        				break;
        			}
        		}
        		if ($found) {
        			$instance->removeOutcome($this);
        			break;
        		}
        	}
        }
        parent::destroy();
        // section 127-0-1-1--7c75b87:1355db19059:-8000:00000000000037B3 end
    }

    /**
     * used to extract the measurements of this item to the ontology
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return taoItems_models_classes_Measurement
     */
    public function toMeasurement()
    {
        $returnValue = null;

        // section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037E0 begin
        $interpretation = $this->getOption('interpretation');
        $returnValue = new taoItems_models_classes_Measurement($this->getIdentifier(), $interpretation);
        if (!is_null($this->getScale())) {
        	$returnValue->setScale($this->getScale());
        }
        // section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037E0 end

        return $returnValue;
    }

    /**
     * to prevent the toQTI function to include the scale we overwrite the
     * to exclude scale
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    protected function extractVariables()
    {
        $returnValue = array();

        // section 127-0-1-1-67366732:1359ace6a59:-8000:0000000000003829 begin
        $returnValue = parent::extractVariables();
        unset($returnValue['scale']);
        // section 127-0-1-1-67366732:1359ace6a59:-8000:0000000000003829 end

        return (array) $returnValue;
    }

    /**
     * Short description of method removeScale
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function removeScale()
    {
        // section 127-0-1-1-67366732:1359ace6a59:-8000:000000000000382C begin
        $this->scale = null;
        // section 127-0-1-1-67366732:1359ace6a59:-8000:000000000000382C end
    }

    /**
     * Short description of method setScale
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Scale scale
     * @return mixed
     */
    public function setScale( taoItems_models_classes_Scale_Scale $scale)
    {
        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003809 begin
        $this->scale = $scale;
        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003809 end
    }

    /**
     * Short description of method getScale
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return taoItems_models_classes_Scale_Scale
     */
    public function getScale()
    {
        $returnValue = null;

        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:000000000000380C begin
       	$returnValue = $this->scale;
        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:000000000000380C end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Outcome */

?>
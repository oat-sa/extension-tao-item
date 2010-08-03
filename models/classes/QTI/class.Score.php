<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - taoItems/models/classes/QTI/class.Score.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 03.08.2010, 11:04:54 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_models_classes_QTI_Data
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/**
 * include taoItems_models_classes_QTI_Response
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Response.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002347-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002347-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002347-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002347-constants end

/**
 * Short description of class taoItems_models_classes_QTI_Score
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Score
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute defaultValue
     *
     * @access protected
     * @var int
     */
    protected $defaultValue = 0;

    /**
     * Short description of attribute mapping
     *
     * @access protected
     * @var array
     */
    protected $mapping = array();

    // --- OPERATIONS ---

    /**
     * Short description of method getDefaultValue
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return int
     */
    public function getDefaultValue()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002418 begin
        
        $returnValue = $this->defaultValue;
        
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002418 end

        return (int) $returnValue;
    }

    /**
     * Short description of method setDefaultValue
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  int value
     * @return mixed
     */
    public function setDefaultValue($value)
    {
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:000000000000241A begin
        
    	$this->defaultValue = $value;
    	
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:000000000000241A end
    }

    /**
     * Short description of method getMappging
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getMappging()
    {
        $returnValue = array();

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:000000000000241D begin
        
        $returnValue = $this->mapping;
        
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:000000000000241D end

        return (array) $returnValue;
    }

    /**
     * Short description of method setMapping
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array map
     * @return mixed
     */
    public function setMapping($map)
    {
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:000000000000241F begin
        
    	if(!is_array($map)){
    		$map = array($map);
    	}
    	$this->mapping = $map;
    	
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:000000000000241F end
    }

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toXHTML()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--4be859a6:12a33452171:-8000:0000000000002462 begin
        // section 127-0-1-1--4be859a6:12a33452171:-8000:0000000000002462 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--4be859a6:12a33452171:-8000:0000000000002464 begin
        // section 127-0-1-1--4be859a6:12a33452171:-8000:0000000000002464 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Score */

?>
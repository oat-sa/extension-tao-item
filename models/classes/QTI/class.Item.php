<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - taoItems/models/classes/QTI/class.Item.php
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
 * include taoItems_models_classes_QTI_Interaction
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Interaction.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000233F-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000233F-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000233F-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000233F-constants end

/**
 * Short description of class taoItems_models_classes_QTI_Item
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Item
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute interactions
     *
     * @access protected
     * @var array
     */
    protected $interactions = array();

    // --- OPERATIONS ---

    /**
     * Short description of method getInteractions
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getInteractions()
    {
        $returnValue = array();

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023D6 begin
        
        $returnValue = $this->interactions;
        
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023D6 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInteractions
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array interactions
     * @return mixed
     */
    public function setInteractions($interactions)
    {
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023D8 begin
        
    	$this->interactions = array();
    	foreach($interactions as $interaction){
    		$this->addInteraction($interaction);
    	}
    	
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023D8 end
    }

    /**
     * Short description of method getInteraction
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_QTI_Interaction
     */
    public function getInteraction($id)
    {
        $returnValue = null;

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023DB begin
        
        if(!empty($id)){
	        if(array_key_exists($id, $this->interactions)){
	        	$returnValue = $this->interactions[$id];
	        }
        }
        
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023DB end

        return $returnValue;
    }

    /**
     * Short description of method addInteraction
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Interaction interaction
     * @return mixed
     */
    public function addInteraction( taoItems_models_classes_QTI_Interaction $interaction)
    {
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023DF begin
        
    	if(!is_null($interaction)){
    		$this->interactions[$interaction->getId()] = $interaction;
    	}
    	
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023DF end
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

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002388 begin
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002388 end

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

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000238A begin
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000238A end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Item */

?>
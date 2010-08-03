<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
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
 * include taoItems_models_classes_QTI_Choice
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Choice.php');

/**
 * include taoItems_models_classes_QTI_Data
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/**
 * include taoItems_models_classes_QTI_Item
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Item.php');

/**
 * include taoItems_models_classes_QTI_Response
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Response.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002341-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002341-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002341-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002341-constants end

/**
 * Short description of class taoItems_models_classes_QTI_Interaction
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
abstract class taoItems_models_classes_QTI_Interaction
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute choices
     *
     * @access protected
     * @var array
     */
    protected $choices = array();

    /**
     * Short description of attribute response
     *
     * @access protected
     * @var Response
     */
    protected $response = null;

    // --- OPERATIONS ---

    /**
     * Short description of method setChoices
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array choices
     * @return mixed
     */
    public function setChoices($choices)
    {
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023EE begin
        
    	$this->choices = array();
    	foreach($choices as $choice){
    		$this->addChoice($choice);
    	}
    	
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023EE end
    }

    /**
     * Short description of method getChoices
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getChoices()
    {
        $returnValue = array();

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F1 begin
        
        $returnValue = $this->choices;
        
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F1 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getChoice
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_QTI_Choice
     */
    public function getChoice($id)
    {
        $returnValue = null;

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F3 begin
        
        if(!empty($id)){
        	if(array_key_exists($id, $this->choices)){
        		$returnValue = $this->choices[$id];
        	}
        }
        
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F3 end

        return $returnValue;
    }

    /**
     * Short description of method addChoice
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Choice choice
     * @return mixed
     */
    public function addChoice( taoItems_models_classes_QTI_Choice $choice)
    {
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F6 begin
        
    	if(!is_null($choice)){
    		$this->choices[$choice->getId()] = $choice;
    	}
    	
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F6 end
    }

    /**
     * Short description of method getResponse
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return taoItems_models_classes_QTI_Response
     */
    public function getResponse()
    {
        $returnValue = null;

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F9 begin
        
        $returnValue = $this->response;
        
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F9 end

        return $returnValue;
    }

    /**
     * Short description of method setResponse
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Response response
     * @return mixed
     */
    public function setResponse( taoItems_models_classes_QTI_Response $response)
    {
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023FB begin
        
    	$this->response = $response;
    	
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023FB end
    }

} /* end of abstract class taoItems_models_classes_QTI_Interaction */

?>
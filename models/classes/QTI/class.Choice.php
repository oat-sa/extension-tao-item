<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.Choice.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 10.08.2010, 16:44:14 with ArgoUML PHP module 
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
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002345-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002345-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002345-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002345-constants end

/**
 * Short description of class taoItems_models_classes_QTI_Choice
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Choice
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute name
     *
     * @access protected
     * @var string
     */
    protected $name = '';

    /**
     * Short description of attribute value
     *
     * @access protected
     * @var string
     */
    protected $value = '';

    // --- OPERATIONS ---

    /**
     * Short description of method getName
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--4be859a6:12a33452171:-8000:0000000000002406 begin
        
        $returnValue = $this->name;
        
        // section 127-0-1-1--4be859a6:12a33452171:-8000:0000000000002406 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setName
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string name
     * @return mixed
     */
    public function setName($name)
    {
        // section 127-0-1-1--4be859a6:12a33452171:-8000:0000000000002408 begin
        
    	$this->name = $name;
    	
        // section 127-0-1-1--4be859a6:12a33452171:-8000:0000000000002408 end
    }

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getValue()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--4be859a6:12a33452171:-8000:000000000000240B begin
        
        $returnValue = $this->value;
        
        // section 127-0-1-1--4be859a6:12a33452171:-8000:000000000000240B end

        return (string) $returnValue;
    }

    /**
     * Short description of method setValue
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string value
     * @return mixed
     */
    public function setValue($value)
    {
        // section 127-0-1-1--4be859a6:12a33452171:-8000:000000000000240D begin
        
    	$this->value = $value;
    	
        // section 127-0-1-1--4be859a6:12a33452171:-8000:000000000000240D end
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

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023DF begin
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023DF end

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

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E1 begin
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023E1 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toForm
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return tao_helpers_form_Form
     */
    public function toForm()
    {
        $returnValue = null;

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:000000000000249B begin
        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:000000000000249B end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Choice */

?>
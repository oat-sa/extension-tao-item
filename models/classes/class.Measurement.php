<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/class.Measurement.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 20.02.2012, 17:19:56 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037CD-includes begin
// section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037CD-includes end

/* user defined constants */
// section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037CD-constants begin
// section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037CD-constants end

/**
 * Short description of class taoItems_models_classes_Measurement
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes
 */
class taoItems_models_classes_Measurement
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute identifier
     *
     * @access private
     * @var string
     */
    private $identifier = '';

    /**
     * Short description of attribute scale
     *
     * @access private
     * @var Scale
     */
    private $scale = null;

    /**
     * Short description of attribute description
     *
     * @access private
     * @var string
     */
    private $description = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string identifier
     * @param  string description
     * @return mixed
     */
    public function __construct($identifier, $description = null)
    {
        // section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037D8 begin
        $this->identifier	= $identifier;
        $this->description	= $description;
        // section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037D8 end
    }

    /**
     * Short description of method getIdentifier
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getIdentifier()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037DD begin
        $returnValue = $this->identifier;
        // section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037DD end

        return (string) $returnValue;
    }

    /**
     * Short description of method getDescription
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getDescription()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037EE begin
        $returnValue = $this->description;
        // section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037EE end

        return (string) $returnValue;
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

        // section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037EC begin
        $returnValue = $this->scale;
        // section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037EC end

        return $returnValue;
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
        // section 127-0-1-1-67366732:1359ace6a59:-8000:000000000000382E begin
        $this->scale = $scale;
        // section 127-0-1-1-67366732:1359ace6a59:-8000:000000000000382E end
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
        // section 127-0-1-1-67366732:1359ace6a59:-8000:0000000000003831 begin
        $this->scale = null;
    	// section 127-0-1-1-67366732:1359ace6a59:-8000:0000000000003831 end
    }

} /* end of class taoItems_models_classes_Measurement */

?>
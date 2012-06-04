<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.Object.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 18.05.2012, 17:44:45 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
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

/* user defined includes */
// section 127-0-1-1-2549921c:137563a02f1:-8000:0000000000003A73-includes begin
// section 127-0-1-1-2549921c:137563a02f1:-8000:0000000000003A73-includes end

/* user defined constants */
// section 127-0-1-1-2549921c:137563a02f1:-8000:0000000000003A73-constants begin
// section 127-0-1-1-2549921c:137563a02f1:-8000:0000000000003A73-constants end

/**
 * Short description of class taoItems_models_classes_QTI_Object
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Object
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toXHTML()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-2549921c:137563a02f1:-8000:0000000000003A8E begin
        $data = $this->getOption('data');
        $options = $this->getOptions();
        unset($options['data']);
        $returnValue = '<object data="'.$data.'"'.$this->xmlizeOptions($options, true).'></object>';
        // section 127-0-1-1-2549921c:137563a02f1:-8000:0000000000003A8E end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--769dcec7:1375f36ffc9:-8000:0000000000003A8E begin
        $returnValue = '<object '.$this->xmlizeOptions().'></object>';
        // section 127-0-1-1--769dcec7:1375f36ffc9:-8000:0000000000003A8E end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Object */

?>
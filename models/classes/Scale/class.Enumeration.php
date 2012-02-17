<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Scale/class.Enumeration.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 17.02.2012, 11:48:19 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Scale
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A scale for the measurements of an item
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/Scale/class.Scale.php');

/* user defined includes */
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037FA-includes begin
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037FA-includes end

/* user defined constants */
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037FA-constants begin
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037FA-constants end

/**
 * Short description of class taoItems_models_classes_Scale_Enumeration
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Scale
 */
class taoItems_models_classes_Scale_Enumeration
    extends taoItems_models_classes_Scale_Scale
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute values
     *
     * @access public
     * @var array
     */
    public $values = array();

    /**
     * Short description of attribute CLASS_URI
     *
     * @access public
     * @var string
     */
    const CLASS_URI = 'http://www.tao.lu/Ontologies/TAOItem.rdf#EnumerationScale';

    // --- OPERATIONS ---

} /* end of class taoItems_models_classes_Scale_Enumeration */

?>
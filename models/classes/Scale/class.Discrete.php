<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Scale/class.Discrete.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 17.02.2012, 11:49:04 with ArgoUML PHP module 
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
 * include taoItems_models_classes_Scale_Numerical
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/Scale/class.Numerical.php');

/* user defined includes */
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037F6-includes begin
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037F6-includes end

/* user defined constants */
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037F6-constants begin
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037F6-constants end

/**
 * Short description of class taoItems_models_classes_Scale_Discrete
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Scale
 */
class taoItems_models_classes_Scale_Discrete
    extends taoItems_models_classes_Scale_Numerical
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute CLASS_URI
     *
     * @access public
     * @var string
     */
    const CLASS_URI = 'http://www.tao.lu/Ontologies/TAOItem.rdf#DiscreteScale';

    /**
     * Short description of attribute distance
     *
     * @access public
     * @var double
     */
    public $distance = 0.0;

    // --- OPERATIONS ---

} /* end of class taoItems_models_classes_Scale_Discrete */

?>
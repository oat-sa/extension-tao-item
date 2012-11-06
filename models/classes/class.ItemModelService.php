<?php

error_reporting(E_ALL);

/**
 * this class is extended by the ItemModels
 * to implement their functionalities
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Service is the base class of all services, and implements the singleton
 * for derived services
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/class.Service.php');

/* user defined includes */
// section 10-30-1--78-5ccf71ea:13ad5bff220:-8000:0000000000003C08-includes begin
// section 10-30-1--78-5ccf71ea:13ad5bff220:-8000:0000000000003C08-includes end

/* user defined constants */
// section 10-30-1--78-5ccf71ea:13ad5bff220:-8000:0000000000003C08-constants begin
// section 10-30-1--78-5ccf71ea:13ad5bff220:-8000:0000000000003C08-constants end

/**
 * this class is extended by the ItemModels
 * to implement their functionalities
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes
 */
abstract class taoItems_models_classes_ItemModelService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * render will be called by itemService:deployItem, and will
     * return the rendered view of the item for the model
     *
     * @abstract
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource item
     * @return string
     */
    public abstract function render( core_kernel_classes_Resource $item);

} /* end of abstract class taoItems_models_classes_ItemModelService */

?>
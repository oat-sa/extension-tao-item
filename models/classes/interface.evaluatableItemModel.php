<?php

error_reporting(E_ALL);

/**
 * evaluates the responses for an item
 * and returns the outcomes as an associative array
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Interface to implement by item models
 *
 * @author Joel Bout, <joel@taotesting.com>
 */
require_once('taoItems/models/classes/interface.itemModel.php');

/* user defined includes */
// section 10-30-1--78--43051535:13d25564359:-8000:0000000000003C88-includes begin
// section 10-30-1--78--43051535:13d25564359:-8000:0000000000003C88-includes end

/* user defined constants */
// section 10-30-1--78--43051535:13d25564359:-8000:0000000000003C88-constants begin
// section 10-30-1--78--43051535:13d25564359:-8000:0000000000003C88-constants end

/**
 * evaluates the responses for an item
 * and returns the outcomes as an associative array
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage models_classes
 */
interface taoItems_models_classes_evaluatableItemModel
    extends taoItems_models_classes_itemModel
{


    // --- OPERATIONS ---

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  array responses
     * @return array
     */
    public function evaluate( core_kernel_classes_Resource $item, $responses);

} /* end of interface taoItems_models_classes_evaluatableItemModel */

?>
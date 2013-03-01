<?php

error_reporting(E_ALL);

/**
 * Interface to implement by item models
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C25-includes begin
// section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C25-includes end

/* user defined constants */
// section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C25-constants begin
// section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C25-constants end

/**
 * Interface to implement by item models
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage models_classes
 */
interface taoItems_models_classes_itemModel
{


    // --- OPERATIONS ---

    /**
     * constructor called by itemService
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return mixed
     */
    public function __construct();

    /**
     * render used for deploy and preview
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return string
     */
    public function render( core_kernel_classes_Resource $item);

} /* end of interface taoItems_models_classes_itemModel */

?>
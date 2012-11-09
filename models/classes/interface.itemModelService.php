<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/interface.itemModelService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.11.2012, 17:06:11 with ArgoUML PHP module 
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
// section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C25-includes begin
// section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C25-includes end

/* user defined constants */
// section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C25-constants begin
// section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C25-constants end

/**
 * Short description of class taoItems_models_classes_itemModelService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes
 */
interface taoItems_models_classes_itemModelService
{


    // --- OPERATIONS ---

    /**
     * Short description of method render
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource item
     * @return string
     */
    public function render( core_kernel_classes_Resource $item);

} /* end of interface taoItems_models_classes_itemModelService */

?>
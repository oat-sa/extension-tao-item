<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
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
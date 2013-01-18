<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\models\classes\QTI\class.QTISessionCache.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 18.01.2013, 14:05:26 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_cache_SessionCache
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/cache/class.SessionCache.php');

/* user defined includes */
// section 127-0-1-1--18485ef3:13542665222:-8000:00000000000065AA-includes begin
// section 127-0-1-1--18485ef3:13542665222:-8000:00000000000065AA-includes end

/* user defined constants */
// section 127-0-1-1--18485ef3:13542665222:-8000:00000000000065AA-constants begin
// section 127-0-1-1--18485ef3:13542665222:-8000:00000000000065AA-constants end

/**
 * Short description of class taoItems_models_classes_QTI_QTISessionCache
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_QTISessionCache
    extends common_cache_SessionCache
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute SESSION_KEY
     *
     * @access public
     * @var string
     */
    const SESSION_KEY = 'cache_qti';

    // --- OPERATIONS ---

} /* end of class taoItems_models_classes_QTI_QTISessionCache */

?>
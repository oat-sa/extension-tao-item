<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.QTISessionCache.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 27.03.2012, 13:57:56 with ArgoUML PHP module 
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
 * include tao_models_classes_cache_SessionCache
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/cache/class.SessionCache.php');

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
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_QTISessionCache
    extends tao_models_classes_cache_SessionCache
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
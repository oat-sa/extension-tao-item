<?php

error_reporting(E_ALL);

/**
 * This exception is used by the server-sided evaluation of the Matching rule.
 * exitResponse is encountered no further rules should be executed.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-6f9e545f:134ec499acb:-8000:000000000000358C-includes begin
// section 127-0-1-1-6f9e545f:134ec499acb:-8000:000000000000358C-includes end

/* user defined constants */
// section 127-0-1-1-6f9e545f:134ec499acb:-8000:000000000000358C-constants begin
// section 127-0-1-1-6f9e545f:134ec499acb:-8000:000000000000358C-constants end

/**
 * This exception is used by the server-sided evaluation of the Matching rule.
 * exitResponse is encountered no further rules should be executed.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
class taoItems_models_classes_Matching_ExitResponseException
    extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getSeverity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return int
     */
    public function getSeverity()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-6f9e545f:134ec499acb:-8000:0000000000003590 begin
        $returnValue = common_Logger::TRACE_LEVEL;
        // section 127-0-1-1-6f9e545f:134ec499acb:-8000:0000000000003590 end

        return (int) $returnValue;
    }

} /* end of class taoItems_models_classes_Matching_ExitResponseException */

?>
<?php

error_reporting(E_ALL);

/**
 * By implementing the exportable interface, the object must export it's data to
 * formats defined here.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--3f707dcb:12af06fca53:-8000:0000000000004158-includes begin
// section 127-0-1-1--3f707dcb:12af06fca53:-8000:0000000000004158-includes end

/* user defined constants */
// section 127-0-1-1--3f707dcb:12af06fca53:-8000:0000000000004158-constants begin
// section 127-0-1-1--3f707dcb:12af06fca53:-8000:0000000000004158-constants end

/**
 * By implementing the exportable interface, the object must export it's data to
 * formats defined here.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
interface taoItems_models_classes_QTI_Exportable
{


    // --- OPERATIONS ---

    /**
     * Export the data in XHTML format
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toXHTML();

    /**
     * EXport the data in the QTI XML format
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toQTI();

    /**
     * EXport the data into TAO's objects Form
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return tao_helpers_form_xhtml_Form
     */
    public function toForm();

} /* end of interface taoItems_models_classes_QTI_Exportable */

?>
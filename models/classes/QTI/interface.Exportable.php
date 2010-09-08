<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/interface.Exportable.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.09.2010, 10:31:46 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
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
 * Short description of class taoItems_models_classes_QTI_Exportable
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
     * Short description of method toXHTML
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toXHTML();

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toQTI();

    /**
     * Short description of method toForm
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return tao_helpers_form_Form
     */
    public function toForm();

} /* end of interface taoItems_models_classes_QTI_Exportable */

?>
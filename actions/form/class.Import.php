<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/actions/form/class.Import.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 15.10.2010, 16:27:33 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This container initialize the import form.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/actions/form/class.Import.php');

/* user defined includes */
// section 127-0-1-1-2993bc96:12baebd89c3:-8000:0000000000002653-includes begin
// section 127-0-1-1-2993bc96:12baebd89c3:-8000:0000000000002653-includes end

/* user defined constants */
// section 127-0-1-1-2993bc96:12baebd89c3:-8000:0000000000002653-constants begin
// section 127-0-1-1-2993bc96:12baebd89c3:-8000:0000000000002653-constants end

/**
 * Short description of class taoItems_actions_form_Import
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */
class taoItems_actions_form_Import
    extends tao_actions_form_Import
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute formats
     *
     * @access protected
     * @var array
     */
    protected $formats = array('csv', 'qti');

    // --- OPERATIONS ---

    /**
     * Short description of method initQTIElements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initQTIElements()
    {
        // section 127-0-1-1-2993bc96:12baebd89c3:-8000:000000000000268B begin
        
    	//create file upload form box
		$fileElt = tao_helpers_form_FormFactory::getElement('source', 'AsyncFile');
		$fileElt->setDescription(__("Add the source file"));
    	if(isset($_POST['import_sent_qti'])){
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		}
		else{
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty', array('message' => '')));
		}
		$fileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('text/xml', 'application/xml', 'application/x-xml'), 'extension' => array('xml'))),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => 2000000))
		));
    	
		$this->form->addElement($fileElt);
		$this->form->createGroup('file', __('Upload QTI File'), array('source'));
		
		$qtiSentElt = tao_helpers_form_FormFactory::getElement('import_sent_qti', 'Hidden');
		$qtiSentElt->setValue(1);
		$this->form->addElement($qtiSentElt);
    	
        // section 127-0-1-1-2993bc96:12baebd89c3:-8000:000000000000268B end
    }

} /* end of class taoItems_actions_form_Import */

?>
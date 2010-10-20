<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/actions/form/class.Import.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 20.10.2010, 14:45:54 with ArgoUML PHP module 
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
    protected $formats = array('csv' => 'CSV', 'qti' => 'QTI Item', 'qtipack' => 'QTI Package');

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

    /**
     * Short description of method initQTIPACKElements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initQTIPACKElements()
    {
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002786 begin
        
    	$descElt = tao_helpers_form_FormFactory::getElement('qti_desc', 'Label');
		$descElt->setValue(__('A QTI-Package is a Zip archive containing a imsmanifest.xml file and the QTI resources to import'));
		$this->form->addElement($descElt);
    	
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
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('application/zip', 'application/x-zip', 'application/x-zip-compressed', 'application/octet-stream'), 'extension' => array('zip'))),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => 3000000))
		));
    	
		$this->form->addElement($fileElt);
		$this->form->createGroup('file', __('Upload a QTI Package File'), array('qti_desc', 'source'));
		
		$qtiSentElt = tao_helpers_form_FormFactory::getElement('import_sent_qti', 'Hidden');
		$qtiSentElt->setValue(1);
		$this->form->addElement($qtiSentElt);
    	
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002786 end
    }

} /* end of class taoItems_actions_form_Import */

?>
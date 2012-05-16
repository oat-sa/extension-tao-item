<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/actions/form/class.Import.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 11.05.2012, 17:45:28 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This container initialize the import form.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
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
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */
class taoItems_actions_form_Import
    extends tao_actions_form_Import
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initQTIElements
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function initQTIElements()
    {
        // section 127-0-1-1-2993bc96:12baebd89c3:-8000:000000000000268B begin
        
    	$descElt = tao_helpers_form_FormFactory::getElement('qti_desc', 'Label');
		$descElt->setValue(__('A QTI item file is an XML file following the version 2.0 of the QTI standard.'));
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
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('text/xml', 'application/xml', 'application/x-xml'), 'extension' => array('xml'))),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => self::UPLOAD_MAX))
		));
    	
		$this->form->addElement($fileElt);
		
		/*
		$disableValidationElt = tao_helpers_form_FormFactory::getElement("disable_validation", 'Checkbox');
		$disableValidationElt->setDescription("Disable validation");
		$disableValidationElt->setOptions(array("on" => ""));
		$this->form->addElement($disableValidationElt);
		*/
		$this->form->createGroup('file', __('Upload QTI File'), array('source','qti_desc'/*, 'disable_validation'*/));
		
		$qtiSentElt = tao_helpers_form_FormFactory::getElement('import_sent_qti', 'Hidden');
		$qtiSentElt->setValue(1);
		$this->form->addElement($qtiSentElt);
    	
        // section 127-0-1-1-2993bc96:12baebd89c3:-8000:000000000000268B end
    }

    /**
     * Short description of method initQTIPACKElements
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function initQTIPACKElements()
    {
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002786 begin
        
    	$descElt = tao_helpers_form_FormFactory::getElement('qti_desc', 'Label');
		$descElt->setValue(__('A QTI package is a Zip archive containing a imsmanifest.xml file and the QTI resources to import.'));
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
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => self::UPLOAD_MAX))
		));
    	
		$this->form->addElement($fileElt);
		
		
		$disableValidationElt = tao_helpers_form_FormFactory::getElement("disable_validation", 'Checkbox');
		$disableValidationElt->setDescription("Disable validation");
		$disableValidationElt->setOptions(array("on" => ""));
		$this->form->addElement($disableValidationElt);
		
		$this->form->createGroup('file', __('Upload a QTI Package File'), array('qti_desc', 'source', 'disable_validation'));
		
		$qtiSentElt = tao_helpers_form_FormFactory::getElement('import_sent_qti', 'Hidden');
		$qtiSentElt->setValue(1);
		$this->form->addElement($qtiSentElt);
    	
        // section 127-0-1-1-5c65d02d:12bc97f5116:-8000:0000000000002786 end
    }

    /**
     * Short description of method initXHTMLElements
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function initXHTMLElements()
    {
        // section 127-0-1-1-2d0bb0b3:12c2c41fb7c:-8000:0000000000002858 begin
        
    	$descElt = tao_helpers_form_FormFactory::getElement('xhtml_desc', 'Label');
		$descElt->setValue(__('An Open Web Item package is a Zip archive containing an index.html file with the XHTML 1.0 Transitional Doctype and resources (images, scripts, css, video, etc.).'));
		$this->form->addElement($descElt);
    	
    	//create file upload form box
		$fileElt = tao_helpers_form_FormFactory::getElement('source', 'AsyncFile');
		$fileElt->setDescription(__("Add the source file"));
    	if(isset($_POST['import_sent_xhtml'])){
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		}
		else{
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty', array('message' => '')));
		}
		$fileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('application/zip', 'application/x-zip', 'application/x-zip-compressed', 'application/octet-stream'), 'extension' => array('zip'))),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => self::UPLOAD_MAX))
		));
    	
		$this->form->addElement($fileElt);
		
		$disableValidationElt = tao_helpers_form_FormFactory::getElement("disable_validation", 'Checkbox');
		$disableValidationElt->setDescription("Disable validation");
		$disableValidationElt->setOptions(array("on" => ""));
		$this->form->addElement($disableValidationElt);
		
		$this->form->createGroup('file', __('Upload an Open Web Item Package File'), array('xhtml_desc', 'source', 'disable_validation'));
		
		$xhtmlSentElt = tao_helpers_form_FormFactory::getElement('import_sent_xhtml', 'Hidden');
		$xhtmlSentElt->setValue(1);
		$this->form->addElement($xhtmlSentElt);
    	
        // section 127-0-1-1-2d0bb0b3:12c2c41fb7c:-8000:0000000000002858 end
    }

    /**
     * Short description of method getFormats
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    protected function getFormats()
    {
        $returnValue = array();

        // section 127-0-1-1--4252657e:1373c83a2a6:-8000:0000000000003A52 begin
        $returnValue = array(
        	'csv'		=> 'CSV',
        	'rdf'		=> 'RDF',
        	'qti'		=> __('QTI Item'),
        	'qtipack'	=> __('QTI Package'),
        	'xhtml'		=> __('Open Web Item Package')
        );
        $paperclass = new core_kernel_classes_Class(TAO_ITEM_MODEL_PAPERBASED);
        if ($paperclass->exists()) {
        	$returnValue['paper'] = __('Paper-based Item');
        }
        // section 127-0-1-1--4252657e:1373c83a2a6:-8000:0000000000003A52 end

        return (array) $returnValue;
    }

    /**
     * Short description of method initPaperElements
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function initPaperElements()
    {
        // section 127-0-1-1--4252657e:1373c83a2a6:-8000:0000000000003A56 begin
    	
    	//create file upload form box
		$fileElt = tao_helpers_form_FormFactory::getElement('source', 'AsyncFile');
		$fileElt->setDescription(__("Max filesize").' '.(tao_helpers_Environment::getFileUploadLimit()/1048576).' '.__("MB"));
    	if(isset($_POST['import_sent_xhtml'])){
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		}
		else{
			$fileElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty', array('message' => '')));
		}
		$fileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => tao_helpers_Environment::getFileUploadLimit()))
		));
    	
		$this->form->addElement($fileElt);
		
		$itemModel = tao_helpers_form_FormFactory::getElement('import_sent_paper', 'Hidden');
		$itemModel->setValue(1);
		$this->form->addElement($itemModel);
		
		$this->form->createGroup('file', __('Upload an paper-based Item'), array($fileElt->getName(), $itemModel->getName()));
		
		// section 127-0-1-1--4252657e:1373c83a2a6:-8000:0000000000003A56 end
    }

} /* end of class taoItems_actions_form_Import */

?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\class.CSSuploader.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.01.2011, 11:32:48 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @subpackage actions_QTIform
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FAB-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FAB-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FAB-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FAB-constants end

/**
 * Short description of class taoItems_actions_QTIform_CSSuploader
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @subpackage actions_QTIform
 */
class taoItems_actions_QTIform_CSSuploader
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute item
     *
     * @access protected
     * @var Item
     */
    protected $item = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Item item
     */
    public function __construct( taoItems_models_classes_QTI_Item $item)
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FAF begin
		
		$this->item = $item;
		$returnValue = parent::__construct(array(), array());
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FAF end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initForm()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB3 begin
		
		$this->form = tao_helpers_form_FormFactory::getForm('css_uploader');
		
		$actions = array();
		
		$this->form->setActions($actions, 'top');
		$this->form->setActions(array(), 'bottom');
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB3 end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB5 begin
		
		$serialElt = tao_helpers_form_FormFactory::getElement('itemSerial', 'Hidden');
		$serialElt->setValue($this->item->getSerial());
		$this->form->addElement($serialElt);
		
		$labelElt = tao_helpers_form_FormFactory::getElement('title', 'Textbox');
		$labelElt->setDescription(__('File name'));
		$this->form->addElement($labelElt);
		
		$importFileElt = tao_helpers_form_FormFactory::getElement("css_import", 'AsyncFile');
		$importFileElt->setAttribute('auto', false);
		$importFileElt->setDescription(__("Upload the style sheet (CSS format required)"));
		$importFileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('NotEmpty'),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => 3000000)),	
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('text/css'), 'extension' => array('css')))
		));
		$this->form->addElement($importFileElt);
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB5 end
    }

} /* end of class taoItems_actions_QTIform_CSSuploader */

?>
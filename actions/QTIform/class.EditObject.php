<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/actions/QTIform/class.EditObject.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 22.06.2012, 11:49:44 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
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
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B2E-includes begin
// section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B2E-includes end

/* user defined constants */
// section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B2E-constants begin
// section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B2E-constants end

/**
 * Short description of class taoItems_actions_QTIform_EditObject
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */
class taoItems_actions_QTIform_EditObject
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute object
     *
     * @access public
     * @var Object
     */
    public $object = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Object object
     * @return mixed
     */
    public function __construct( taoItems_models_classes_QTI_Object $object)
    {
        // section 127-0-1-1-4f2fac4c:138139660bb:-8000:0000000000003B37 begin
        $this->object = $object;
        parent::__construct();
        // section 127-0-1-1-4f2fac4c:138139660bb:-8000:0000000000003B37 end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        // section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B2F begin
        $this->form = tao_helpers_form_FormFactory::getForm('EditObjectForm');
		$this->form->setActions(array(), 'bottom');
        // section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B2F end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B31 begin
        $itemElt = tao_helpers_form_FormFactory::getElement('itemSerial', 'Hidden');
		$itemElt->setValue($this->object->getSerial());
		$this->form->addElement($itemElt);
		
    	//title:
		$urlElt = tao_helpers_form_FormFactory::getElement('objecturl', 'Textbox');
		$urlElt->setDescription(__('URL'));
		$urlElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('NotEmpty'),
			tao_helpers_form_FormFactory::getValidator('Url')	
		));
		$urlElt->setValue($this->object->getOption('data'));
		$this->form->addElement($urlElt);
		
		$heightElt = tao_helpers_form_FormFactory::getElement('height', 'Textbox');
		$heightElt->setDescription(__('Height'));
		$heightElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('Integer')
		));
		$heightElt->setValue($this->object->getOption('height'));
		$this->form->addElement($heightElt);
		
		$widthElt = tao_helpers_form_FormFactory::getElement('width', 'Textbox');
		$widthElt->setDescription(__('Width'));
		$widthElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('Integer')
		));
		$widthElt->setValue($this->object->getOption('width'));
		$this->form->addElement($widthElt);
		/*
		$typeElt = tao_helpers_form_FormFactory::getElement('type', 'Textbox');
		$typeElt->setDescription(__('Type'));
		$this->form->addElement($typeElt);
		*/
        // section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B31 end
    }

} /* end of class taoItems_actions_QTIform_EditObject */

?>
<?php

error_reporting(E_ALL);

/**
 * This container :
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('tao/helpers/form/class.FormContainer.php');

/**
 * This container initialize the login form.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */
class taoItems_actions_QTIform_CSSuploader
    extends tao_helpers_form_FormContainer
{
	
	/**
     * the class resource to create the form from
     *
     * @access protected
     * @var Item
     */
    protected $item = null;
	
	public function __construct(taoItems_models_classes_QTI_Item $item){
		
		$this->item = $item;
		$returnValue = parent::__construct(array(), array());
		
	}
	
	/**
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */
    public function initForm(){
	
		$this->form = tao_helpers_form_FormFactory::getForm('css_uploader');
		
		$actions = array();
		
		$this->form->setActions($actions, 'top');
		$this->form->setActions(array(), 'bottom');
		
    }
	
	public function initElements(){
		//serial
		$serialElt = tao_helpers_form_FormFactory::getElement('itemSerial', 'Hidden');
		$serialElt->setValue($this->item->getSerial());
		$this->form->addElement($serialElt);
		
		$labelElt = tao_helpers_form_FormFactory::getElement('title', 'Textbox');
		$labelElt->setDescription(__('File name'));
		$this->form->addElement($labelElt);
		
		$importFileElt = tao_helpers_form_FormFactory::getElement("css_import", 'AsyncFile');
		$importFileElt->setDescription(__("Upload the style sheet (CSS format required)"));
		$importFileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('NotEmpty'),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => 3000000)),	
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('text/css'), 'extension' => array('css')))
		));
		$this->form->addElement($importFileElt);
    }
	
	
}

?>
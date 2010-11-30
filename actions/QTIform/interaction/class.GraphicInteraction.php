<?php

error_reporting(E_ALL);

/**
 * This container initialize the qti item form:
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
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */
abstract class taoItems_actions_QTIform_interaction_GraphicInteraction
    extends taoItems_actions_QTIform_interaction_BlockInteraction
{
	
	public function setCommonElements(){
		parent::setCommonElements();
		
		$object = $this->interaction->getObject();
		
		//add the object form:
		$objectSrcElt = tao_helpers_form_FormFactory::getElement('object_data', 'Textbox');
		$objectSrcElt->setAttribute('class', 'qti-file-img');
		$objectSrcElt->setDescription(__('Image source url'));
		
		$objectWidthElt = tao_helpers_form_FormFactory::getElement('object_width', 'Textbox');
		$objectWidthElt->setDescription(__('Image width'));
		
		$objectHeightElt = tao_helpers_form_FormFactory::getElement('object_height', 'Textbox');
		$objectHeightElt->setDescription(__('Image height'));
		
		//note: no type element since it must be determined by the image type
		
		if(is_array($object)){
			if(isset($object['data'])){
				$objectSrcElt->setValue($object['data']);
			}
			if(isset($object['width'])){
				$objectWidthElt->setValue($object['width']);
			}
			if(isset($object['height'])){
				$objectHeightElt->setValue($object['height']);
			}
		}
		
		$this->form->addElement($objectSrcElt);
		$this->form->addElement($objectWidthElt);
		$this->form->addElement($objectHeightElt);
	}
}

?>
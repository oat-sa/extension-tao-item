<?php

error_reporting(E_ALL);

/**
 * This container initialize the qti string interaction response form:
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This container initialize the login form.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */
class taoItems_actions_QTIform_response_ExtendedtextInteraction
    extends taoItems_actions_QTIform_response_StringInteraction
{
	
	public function initElements(){
	
		parent::setCommonElements();
		
		//the fixed attribute element
		$orderedCardinalityElt = tao_helpers_form_FormFactory::getElement('ordered', 'Radiobox');
		$orderedCardinalityElt->setDescription(__('Ordered response'));
		$orderedCardinalityElt->setOptions(array(0 => __('no'), 1 => __('yes')));
		$orderedCardinalityElt->setValue(0);
		$cardinality = $this->response->getOption('cardinality');
		if(!empty($cardinality)){
			if($cardinality == 'ordered'){
				$orderedCardinalityElt->setValue(1);
			}
		}
		$this->form->addElement($orderedCardinalityElt);
    }
	
}

?>
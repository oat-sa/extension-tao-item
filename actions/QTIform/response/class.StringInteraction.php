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
abstract class taoItems_actions_QTIform_response_StringInteraction
    extends taoItems_actions_QTIform_response_Response
{
	
	public function setCommonElements(){
	
		parent::setCommonElements();
		
		$baseTypeElt = tao_helpers_form_FormFactory::getElement('baseType', 'Radiobox');
		$baseTypeElt->setDescription(__('Response variable type'));
		$options = array(
			'string' => __('String'),
			'integer' => __('Integer'),
			'float' => __('Float')
		);
		$baseTypeElt->setOptions($options);
		$baseType = $this->response->getOption('baseType');
		if(!empty($baseType)){
			if(in_array($baseType, array_keys($options))){
				$baseTypeElt->setValue($baseType);
			}else{
				$baseTypeElt->setValue('string');
			}
		}
		$this->form->addElement($baseTypeElt);
		
    }
	
}

?>
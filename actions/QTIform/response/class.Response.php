<?php

error_reporting(E_ALL);

/**
 * This container initialize the qti interaction response form:
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
abstract class taoItems_actions_QTIform_response_Response
    extends tao_helpers_form_FormContainer
{
	
	/**
     * the class resource to create the form from
     *
     * @access protected
     * @var Response
     */
    protected $response = null;
	
	public function __construct(taoItems_models_classes_QTI_Response $response){
		
		$this->response = $response;
		$returnValue = parent::__construct(array(), array());
		
	}
	
	/**
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */
    public function initForm(){
	
		$this->form = tao_helpers_form_FormFactory::getForm('Response_Form');
		
		$actions = array();
		
		$saveElt = tao_helpers_form_FormFactory::getElement('save', 'Free');
		$saveElt->setValue("<a href='#' class='response-form-submitter' ><img src='".TAOBASE_WWW."/img/save.png'  /> ".__('Apply')."</a>");
		$actions[] = $saveElt;
		
		$this->form->setActions($actions, 'top');
		$this->form->setActions(array(), 'bottom');
		
    }
	
	public function getResponse(){
		return $this->response;
	}
	
	public function setCommonElements(){
	
		//serial
		$serialElt = tao_helpers_form_FormFactory::getElement('responseSerial', 'Hidden');
		$serialElt->setValue($this->response->getSerial());
		$this->form->addElement($serialElt);
		
		//set response processing type:
		//TODO urencode:
		$availableTemplates = array(
			tao_helpers_Uri::encode(QTI_RESPONSE_TEMPLATE_MATCH_CORRECT) => __('correct'),
			tao_helpers_Uri::encode(QTI_RESPONSE_TEMPLATE_MAP_RESPONSE) => __('map'),
			tao_helpers_Uri::encode(QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT) => __('map point'),
		);
		$ResponseProcessingTplElt = tao_helpers_form_FormFactory::getElement('processingTemplate', 'Combobox');
		$ResponseProcessingTplElt->setDescription(__('Processing type'));
		$ResponseProcessingTplElt->setOptions($availableTemplates);
		$ResponseProcessingTplElt->setValue($this->response->getHowMatch());
		$this->form->addElement($ResponseProcessingTplElt);
		
    }
	
}

?>
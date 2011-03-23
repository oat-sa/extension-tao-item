<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\actions\QTIform\response\class.Response.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.01.2011, 11:32:47 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10074
 * @subpackage actions_QTIform_response
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
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050B8-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050B8-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050B8-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050B8-constants end

/**
 * Short description of class taoItems_actions_QTIform_response_Response
 *
 * @abstract
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10074
 * @subpackage actions_QTIform_response
 */
abstract class taoItems_actions_QTIform_response_Response
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute response
     *
     * @access protected
     * @var Response
     */
    protected $response = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Response response
     */
    public function __construct( taoItems_models_classes_QTI_Response $response)
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050BC begin
		
		$this->response = $response;
		$returnValue = parent::__construct(array(), array());
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050BC end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initForm()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050BF begin
		
		$this->form = tao_helpers_form_FormFactory::getForm('Response_Form');
		
		$actions = array();
		
		$saveElt = tao_helpers_form_FormFactory::getElement('save', 'Free');
		$saveElt->setValue("<a href='#' class='response-form-submitter' ><img src='".BASE_WWW."img/qtiAuthoring/update.png'  /> ".__('Update response & scoring modifications')."</a>");
		$actions[] = $saveElt;
		
		$this->form->setActions($actions, 'top');
		$this->form->setActions(array(), 'bottom');
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050BF end
    }

    /**
     * Short description of method getResponse
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return taoItems_models_classes_QTI_Response
     */
    public function getResponse()
    {
        $returnValue = null;

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050C1 begin
		$returnValue = $this->response;
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050C1 end

        return $returnValue;
    }

    /**
     * Short description of method setCommonElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return mixed
     */
    public function setCommonElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050C3 begin
		
		//serial
		$serialElt = tao_helpers_form_FormFactory::getElement('responseSerial', 'Hidden');
		$serialElt->setValue($this->response->getSerial());
		$this->form->addElement($serialElt);
		
		//set response processing type:
		$mapKey = tao_helpers_Uri::encode(QTI_RESPONSE_TEMPLATE_MAP_RESPONSE);
		$mapPointKey = tao_helpers_Uri::encode(QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT);
		
		$availableTemplates = array(
			tao_helpers_Uri::encode(QTI_RESPONSE_TEMPLATE_MATCH_CORRECT) => __('correct')
		);
		
		//get interaction type:
		$qtiService = tao_models_classes_ServiceFactory::get('taoItems_models_classes_QTI_Service');
		$interaction = $qtiService->getComposingData($this->response);
		if(!is_null($interaction)){
			switch(strtolower($interaction->getType())){
				case 'order':
				case 'graphicorder':{
					break;
				}
				case 'selectpoint';
				case 'positionobject':{
					$availableTemplates[$mapPointKey] = __('map point');
					break;
				}
				default:{
					$availableTemplates[$mapKey] = __('map');
				}
			}
		}
		
		$ResponseProcessingTplElt = tao_helpers_form_FormFactory::getElement('processingTemplate', 'Combobox');
		$ResponseProcessingTplElt->setDescription(__('Processing type'));
		$ResponseProcessingTplElt->setOptions($availableTemplates);
		$ResponseProcessingTplElt->setValue($this->response->getHowMatch());
		$this->form->addElement($ResponseProcessingTplElt);
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:00000000000050C3 end
    }

} /* end of abstract class taoItems_actions_QTIform_response_Response */

?>
<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

use oat\generis\model\OntologyRdfs;

/**
 * SaSItems Controller provide process services for in the Items
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems

 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoItems_actions_SaSItems extends taoItems_actions_Items
{

	/**
     * overrided to prevent exception:
     * if no class is selected, the root class is returned
     * @see TaoModule::getCurrentClass()
     * @return core_kernel_classes_Class
     */
    protected function getCurrentClass()
    {
        if ($this->hasRequestParameter('classUri')) {
        	return parent::getCurrentClass();
        }
		return $this->getRootClass();
    }

	/**
	 * Edit an instances
	 * @return void
	 */
	public function sasEditInstance()
    {
        $this->loadStandaloneMode();

        $clazz = $this->getCurrentClass();
		$instance = $this->getCurrentInstance();

		$formContainer = new tao_actions_form_Instance($clazz, $instance);
		$myForm = $formContainer->getForm();

		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($instance);
				$instance = $binder->bind($myForm->getValues());
				$instance = $this->getClassService()->setDefaultItemContent($instance);
				$this->setData('message', __('Item saved'));
			}
		}

		$this->setData('uri', tao_helpers_Uri::encode($instance->getUri()));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->getUri()));
		$this->setData('formTitle', __('Edit item'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', 'tao');
	}

	/**
	 * view and item
     * @throws common_exception_BadRequest
     * @throws common_exception_Error
     * @throws tao_models_classes_MissingRequestParameterException
	 * @return void
	 */
	public function viewItem()
    {
        $this->loadStandaloneMode();

		if (!$this->isXmlHttpRequest()) {
			throw new common_exception_BadRequest('wrong request mode');
		}

		$itemClass = $this->getCurrentClass();
		$item = $this->getCurrentInstance();

		$lang = null;
		if($this->hasRequestParameter('target_lang')){
			$lang = $this->getRequestParameter('target_lang');
		}

		$hiddenProperties = array(
			taoItems_models_classes_ItemsService::PROPERTY_ITEM_CONTENT
		);

		$properties = array();
		foreach($this->getClassService()->getClazzProperties($itemClass) as $property){
			if(in_array($property->getUri(), $hiddenProperties)){
				continue;
			}
			$range = $property->getRange();

			if(is_null($lang)){
				$propValues = $item->getPropertyValues($property);
			} else {
				$propContainer = $item->getPropertyValuesByLg($property, $lang);
				$propValues = $propContainer->getIterator();
			}
			foreach($propValues as $propValue){
				if($range->getUri() == OntologyRdfs::RDFS_LITERAL){
					$value = (string)$propValue;
				} else {
					$resource = new core_kernel_classes_Resource($propValue);
					$value = $resource->getLabel();
				}
				$properties[] = array(
					'name'	=> $property->getLabel(),
					'value'	=> $value
				);
			}
		}

		$previewData = $this->initPreview($item, $itemClass);
		if(count($previewData) == 0){
			$this->setData('preview', false);
			$this->setData('previewMsg', __('Not yet available'));
		}
		else{
			$this->setData('preview', true);
			$this->setData('instanceUri', tao_helpers_Uri::encode($item->getUri(), false));
			foreach($previewData as $key => $value){
				$this->setData($key, $value);
			}
		}

		$this->setData('uri', tao_helpers_Uri::encode($item->getUri()));
		$this->setData('classUri', tao_helpers_Uri::encode($itemClass->getUri()));

		$this->setData('label', $item->getLabel());
		$this->setData('itemProperties', $properties);
		$this->setView('view.tpl');
	}

    /**
     * Load the standalone mode
     */
    protected function loadStandaloneMode()
    {
        tao_helpers_Context::load('STANDALONE_MODE');
    }
}

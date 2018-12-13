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
 *               2012-2018 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use oat\generis\model\OntologyAwareTrait;
use oat\generis\model\OntologyRdfs;
use oat\oatbox\event\EventManagerAwareTrait;
use oat\tao\model\lock\LockManager;
use oat\taoItems\model\event\ItemRdfUpdatedEvent;
use oat\taoItems\model\event\ItemUpdatedEvent;
use oat\taoItems\model\ItemModelStatus;
use oat\tao\model\resources\ResourceWatcher;

/**
 * Items Controller provide actions performed from url resolution
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoItems_actions_Items extends tao_actions_SaSModule
{
    use OntologyAwareTrait;
    use EventManagerAwareTrait;

    /**
     * overwrite the parent defaultData, adding the item label to be sent to the view
     */
    protected function defaultData()
    {
        parent::defaultData();
        if($this->hasRequestParameter('uri')){
            $uri = $this->getRequestParameter('uri');
            $classUri = $this->getRequestParameter('classUri');
            if(!empty($uri)){
                $item = $this->getResource(tao_helpers_Uri::decode($uri));
                $this->setData('label', $item->getLabel());
                $this->setData('authoringUrl', _url('authoring', 'Items', 'taoItems', array('uri' => $uri, 'classUri' => $classUri)));
                $this->setData('previewUrl', $this->getClassService()->getPreviewUrl($item));
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see tao_actions_RdfController::getClassService()
     * @return taoItems_models_classes_ItemsService
     */
    protected function getClassService()
    {
        if (is_null($this->service)) {
            $this->service = taoItems_models_classes_ItemsService::singleton();
        }
        return $this->service;
    }

    /**
     * overwrite the parent addInstance to add the requiresRight only in Items
     * @requiresRight id WRITE
     */
    public function addInstance()
    {
        parent::addInstance();
    }

    /**
     * overwrite the parent addSubClass to add the requiresRight only in Items
     * @requiresRight id WRITE
     */
    public function addSubClass()
    {
        parent::addSubClass();
    }

    /**
     * overwrite the parent cloneInstance to add the requiresRight only in Items
     * @see tao_actions_TaoModule::cloneInstance()
     * @requiresRight uri READ
     * @requiresRight classUri WRITE
     */
    public function cloneInstance()
    {
        return parent::cloneInstance();
    }

    /**
     * overwrite the parent moveInstance to add the requiresRight only in Items
     * @see tao_actions_TaoModule::moveInstance()
     * @requiresRight uri WRITE
     * @requiresRight destinationClassUri WRITE
     */
    public function moveInstance()
    {
        return parent::moveInstance();
    }

    /**
     * overwrite the parent moveAllInstances to add the requiresRight only in Items
     * @see tao_actions_TaoModule::moveResource()
     * @requiresRight uri WRITE
     */
    public function moveResource()
    {
        return parent::moveResource();
    }

    /**
     * overwrite the parent moveAllInstances to add the requiresRight only in Items
     * @see tao_actions_TaoModule::moveAll()
     * @requiresRight ids WRITE
     */
    public function moveAll()
    {
        return parent::moveAll();
    }

    /**
     * overwrite the parent getOntologyData to add the requiresRight only in Items
     * @see tao_actions_TaoModule::getOntologyData()
     * @requiresRight classUri READ
     */
    public function getOntologyData()
    {
        return parent::getOntologyData();
    }

    /**
     * edit an item instance
     * @requiresRight id READ
     */
    public function editItem()
    {
        $this->defaultData();

        $itemClass = $this->getCurrentClass();
        $item = $this->getCurrentInstance();

        if(!$this->isLocked($item, 'item_locked.tpl')){

            // my lock
            $lock = LockManager::getImplementation()->getLockData($item);
            if (!is_null($lock) && $lock->getOwnerId() == $this->getSession()->getUser()->getIdentifier()) {
                $this->setData('lockDate', $lock->getCreationTime());
                $this->setData('id', $item->getUri());
            }

            $formContainer = new taoItems_actions_form_Item($itemClass, $item);
            $myForm = $formContainer->getForm();

            if ($this->hasWriteAccess($item->getUri())) {

                if($myForm->isSubmited() && $this->hasWriteAccess($item->getUri())){
                    if($myForm->isValid()){

                        $properties = $myForm->getValues();
                        if (array_key_exists('warning', $properties)) {
                            $this->logWarning('Warning property is still in use', ['backend']);
                            unset($properties['warning']);
                        }

                        //bind item properties and set default content:
                        $binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($item);
                        $item = $binder->bind($properties);

                        $this->getEventManager()->trigger(new ItemUpdatedEvent($item->getUri(), $properties));
                        $this->getEventManager()->trigger(new ItemRdfUpdatedEvent($item->getUri(), $properties));

                        //if item label has been changed, do not use getLabel() to prevent cached value from lazy loading
                        $label = $item->getOnePropertyValue(new core_kernel_classes_Property(OntologyRdfs::RDFS_LABEL));
                        $this->setData("selectNode", tao_helpers_Uri::encode($item->getUri()));
                        $this->setData('label', ($label != null) ? $label->literal : '');
                        $this->setData('message', __('Item saved'));
                        $this->setData('reload', true);
                    }
                }
            } else {
                $myForm->setActions(array());
            }

            $currentModel = $this->getClassService()->getItemModel($item);
            $hasPreview = false;
            $hasModel   = false;
            if(!empty($currentModel)) {
                $hasModel = true;
                $isDeprecated = $this->getClassService()->hasModelStatus($item, array(ItemModelStatus::INSTANCE_DEPRECATED));
                $hasPreview = !$isDeprecated && $this->getClassService()->hasItemContent($item);
            }

            $updatedAt = $this->getServiceLocator()->get(ResourceWatcher::SERVICE_ID)->getUpdatedAt($item);
            $this->setData('isPreviewEnabled', $hasPreview);
            $this->setData('updatedAt', $updatedAt);
            $this->setData('isAuthoringEnabled', $hasModel);

            $this->setData('formTitle', __('Edit Item'));
            $this->setData('myForm', $myForm->render());

            $this->setView('Items/editItem.tpl');
        }
    }

    /**
     * Edit a class
     * @requiresRight id READ
     */
    public function editItemClass()
    {
        $this->defaultData();

        $clazz = $this->getClass($this->getRequestParameter('id'));

        if($this->hasRequestParameter('property_mode')){
            $this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
        }

        $myForm = $this->getClassForm($clazz, $this->getClassService()->getRootClass());

        if ($this->hasWriteAccess($clazz->getUri())) {
            if($myForm->isSubmited()){
                if($myForm->isValid()){
                    if($clazz instanceof core_kernel_classes_Resource){
                        $this->setData("selectNode", tao_helpers_Uri::encode($clazz->getUri()));
                    }
                    $this->setData('message', __('Class schema saved'));
                    $this->setData('reload', false);
                }
            }
        } else {
            $myForm->setActions(array());
        }
        $this->setData('formTitle', __('Manage item class schema'));
        $this->setData('myForm', $myForm->render());
        $this->setView('form.tpl', 'tao');
    }

    /**
     * delete an item
     * called via ajax
     * @requiresRight id WRITE
     * @return void
     * @throws Exception
     */
    public function deleteItem()
    {
        return parent::deleteResource();
    }

    /**
     * delete a class
     * @requiresRight id WRITE
     * @throws Exception
     */
    public function deleteClass()
    {
        return parent::deleteClass();
    }

    /**
     * Delete all given resources
     *
     * @requiresRight ids WRITE
     *
     * @throws Exception
     */
    public function deleteAll()
    {
        return parent::deleteAll();
    }

    /**
     * @see TaoModule::translateInstance
     * @requiresRight uri WRITE
     * @return void
     */
    public function translateInstance()
    {
        $this->defaultData();
        parent::translateInstance();
        $this->setView('form.tpl', 'tao');
    }

    /**
     * Item Authoring tool loader action
     * @requiresRight id WRITE
     */
    public function authoring()
    {
        $this->defaultData();

        $item = $this->getResource($this->getRequestParameter('id'));

        if(!$this->isLocked($item, 'item_locked.tpl')){

            $this->setData('error', false);
            try{

                $itemModel = $this->getClassService()->getItemModel($item);
                if(!is_null($itemModel)){
                    $itemModelImpl = $this->getClassService()->getItemModelImplementation($itemModel);
                    $authoringUrl = $itemModelImpl->getAuthoringUrl($item);
                    if(!empty($authoringUrl)){
                        LockManager::getImplementation()->setLock($item, $this->getSession()->getUser()->getIdentifier());

                        return $this->forwardUrl($authoringUrl);
                    }
                }
                throw new common_exception_NoImplementation();
                $this->setData('instanceUri', tao_helpers_Uri::encode($item->getUri(), false));

            } catch (Exception $e) {
                $this->setData('error', true);
                //build clear error or warning message:
                if(!empty($itemModel) && $itemModel instanceof core_kernel_classes_Resource){
                    $errorMsg = __('No item authoring tool available for the selected type of item: %s'.$itemModel->getLabel());
                }else{
                    $errorMsg = __('No item type selected for the current item.')." {$item->getLabel()} ".__('Please select first the item type!');
                }
                $this->setData('errorMsg', $errorMsg);
            }
        }
    }
}

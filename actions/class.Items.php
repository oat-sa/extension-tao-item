<?php
/*
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

/**
 * Items Controller provide actions performed from url resolution
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoItems_actions_Items extends tao_actions_SaSModule
{

    /**
     * constructor: initialize the service and the default data
     * @return  Items
     */
    public function __construct(){

        parent::__construct();

        //the service is initialized by default
        $this->service = taoItems_models_classes_ItemsService::singleton();
        $this->defaultData();
    }

    /**
     * overwrite the parent defaultData, adding the item label to be sent to the view
     */
    protected function defaultData(){
        parent::defaultData();
        if($this->hasRequestParameter('uri')){
            $uri = $this->getRequestParameter('uri');
            $classUri = $this->getRequestParameter('classUri');
            if(!empty($uri)){
                $item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($uri));
                $this->setData('label', $item->getLabel());
                $this->setData('authoringUrl', _url('authoring', 'Items', 'taoItems', array('uri' => $uri, 'classUri' => $classUri)));
                $this->setData('previewUrl', $this->service->getPreviewUrl($item));
            }
        }
    }

    /**
     * overwrite the parent addInstance to add the requiresRight only in Items
     * @requiresRight classUri WRITE
     */
    public function addInstance(){
        parent::addInstance();
    }

    /**
     * overwrite the parent addSubClass to add the requiresRight only in Items
     * @requiresRight classUri WRITE
     */
    public function addSubClass(){
        parent::addSubClass();
    }

    /*
     * conveniance methods
     */

    /**
     * get the main class
     * @return core_kernel_classes_Classes
     */
    protected function getClassService(){
        return taoItems_models_classes_ItemsService::singleton();
    }

    /*
     * controller actions
     */

    /**
     * get the class content
     * 
     * @requiresRight classUri WRITE
     */
    public function getOntologyData()
    {
        return parent::getOntologyData();
    }
    
    /**
     * edit an item instance
     * @requiresRight uri WRITE
     */
    public function editItem(){



        $itemClass = $this->getCurrentClass();
        $item = $this->getCurrentInstance();

        if(!$this->isLocked($item, 'item_locked.tpl')){

            //$this->setView('item_locked.tpl');
            $formContainer = new taoItems_actions_form_Item($itemClass, $item);
            $myForm = $formContainer->getForm();

            if($myForm->isSubmited()){
                if($myForm->isValid()){

                    $properties = $myForm->getValues();
                    unset($properties[TAO_ITEM_CONTENT_PROPERTY]);
                    unset($properties['warning']);

                    //bind item properties and set default content:
                    $binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($item);
                    $item = $binder->bind($properties);
                    $item = $this->service->setDefaultItemContent($item);

                    //if item label has been changed, do not use getLabel() to prevent cached value from lazy loading
                    $label = $item->getOnePropertyValue(new core_kernel_classes_Property(RDFS_LABEL));
                    $this->setData("selectNode", tao_helpers_Uri::encode($item->getUri()));
                    $this->setData('label', ($label != null) ? $label->literal : '');
                    $this->setData('message', __('Item saved'));
                    $this->setData('reload', true);
                }
            }
            
            $currentModel = $this->service->getItemModel($item);
            $hasPreview = false;
            $hasModel   = false;
            if(!empty($currentModel)) {
                $hasModel = true;
                $isDeprecated = $this->service->hasModelStatus($item, array(TAO_ITEM_MODEL_STATUS_DEPRECATED));
                $hasPreview = !$isDeprecated && $this->service->hasItemContent($item);
            }

            $myForm->removeElement(tao_helpers_Uri::encode(TAO_ITEM_CONTENT_PROPERTY));

            $this->setData('uri', tao_helpers_Uri::encode($item->getUri()));
            $this->setData('classUri', tao_helpers_Uri::encode($itemClass->getUri()));

            $this->setData('isPreviewEnabled', $hasPreview);
            $this->setData('isAuthoringEnabled', $hasModel);

            $this->setData('formTitle', __('Edit Item'));
            $this->setData('myForm', $myForm->render());

            $this->setView('item_form.tpl');
        }
    }

    /**
 * Display directly the content of the preview, outside any container
 */
    public function fullScreenPreview(){
        $item = $this->getCurrentInstance();

        $options = array(
            'uri' => $this->getRequestParameter('uri'),
            'fullScreen' => true
        );

        $itemModel = $this->service->getItemModel($item);
        if($itemModel != null && $itemModel->getUri() == TAO_ITEM_MODEL_QTI){
            $this->redirect(_url('index', 'QtiPreview', 'taoQtiItem', $options));
        }else{
            $this->redirect(_url('index', 'ItemPreview', 'taoItems', $options));
        }
    }

    /**
     * Display directly the content of the preview, outside any container
     *
     * Modified version of self::fullScreenPreview()
     * @Todo remove self::fullScreenPreview() as soon as this works as intended
     */
    public function itemPreview(){
        $item = $this->getCurrentInstance();

        $options = array(
            'uri' => $this->getRequestParameter('uri'),
            'fullScreen' => false
        );

        $itemModel = $this->service->getItemModel($item);
        if($itemModel != null && $itemModel->getUri() == TAO_ITEM_MODEL_QTI){
            $this->redirect(_url('index', 'QtiPreview', 'taoQtiItem', $options));
        }else{
            $this->redirect(_url('index', 'ItemPreview', 'taoItems', $options));
        }
    }

    /**
     * Get the Url with right options to run the preview
     * @param core_kernel_classes_Resource $item
     * @param core_kernel_classes_Class    $clazz
     * @return string|null
     */
    protected function getPreviewUrl(core_kernel_classes_Resource $item, core_kernel_classes_Class $clazz){

        $previewUrl = null;

        if($this->service->hasItemContent($item) && $this->service->isItemModelDefined($item)){

            $options = array(
                'uri' => tao_helpers_Uri::encode($item->getUri()),
                'classUri' => tao_helpers_Uri::encode($clazz->getUri()),
                'context' => false,
                'match' => 'server'
            );
            if($this->hasSessionAttribute('previewOpts')){
                $options = array_merge($options, $this->getSessionAttribute('previewOpts'));
            }

            $previewUrl = _url('index', 'ItemPreview', 'taoItems', $options);
        }

        return $previewUrl;
    }

    /**
     * Edit a class
     * @requiresRight classUri WRITE 
     */
    public function editItemClass(){
        $clazz = $this->getCurrentClass();

        if($this->hasRequestParameter('property_mode')){
            $this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
        }

        $myForm = $this->editClass($clazz, $this->service->getRootClass());
        if($myForm->isSubmited()){
            if($myForm->isValid()){
                if($clazz instanceof core_kernel_classes_Resource){
                    $this->setData("selectNode", tao_helpers_Uri::encode($clazz->getUri()));
                }
                $this->setData('message', __('Class saved'));
                $this->setData('reload', true);
            }
        }
        $this->setData('formTitle', __('Edit item class'));
        $this->setData('myForm', $myForm->render());
        $this->setView('form.tpl', 'tao');
    }

    /**
     * delete an item
     * called via ajax
     * @requiresRight uri WRITE 
     * @return void
     * @throws Exception
     */
    public function deleteItem(){
        if(!tao_helpers_Request::isAjax()){
            throw new Exception("wrong request mode");
        }

        $deleted = false;
        $deleted = $this->service->deleteItem($this->getCurrentInstance());
        echo json_encode(array('deleted' => $deleted));
    }

    /**
     * delete an item class
     * called via ajax
     * @requiresRight classUri WRITE
     * @return void
     * @throws Exception
     */
    public function deleteClass(){
        if(!tao_helpers_Request::isAjax()){
            throw new Exception("wrong request mode");
        }

        $deleted = false;
        $deleted = $this->service->deleteItemClass($this->getCurrentClass());
        echo json_encode(array('deleted' => $deleted));
    }

    /**
     * @see TaoModule::translateInstance
     * @requiresRight uri WRITE 
     * @return void
     */
    public function translateInstance(){
        parent::translateInstance();
        $this->setView('form.tpl');
    }

    /**
     * Display the Item.ItemContent property value.
     * It's used by the authoring runtime/tools to retrieve the content
     * @return void
     */
    public function getItemContent(){

        $this->setContentHeader('text/xml');

        try{
            //output direclty the itemContent as XML
            print $this->service->getItemContent($this->getCurrentInstance());
        }catch(Exception $e){
            //print an empty response
            print '<?xml version="1.0" encoding="utf-8" ?>';
            if(DEBUG_MODE){
                print '<exception><![CDATA[';
                print $e;
                print ']]></exception>';
            }
        }

        return;
    }

    /**
     * Download the content of the item in parameter
     */
    public function downloadItemContent(){

        $instance = $this->getCurrentInstance();
        if($this->service->isItemModelDefined($instance)){

            $itemModel = $instance->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
            $filename = $instance->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_SOURCENAME_PROPERTY));
            if(is_null($filename)){
                $filename = $itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY));
            }

            $itemContent = $this->service->getItemContent($instance);
            $size = strlen($itemContent);

            $this->setContentHeader('text/xml');
            header("Content-Length: $size");
            header("Content-Disposition: attachment; filename=\"{$filename}\"");
            header("Expires: 0");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            print $itemContent;
            return;
        }
    }

    /**
     * Item Authoring tool loader action
     * @return void
     */
    public function authoring(){
        $item = $this->getCurrentInstance();
        $itemClass = $this->getCurrentClass();

        if(!$this->isLocked($item, 'item_locked.tpl')){
            $this->setData('error', false);
            try{

                $itemModel = $this->service->getItemModel($item);
                if(!is_null($itemModel)){
                    $itemModelImpl = $this->service->getItemModelImplementation($itemModel);
                    $authoringUrl = $itemModelImpl->getAuthoringUrl($item);
                    if(!empty($authoringUrl)){
                        tao_models_classes_lock_DbLock::singleton()->setLock($item, tao_models_classes_UserService::singleton()->getCurrentUser());

                        return $this->forwardUrl($authoringUrl);
                    }
                }
                throw new common_exception_NoImplementation();
                $this->setData('instanceUri', tao_helpers_Uri::encode($item->getUri(), false));

            }catch(Exception $e){
                $this->setData('error', true);
                //build clear error or warning message:
                if(!empty($itemModel) && $itemModel instanceof core_kernel_classes_Resource){
                    $errorMsg = __('No item authoring tool available for the selected type of item: '.$itemModel->getLabel());
                }else{
                    $errorMsg = __('No item type selected for the current item.')." {$item->getLabel()} ".__('Please select first the item type!');
                }
                $this->setData('errorMsg', $errorMsg);
            }
        }
    }

    /**
     * Load an item external media
     * It prevents to get it direclty in the data folder that access is denied
     *
     */
    public function getMediaResource(){

        if($this->hasRequestParameter('path')){

            $item = null;
            if($this->hasRequestParameter('uri') && $this->hasRequestParameter('classUri')){
                $item = $this->getCurrentInstance();
            }else if($this->hasSessionAttribute('uri') && $this->hasSessionAttribute('classUri')){
                $classUri = tao_helpers_Uri::decode($this->getSessionAttribute('classUri'));
                if($this->service->isItemClass(new core_kernel_classes_Class($classUri))){
                    $item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getSessionAttribute('uri')));
                }
            }

            if(!is_null($item)){

                $path = urldecode($this->getRequestParameter('path'));
                if(!tao_helpers_File::securityCheck($path)){
                    throw new Exception('Unauthorized path '.$path);
                }
                if(preg_match('/(.)+\/filemanager\/views\/data\//i', $path)){
                    // check if the file is linked to the file manager
                    $resource = preg_replace('/(.)+\/filemanager\/views\/data\//i', ROOT_PATH.'/filemanager/views/data/', $path);
                }else{
                    // look in the item's dedicated folder. it should be a resource
                    // that is local to the item, not it the file manager
                    // $folder is the item's dedicated folder path, $path the path to the resource, relative to $folder
                    $folder = $this->service->getItemFolder($item);
                    $resource = tao_helpers_File::concat(array($folder, $path));
                }

                if(file_exists($resource)){
                    $mimeType = tao_helpers_File::getMimeType($resource);

                    //allow only images, video, flash (and css?)
                    if(preg_match("/^(image|video|audio|application\/x-shockwave-flash)/", $mimeType)){
                        header("Content-Type: $mimeType; charset utf-8");
                        print trim(file_get_contents($resource));
                    }
                }
            }
        }
    }


}

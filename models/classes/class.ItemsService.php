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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung
 *                         (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor
 *                         (under the project TAO-SUSTAIN & TAO-DEV);
 *               2012-2023 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT)
 */

use oat\taoItems\model\Command\DeleteItemCommand;
use oat\taoItems\model\TaoItemOntology;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\ServiceNotFoundException;
use oat\tao\model\lock\LockManager;
use oat\tao\model\OntologyClassService;
use oat\tao\model\TaoOntology;
use oat\taoItems\model\event\ItemContentClonedEvent;
use oat\taoItems\model\event\ItemDuplicatedEvent;
use oat\taoItems\model\event\ItemRemovedEvent;
use oat\taoItems\model\ItemModelStatus;
use oat\taoQtiItem\helpers\QtiFile;

/**
 * Service methods to manage the Items business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 */
class taoItems_models_classes_ItemsService extends OntologyClassService
{
    /**
     * Key to use to store the default filesource to be used in for new items
     */
    public const CONFIG_DEFAULT_FILESOURCE = 'defaultItemFileSource';

    /** @deprecated Use oat\taoItems\model\TaoItemOntology::PROPERTY_ITEM_MODEL instead */
    public const PROPERTY_ITEM_MODEL = TaoItemOntology::PROPERTY_ITEM_MODEL;

    /** @deprecated Use oat\taoItems\model\TaoItemOntology::PROPERTY_ITEM_CONTENT instead */
    public const PROPERTY_ITEM_CONTENT = TaoItemOntology::PROPERTY_ITEM_CONTENT;

    /** @deprecated Use oat\taoItems\model\TaoItemOntology::PROPERTY_MODEL_SERVICE instead */
    public const PROPERTY_ITEM_MODEL_SERVICE = TaoItemOntology::PROPERTY_MODEL_SERVICE;

    /** @deprecated Use oat\taoItems\model\TaoItemOntology::PROPERTY_ITEM_CONTENT_SOURCE_NAME instead */
    public const PROPERTY_ITEM_CONTENT_SRC = TaoItemOntology::PROPERTY_ITEM_CONTENT_SOURCE_NAME;

    /** @deprecated Use oat\taoItems\model\TaoItemOntology::PROPERTY_DATA_FILE_NAME instead */
    public const TAO_ITEM_MODEL_DATAFILE_PROPERTY = TaoItemOntology::PROPERTY_DATA_FILE_NAME;

    public const INSTANCE_SERVICE_ITEM_RUNNER = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ServiceItemRunner';

    public const INSTANCE_FORMAL_PARAM_ITEM_PATH = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#FormalParamItemPath';

    // phpcs:disable Generic.Files.LineLength
    public const INSTANCE_FORMAL_PARAM_ITEM_DATA_PATH = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#FormalParamItemDataPath';
    // phpcs:enable Generic.Files.LineLength

    public const INSTANCE_FORMAL_PARAM_ITEM_URI = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#FormalParamItemUri';

    private const DIV_CLASS_EMPTY = '<div class="empty"';

    public function getRootClass()
    {
        return $this->getClass(TaoOntology::CLASS_URI_ITEM);
    }

    public function getItemModelProperty()
    {
        return $this->getProperty(self::PROPERTY_ITEM_MODEL);
    }

    public function getItemContentProperty()
    {
        return $this->getProperty(self::PROPERTY_ITEM_CONTENT);
    }

    /**
     * get an item subclass by uri.
     * If the uri is not set, it returns the  item class (the top level class.
     * If the uri don't reference an item subclass, it returns null
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string uri
     * @return core_kernel_classes_Class
     * @deprecated
     */
    public function getItemClass($uri = '')
    {
        $returnValue = null;

        if (empty($uri)) {
            $returnValue = $this->getRootClass();
        } else {
            $clazz = $this->getClass($uri);
            if ($this->isItemClass($clazz)) {
                $returnValue = $clazz;
            }
        }

        return $returnValue;
    }

    /**
     * check if the class is a or a subclass of an Item
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  core_kernel_classes_Class clazz
     * @return boolean
     */
    public function isItemClass(core_kernel_classes_Class $clazz)
    {
        return $clazz->equals($this->getRootClass()) || $clazz->isSubClassOf($this->getRootClass());
    }

    public function delete(DeleteItemCommand $command): void
    {
        $resource = $command->getResource();

        if (LockManager::getImplementation()->isLocked($resource)) {
            $userId = common_session_SessionManager::getSession()->getUser()->getIdentifier();
            LockManager::getImplementation()->releaseLock($resource, $userId);
        }

        $result = $this->deleteItemContent($resource) && parent::deleteResource($resource);

        if (!$result) {
            throw new Exception(
                sprintf(
                    'Error deleting item content for resource "%s" [%s]',
                    $resource->getLabel(),
                    $resource->getUri()
                )
            );
        }

        $this->getEventManager()->trigger(
            new ItemRemovedEvent(
                $resource->getUri(),
                [
                    ItemRemovedEvent::PAYLOAD_KEY_DELETE_RELATED_ASSETS => $command->mustDeleteRelatedAssets(),
                ]
            )
        );
    }

    /**
     * please call deleteResource() instead
     * @deprecated
     */
    public function deleteItem(core_kernel_classes_Resource $item)
    {
        return $this->deleteResource($item);
    }

    /**
     * delete an item
     * @param core_kernel_classes_Resource $resource
     * @throws common_exception_Unauthorized
     * @return boolean
     * @deprecated use self::delete()
     */
    public function deleteResource(core_kernel_classes_Resource $resource)
    {
        try {
            $this->delete(new DeleteItemCommand($resource));

            return true;
        } catch (Throwable $exception) {
            return false;
        }
    }

    /**
     * delete an item class or subclass
     * @deprecated
     */
    public function deleteItemClass(core_kernel_classes_Class $clazz)
    {
        return $this->deleteClass($clazz);
    }

    /**
     * Check if the item has an itemContent Property
     *
     * @param core_kernel_classes_Resource $item
     * @param string $lang
     * @return bool
     * @throws Exception
     */
    public function hasItemContent(core_kernel_classes_Resource $item, $lang = '')
    {
        if (is_null($item)) {
            return false;
        }

        if (empty($lang)) {
            $lang = $this->getSessionLg();
        }

        $itemContents = $item->getPropertyValuesByLg($this->getItemContentProperty(), $lang);

        if ($itemContents->isEmpty()) {
            return false;
        }

        $file = $this->getItemDirectory($item, $lang)->getFile(QtiFile::FILE);

        return $file->exists() ? !(strpos($file->read(), self::DIV_CLASS_EMPTY) !== false) : false;
    }

    /**
     * Check if the Item has on of the itemModel property in the models array
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  array models the list of URI of the itemModel to check
     * @return boolean
     */
    public function hasItemModel(core_kernel_classes_Resource $item, $models)
    {
        $returnValue = false;

        $itemModel = $item->getOnePropertyValue($this->getItemModelProperty());
        if ($itemModel instanceof core_kernel_classes_Resource) {
            if (in_array($itemModel->getUri(), $models)) {
                $returnValue = true;
            }
        }

        return $returnValue;
    }

    /**
     * Check if the itemModel has been defined for that item
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return boolean
     */
    public function isItemModelDefined(core_kernel_classes_Resource $item)
    {
        $returnValue = false;

        if (!is_null($item)) {
            $model = $item->getOnePropertyValue($this->getItemModelProperty());
            if ($model instanceof core_kernel_classes_Literal) {
                if (strlen((string)$model) > 0) {
                    $returnValue = true;
                }
            } elseif (!is_null($model)) {
                $returnValue = true;
            }
        }

        return $returnValue;
    }

    /**
     * Get the runtime associated to the item model.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return core_kernel_classes_Resource
     */
    public function getModelRuntime(core_kernel_classes_Resource $item)
    {
        $returnValue = null;

        if (!is_null($item)) {
            $itemModel = $item->getOnePropertyValue($this->getItemModelProperty());
            if (!is_null($itemModel)) {
                $returnValue = $itemModel->getOnePropertyValue(
                    $this->getProperty(taoItems_models_classes_itemModel::CLASS_URI_RUNTIME)
                );
            }
        }

        return $returnValue;
    }

    /**
     * Short description of method hasModelStatus
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  array status
     * @return boolean
     */
    public function hasModelStatus(core_kernel_classes_Resource $item, $status)
    {
        $returnValue = false;

        if (!is_null($item)) {
            if (!is_array($status) && is_string($status)) {
                $status = [$status];
            }
            try {
                $itemModel = $item->getOnePropertyValue($this->getItemModelProperty());
                if ($itemModel instanceof core_kernel_classes_Resource) {
                    $itemModelStatus = $itemModel->getUniquePropertyValue(
                        $this->getProperty(ItemModelStatus::CLASS_URI)
                    );

                    if (in_array($itemModelStatus->getUri(), $status)) {
                        $returnValue = true;
                    }
                }
            } catch (common_exception_EmptyProperty $ce) {
                $returnValue = false;
            }
        }

        return $returnValue;
    }

    /**
     * render used for deploy and preview
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return string
     * @throws taoItems_models_classes_ItemModelException
     */
    public function render(core_kernel_classes_Resource $item, $language)
    {
        $itemModel = $this->getItemModel($item);
        if (is_null($itemModel)) {
            throw new common_exception_NoImplementation('No item model for item ' . $item->getUri());
        }
        $impl = $this->getItemModelImplementation($itemModel);
        if (is_null($impl)) {
            throw new common_exception_NoImplementation('No implementation for model ' . $itemModel->getUri());
        }
        return $impl->render($item, $language);
    }

    /**
     * @param string $itemContentDirectoryName
     * @param string $actualLang
     * @return string
     */
    public function composeItemDirectoryPath(string $itemContentDirectoryName, string $actualLang): string
    {
        return  $itemContentDirectoryName . DIRECTORY_SEPARATOR . 'itemContent' . DIRECTORY_SEPARATOR . $actualLang;
    }

    /**
     * Woraround for item content
     * (non-PHPdoc)
     * @see tao_models_classes_GenerisService::cloneInstanceProperty()
     */
    protected function cloneInstanceProperty(
        core_kernel_classes_Resource $source,
        core_kernel_classes_Resource $destination,
        core_kernel_classes_Property $property
    ) {
        if ($property->getUri() == self::PROPERTY_ITEM_CONTENT) {
            return $this->cloneItemContent($source, $destination, $property);
        } else {
            return parent::cloneInstanceProperty($source, $destination, $property);
        }
    }

    /**
     * Clone item content
     *
     * @param core_kernel_classes_Resource $source
     * @param core_kernel_classes_Resource $destination
     * @param core_kernel_classes_Property $property
     * @throws FileNotFoundException
     * @throws \oat\generis\model\fileReference\FileSerializerException
     * @throws common_Exception
     */
    protected function cloneItemContent(
        core_kernel_classes_Resource $source,
        core_kernel_classes_Resource $destination,
        core_kernel_classes_Property $property
    ) {

        $serializer = $this->getFileReferenceSerializer();
        $this->setItemModel($destination, $this->getItemModel($source));

        foreach ($source->getUsedLanguages($this->getItemContentProperty()) as $lang) {
            $sourceItemDirectory = $this->getItemDirectory($source, $lang);
            $destinationItemDirectory = $this->getItemDirectory($destination, $lang);
            $propertyValuesCollection = $source->getPropertyValuesCollection($property, ['lg' => $lang]);

            foreach ($propertyValuesCollection->getIterator() as $propertyValue) {
                $id = $propertyValue instanceof core_kernel_classes_Resource
                    ? $propertyValue->getUri()
                    : (string)$propertyValue;
                $sourceDirectory = $serializer->unserializeDirectory($id);
                $iterator = $sourceDirectory->getFlyIterator(Directory::ITERATOR_FILE | Directory::ITERATOR_RECURSIVE);

                foreach ($iterator as $iteratorFile) {
                    $newFile = $destinationItemDirectory->getFile($sourceItemDirectory->getRelPath($iteratorFile));
                    $newFile->write($iteratorFile->readStream());
                }

                $destinationDirectory = $destinationItemDirectory->getDirectory(
                    $sourceItemDirectory->getRelPath($sourceDirectory)
                );
                $serializer->serialize($destinationDirectory);
            }
        }
        $this->getEventManager()->trigger(
            new ItemContentClonedEvent($source->getUri(), $destination->getUri())
        );
    }

    public function cloneInstance(core_kernel_classes_Resource $instance, core_kernel_classes_Class $clazz = null)
    {
        $result = parent::cloneInstance($instance, $clazz);
        if ($result) {
            // Fixes duplicate item models after cloning.
            $itemModels = $result->getPropertyValues($this->getItemModelProperty());
            if (count($itemModels) > 1) {
                $result->editPropertyValues($this->getItemModelProperty(), current($itemModels));
            }
            $this->getEventManager()->trigger(new ItemDuplicatedEvent($instance->getUri(), $result->getUri()));
        }
        return $result;
    }


    public function getPreviewUrl(core_kernel_classes_Resource $item, $lang = '')
    {
        $itemModel = $this->getItemModel($item);
        if (is_null($itemModel)) {
            return null;
        }
        return $this->getItemModelImplementation($itemModel)->getPreviewUrl($item, $lang);
    }

    /**
     * Short description of method getItemModel
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return core_kernel_classes_Resource
     */
    public function getItemModel(core_kernel_classes_Resource $item)
    {
        $returnValue = null;

        $itemModel = $item->getOnePropertyValue($this->getItemModelProperty());
        if ($itemModel instanceof core_kernel_classes_Resource) {
            $returnValue = $itemModel;
        }

        return $returnValue;
    }

    /**
     * Set the model of an item
     *
     * @param core_kernel_classes_Resource $item
     * @param core_kernel_classes_Resource $model
     * @return boolean
     */
    public function setItemModel(core_kernel_classes_Resource $item, core_kernel_classes_Resource $model)
    {
        return $item->editPropertyValues($this->getProperty(self::PROPERTY_ITEM_MODEL), $model);
    }

    /**
     * Rertrieve current user's language from the session object to know where
     * item content should be located
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getSessionLg()
    {
        $sessionLang = \common_session_SessionManager::getSession()->getDataLanguage();
        if (empty($sessionLang)) {
            throw new Exception('the data language of the user cannot be found in session');
        }

        return (string)$sessionLang;
    }

    /**
     * Deletes the content but does not unreference it
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  core_kernel_classes_Resource item
     * @return boolean
     */
    public function deleteItemContent(core_kernel_classes_Resource $item)
    {
        // Delete item directory from filesystem
        $definitonFileValues = $item->getPropertyValues($this->getItemContentProperty());
        if (!empty($definitonFileValues)) {
            /** @var Directory $directory */
            $directory = $this->getFileReferenceSerializer()->unserializeDirectory(reset($definitonFileValues));
            if ($directory->exists()) {
                $directory->deleteSelf();
            }
        }

        //delete the folder for all languages!
        foreach ($item->getUsedLanguages($this->getItemContentProperty()) as $lang) {
            $files = $item->getPropertyValuesByLg($this->getItemContentProperty(), $lang);
            foreach ($files->getIterator() as $file) {
                if ($file instanceof core_kernel_classes_Resource) {
                    $this->getFileReferenceSerializer()->cleanUp($file->getUri());
                }
            }
        }

        return true;
    }

    /**
     * Get the correct implementation for a specific item model
     * @author Joel Bout, <joel@taotesting.com>
     * @access public
     *
     * @param  core_kernel_classes_Resource $itemModel
     *
     * @return taoItems_models_classes_itemModel
     * @throws common_exception_NoImplementation
     * @throws common_exception_Error
     */
    public function getItemModelImplementation(core_kernel_classes_Resource $itemModel)
    {
        $serviceId = (string)$itemModel->getOnePropertyValue($this->getProperty(self::PROPERTY_ITEM_MODEL_SERVICE));
        if (empty($serviceId)) {
            throw new common_exception_NoImplementation(
                'No implementation found for item model ' . $itemModel->getUri()
            );
        }
        try {
            $itemModelService = $this->getServiceManager()->get($serviceId);
        } catch (ServiceNotFoundException $e) {
            if (!class_exists($serviceId)) {
                throw new common_exception_Error('Item model service ' . $serviceId . ' not found');
            }
            // for backward compatibility support classname instead of a serviceid
            common_Logger::w('Outdated model definition "' . $serviceId . '", please use test model service');
            $itemModelService = new $serviceId();
        }
        if (!$itemModelService instanceof taoItems_models_classes_itemModel) {
            throw new common_exception_Error(
                'Item model service ' . get_class($itemModelService) . ' not compatible for item model ' . $serviceId
            );
        }

        return $itemModelService;
    }

    public function getCompilerClass(core_kernel_classes_Resource $item)
    {
        $itemModel = $this->getItemModel($item);
        if (is_null($itemModel)) {
            throw new common_exception_Error('undefined itemmodel for test ' . $item->getUri());
        }
        return $this->getItemModelImplementation($itemModel)->getCompilerClass();
    }

    /**
     * sets the filesource to use for new items
     *
     * @author Joel Bout, <joel@taotesting.com>
     * @param string $filesourceId
     */
    public function setDefaultFilesourceId($filesourceId)
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
        $ext->setConfig(self::CONFIG_DEFAULT_FILESOURCE, $filesourceId);
    }

    /**
     * Returns the items flysystem directory
     *
     * @param core_kernel_classes_Resource $item
     * @param string $language
     * @return \oat\oatbox\filesystem\Directory
     * @throws Exception
     * @throws common_Exception
     * @throws core_kernel_persistence_Exception
     */
    public function getItemDirectory(core_kernel_classes_Resource $item, $language = '')
    {
        // Get file by language
        if ($language === '') {
            $files = $item->getPropertyValues($this->getItemContentProperty());
        } else {
            $files = $item->getPropertyValuesByLg($this->getItemContentProperty(), $language)->toArray();
        }

        // If multiple files then throw exception
        if (count($files) > 1) {
            common_Logger::i(print_r($files, true));
            throw new common_Exception(__METHOD__ . ': Item ' . $item->getUri() . ' has multiple.');
        }

        // If there is one file then return directory
        if (count($files) == 1) {
            $file = reset($files);
            $file = is_object($file) && $file instanceof core_kernel_classes_Resource ? $file->getUri() : (string)$file;
            return $this->getFileReferenceSerializer()->unserializeDirectory($file);
        }

        // Otherwise there is no file, create one and return directory
        $model = $this->getItemModel($item);
        if (is_null($model)) {
            throw new common_Exception('Call to ' . __FUNCTION__ . ' for item without model');
        }

        $itemContentDirectoryName = tao_helpers_Uri::getUniqueId($item->getUri());
        // File does not exist, let's create it
        $actualLang = empty($language) ? $this->getSessionLg() : $language;

        $directoryPath = $this->composeItemDirectoryPath($itemContentDirectoryName, $actualLang);

        // Create item directory
        $itemDirectory = $this->getDefaultItemDirectory()->getDirectory($directoryPath);

        // Set uri file value as serial to item persistence
        $serial = $this->getFileReferenceSerializer()->serialize($itemDirectory);

        $item->setPropertyValueByLg($this->getItemContentProperty(), $serial, $actualLang);

        return $itemDirectory;
    }

    /**
     * Returns the defaul item directory
     * @return Directory
     * @throws common_ext_ExtensionException
     */
    public function getDefaultItemDirectory()
    {
        $filesystemId = common_ext_ExtensionsManager::singleton()
            ->getExtensionById('taoItems')
            ->getConfig(self::CONFIG_DEFAULT_FILESOURCE);

        return $this->getServiceManager()
            ->get(FileSystemService::SERVICE_ID)
            ->getDirectory($filesystemId);
    }

    /**
     * Get items of a specific model
     * @param string|core_kernel_classes_Resource $itemModel - the item model URI
     * @return core_kernel_classes_Resource[] the found items
     */
    public function getAllByModel($itemModel)
    {
        if (!empty($itemModel)) {
            $uri = ($itemModel instanceof core_kernel_classes_Resource) ? $itemModel->getUri() : $itemModel;
            return $this->getRootClass()->searchInstances([
                $this->getItemModelProperty()->getUri() => $uri
            ], [
                'recursive' => true
            ]);
        }
        return [];
    }

    /**
     * Get serializer to persist filesystem object
     *
     * @return FileReferenceSerializer
     */
    protected function getFileReferenceSerializer()
    {
        return $this->getServiceLocator()->get(FileReferenceSerializer::SERVICE_ID);
    }
}

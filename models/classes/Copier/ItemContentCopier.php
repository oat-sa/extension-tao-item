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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA.
 *
 * @author Andrei Shapiro <andrei.shapiro@taotesting.com>
 */

declare(strict_types=1);

namespace oat\taoItems\model\Copier;

use core_kernel_classes_Resource;
use oat\oatbox\event\EventManager;
use oat\oatbox\filesystem\Directory;
use oat\taoItems\model\TaoItemOntology;
use taoItems_models_classes_ItemsService;
use oat\taoItems\model\event\ItemContentClonedEvent;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\tao\model\resources\Contract\InstanceContentCopierInterface;

class ItemContentCopier implements InstanceContentCopierInterface
{
    /** @var FileReferenceSerializer */
    private $fileReferenceSerializer;

    /** @var taoItems_models_classes_ItemsService */
    private $itemsService;

    /** @var EventManager */
    private $eventManager;

    public function __construct(
        FileReferenceSerializer $fileReferenceSerializer,
        taoItems_models_classes_ItemsService $itemsService,
        EventManager $eventManager
    ) {
        $this->fileReferenceSerializer = $fileReferenceSerializer;
        $this->itemsService = $itemsService;
        $this->eventManager = $eventManager;
    }

    public function copy(
        core_kernel_classes_Resource $instance,
        core_kernel_classes_Resource $destinationInstance
    ): void {
        $this->copyItemModel($instance, $destinationInstance);
        $property = $instance->getProperty(TaoItemOntology::PROPERTY_ITEM_CONTENT);

        foreach ($instance->getUsedLanguages($property) as $lang) {
            $sourceItemDirectory = $this->itemsService->getItemDirectory($instance, $lang);
            $destinationItemDirectory = $this->itemsService->getItemDirectory($destinationInstance, $lang);
            $propertyValues = $instance->getPropertyValuesCollection($property, ['lg' => $lang]);

            foreach ($propertyValues as $propertyValue) {
                $id = $propertyValue instanceof core_kernel_classes_Resource
                    ? $propertyValue->getUri()
                    : (string) $propertyValue;

                $sourceDirectory = $this->fileReferenceSerializer->unserializeDirectory($id);
                $iterator = $sourceDirectory->getFlyIterator(
                    Directory::ITERATOR_FILE | Directory::ITERATOR_RECURSIVE
                );

                foreach ($iterator as $iteratorFile) {
                    $newFile = $destinationItemDirectory->getFile($sourceItemDirectory->getRelPath($iteratorFile));
                    $newFile->write($iteratorFile->readStream());
                }

                $destinationDirectory = $destinationItemDirectory->getDirectory(
                    $sourceItemDirectory->getRelPath($sourceDirectory)
                );
                $this->fileReferenceSerializer->serialize($destinationDirectory);
            }
        }

        $this->eventManager->trigger(
            new ItemContentClonedEvent($instance->getUri(), $destinationInstance->getUri())
        );
    }

    private function copyItemModel(
        core_kernel_classes_Resource $instance,
        core_kernel_classes_Resource $destinationInstance
    ): void {
        $itemModelProperty = $instance->getProperty(TaoItemOntology::PROPERTY_ITEM_MODEL);
        $model = $instance->getOnePropertyValue($itemModelProperty);

        $destinationInstance->editPropertyValues(
            $itemModelProperty,
            $model instanceof core_kernel_classes_Resource ? $model : null
        );
    }
}

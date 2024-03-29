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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoItems\model\pack;

use core_kernel_classes_Resource;
use oat\oatbox\filesystem\Directory;
use taoItems_models_classes_ItemsService;
use common_exception_NoImplementation;
use common_Exception;
use Exception;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * The Item Pack represents the item package data produced by the compilation.
 *
 * @package taoItems
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class Packer implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * The item to pack
     * @var core_kernel_classes_Resource
     */
    private $item;

    /**
     * The lang of the item to pack
     * @var string
     */
    private $lang;

    /**
     * The item service
     * @var taoItems_models_classes_ItemsService
     */
    private $itemService;

    /** @var bool */
    private $skipValidation;

    /**
     * Create a packer for an item
     *
     * @param core_kernel_classes_Resource $item
     * @param string $lang
     */
    public function __construct(core_kernel_classes_Resource $item, $lang = '', bool $skipValidation = false)
    {
        $this->item = $item;
        $this->lang = $lang;
        $this->itemService = taoItems_models_classes_ItemsService::singleton();
        $this->skipValidation = $skipValidation;
    }

    /**
     * Get the packer for the item regarding it's implementation.
     *
     * @return ItemPacker the item packer implementation
     * @throws common_exception_NoImplementation
     */
    protected function getItemPacker()
    {

        //look at the item model
        $itemModel = $this->itemService->getItemModel($this->item);
        if (is_null($itemModel)) {
            throw new common_exception_NoImplementation('No item model for item ' . $this->item->getUri());
        }

        //get the itemModel implementation for this model
        $impl = $this->itemService->getItemModelImplementation($itemModel);
        if (is_null($impl)) {
            throw new common_exception_NoImplementation('No implementation for model ' . $itemModel->getUri());
        }

        //then retrieve the packer class and instantiate it
        $packerClass = $impl->getPackerClass();
        if (is_null($packerClass) || get_parent_class($packerClass) !== 'oat\taoItems\model\pack\ItemPacker') {
            throw new common_exception_NoImplementation('The packer class seems to be not implemented');
        }

        return new $packerClass();
    }

    /**
     * Pack an item.
     * @param array $assetEncoders the list of encoders to use in packing (configure the item packer)
     * @param boolean $nestedResourcesInclusion
     * @return ItemPack of the item. It can be serialized directly.
     * @throws common_Exception
     */
    public function pack($assetEncoders = [], $nestedResourcesInclusion = true)
    {

        try {
            //call the factory to get the itemPacker implementation
            $itemPacker = $this->getItemPacker();

            $itemPacker->setAssetEncoders($assetEncoders);
            $itemPacker->setNestedResourcesInclusion($nestedResourcesInclusion);
            $itemPacker->setSkipValidation($this->skipValidation);

            //then create the pack
            $itemPack = $itemPacker->packItem($this->item, $this->lang, $this->getStorageDirectory());
        } catch (Exception $e) {
            throw new common_Exception('The item ' . $this->item->getUri() . ' cannot be packed : ' . $e->getMessage());
        }

        return $itemPack;
    }

    /**
     * @return Directory
     * @throws \Exception
     */
    protected function getStorageDirectory()
    {
        /** @var \oat\oatbox\filesystem\Directory $directory */
        $directory = $this->itemService->getItemDirectory($this->item, $this->lang);

        //we should use be language unaware for storage manipulation
        $path = str_replace($this->lang, '', $directory->getPrefix());

        return $this->itemService->getDefaultItemDirectory()->getDirectory($path);
    }
}

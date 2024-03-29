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
 * Copyright (c) 2015-2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

declare(strict_types=1);

namespace oat\taoItems\model\pack;

use InvalidArgumentException;
use JsonSerializable;
use LogicException;
use oat\tao\helpers\Base64;
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\sourceStrategy\HttpSource;
use oat\taoMediaManager\model\MediaSource;
use tao_models_classes_FileNotFoundException as FileNotFoundException;
use tao_models_classes_service_StorageDirectory as StorageDirectory;

/**
 * The Item Pack represents the item package data produced by the compilation.
 *
 * @package taoItems
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class ItemPack implements JsonSerializable
{
    /**
     * The supported assets types
     * @var string[]
     */
    private static $assetTypes = [
        'html',
        'document',
        'js',
        'css',
        'font',
        'img',
        'audio',
        'video',
        'xinclude',
        'apip',
        'pdf'
    ];

    /**
     * The item type
     * @var string
     */
    private $type;

    /**
     * The item data as arrays. Can be anything, just be careful of cyclic refs.
     * @var array
     */
    private $data = [];

    /**
     * The item's required assets by type
     * @var array
     */
    private $assets = [];

    /**
     * Determines what type of assets should be packed as well as packer
     * @example array('css'=>'base64')
     * @var array
     */
    protected $assetEncoders = [
        'html' => 'none',
        'document' => 'none',
        'js' => 'none',
        'css' => 'none',
        'font' => 'none',
        'img' => 'none',
        'audio' => 'none',
        'video' => 'none',
        'xinclude' => 'none',
        'apip' => 'none',
        'pdf' => 'none',
    ];

    /**
     * Should be @import or url() processed
     * @var bool
     */
    protected $nestedResourcesInclusion = true;

    /**
     * Creates an ItemPack with the required data.
     *
     * @param string $type the item type
     * @param array $data the item data
     *
     * @throw InvalidArgumentException
     */
    public function __construct($type, $data)
    {
        if (empty($type)) {
            throw new InvalidArgumentException('Please provide and item type');
        }
        if (!is_array($data)) {
            throw new InvalidArgumentException('Please provide the item data as an array');
        }
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the item type
     * @return string the type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the item data
     * @return array the data
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Set item's assets of a given type to the pack.
     *
     * @param string $type
     * @param $assets
     * @param StorageDirectory|null $publicDirectory
     * @param bool $skipBinaries
     *
     * @throws ExceptionMissingEncoder
     * @throws FileNotFoundException
     */
    public function setAssets(
        string $type,
        $assets,
        ?StorageDirectory $publicDirectory = null,
        bool $skipBinaries = false
    ): void {
        if (!is_array($assets)) {
            throw new InvalidArgumentException('Assets should be an array, "' . gettype($assets) . '" given');
        }

        foreach ($assets as $asset) {
            $this->setAsset($type, $asset, $publicDirectory, $skipBinaries);
        }
    }

    /**
     * @param string $type
     * @param $asset
     * @param StorageDirectory|null $publicDirectory
     * @param bool $skipBinaries
     *
     * @throws ExceptionMissingEncoder
     * @throws FileNotFoundException
     */
    public function setAsset(
        string $type,
        $asset,
        ?StorageDirectory $publicDirectory = null,
        bool $skipBinaries = false
    ): void {
        if (!in_array($type, self::$assetTypes, true)) {
            throw new InvalidArgumentException(sprintf(
                'Unknown asset type "%s", it should be either %s',
                $type,
                implode(', ', self::$assetTypes)
            ));
        }

        $encoder = EncoderService::singleton()->get($this->assetEncoders[$type], $publicDirectory);
        $assetKey = $this->getAssetKey($asset);
        if ($skipBinaries && Base64::isEncodedImage($asset->getMediaIdentifier())) {
            return;
        }
        $this->assets[$type][$assetKey] = $encoder->encode($asset);
    }

    /**
     * Get item's assets of a given type.
     *
     * @param string $type the assets type, one of those who are supported
     * @return string[] the list of assets' URL to load
     */
    public function getAssets(string $type): array
    {
        if (!array_key_exists($type, $this->assets)) {
            return [];
        }
        return $this->assets[$type];
    }

    /**
     * How to serialize the pack in JSON.
     *
     * phpcs:disable PSR1.Methods.CamelCapsMethodName
     */
    public function JsonSerialize()
    {
        return [
            'type'      => $this->type,
            'data'      => $this->data,
            'assets'    => $this->assets
        ];
    }
    // phpcs:enable PSR1.Methods.CamelCapsMethodName

    /**
     * @return array
     */
    public function getAssetEncoders(): array
    {
        return $this->assetEncoders;
    }

    /**
     * @param array $assetEncoders
     */
    public function setAssetEncoders(array $assetEncoders): void
    {
        foreach ($assetEncoders as $type => $encoder) {
            if ($encoder == '') {
                $this->assetEncoders[$type] = 'none';
            } else {
                $this->assetEncoders[$type] = $encoder;
            }
        }
    }

    /**
     * @return boolean
     */
    public function isNestedResourcesInclusion(): bool
    {
        return $this->nestedResourcesInclusion;
    }

    /**
     * @param boolean $nestedResourcesInclusion
     */
    public function setNestedResourcesInclusion(bool $nestedResourcesInclusion): void
    {
        $this->nestedResourcesInclusion = (bool)$nestedResourcesInclusion;
    }

    /**
     * @param string|MediaAsset $asset
     *
     * @throws FileNotFoundException
     *
     * @return string
     */
    private function getAssetKey($asset): string
    {
        if (!$asset instanceof MediaAsset) {
            if (!is_string($asset)) {
                throw new LogicException('Item pack can only pack assets as string url or MediaAsset');
            }
            return $asset;
        }

        $mediaSource = $asset->getMediaSource();
        $mediaIdentifier = $asset->getMediaIdentifier();

        if (
            $mediaSource instanceof MediaSource
            || $mediaSource instanceof HttpSource
            || Base64::isEncodedImage($mediaIdentifier)
        ) {
            return $mediaIdentifier;
        }

        return $mediaSource->getBaseName($mediaIdentifier);
    }
}

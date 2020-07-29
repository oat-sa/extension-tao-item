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
 * @author Mikhail Kamarouski, <kamarouski@1pt.com>
 */

namespace oat\taoItems\model\pack\encoders;

use oat\tao\helpers\Base64;
use oat\tao\model\media\MediaAsset;
use core_kernel_persistence_Exception;
use oat\taoMediaManager\model\MediaSource;
use tao_models_classes_FileNotFoundException;
use tao_models_classes_service_StorageDirectory;
use oat\taoItems\model\pack\ExceptionMissingAsset;
use oat\tao\model\media\sourceStrategy\HttpSource;

/**
 * Class Base64fileEncoder
 * Helper, encode file by uri for embedding  using base64 algorithm
 *
 * @package oat\taoItems\model\pack\encoders
 */
class Base64fileEncoder implements Encoding
{
    /** @var tao_models_classes_service_StorageDirectory */
    private $directory;

    /** Applied data-uri format placeholder */
    const DATA_PREFIX = 'data:%s;base64,%s';

    /**
     * Base64fileEncoder constructor.
     *
     * @param tao_models_classes_service_StorageDirectory $directory
     */
    public function __construct(tao_models_classes_service_StorageDirectory $directory)
    {
        $this->directory = $directory;
    }

    /**
     * @param string|MediaAsset $data name of the assert
     *
     * @throws ExceptionMissingAsset
     * @throws core_kernel_persistence_Exception
     * @throws tao_models_classes_FileNotFoundException
     *
     * @return mixed|string
     */
    public function encode($data)
    {
        // Skip  if external resource
        if (filter_var($data, FILTER_VALIDATE_URL)) {
            return $data;
        }

        if ($data instanceof MediaAsset) {
            $mediaSource = $data->getMediaSource();
            $data = $data->getMediaIdentifier();

            if ($mediaSource instanceof HttpSource || Base64::isEncodedImage($mediaSource)) {
                return $data;
            }

            if ($mediaSource instanceof MediaSource) {
                $fileInfo = $mediaSource->getFileInfo($data);
                $stream = $mediaSource->getFileStream($data);

                return sprintf(self::DATA_PREFIX, $fileInfo['mime'], base64_encode($stream->getContents()));
            }
        }

        $file = $this->directory->getFile($data);

        if ($file->exists()) {
            return sprintf(self::DATA_PREFIX, $file->getMimeType(), base64_encode($file->read()));
        }

        throw new ExceptionMissingAsset(sprintf(
            'Assets %s not found at %s',
            $data,
            $file->getPrefix()
        ));
    }
}

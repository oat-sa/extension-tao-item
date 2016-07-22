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
 * @author Mikhail Kamarouski, <kamarouski@1pt.com>
 */
namespace oat\taoItems\model\pack\encoders;

use League\Flysystem\Directory;
use oat\taoItems\model\pack\ExceptionMissingAsset;

/**
 * Class Base64fileEncoder
 * Helper, encode file by uri for embedding  using base64 algorithm
 * @package oat\taoItems\model\pack\encoders
 */
class Base64fileEncoder implements Encoding
{
    /**
     * @var Directory
     */
    private $directory;

    /**
     * Applied data-uri format placeholder
     */
    const DATA_PREFIX = 'data:%s;base64,%s';

    /**
     * @var null|string
     */
    private $lang;

    /**
     * Base64fileEncoder constructor.
     *
     * @param \tao_models_classes_service_StorageDirectory $directory
     * @param string $lang
     */
    public function __construct(\tao_models_classes_service_StorageDirectory $directory, $lang)
    {
        $this->directory = $directory;
        $this->lang = $lang;
    }


    /**
     * @param string $data name of the assert
     *
     * @return string
     * @throws ExceptionMissingAsset
     */
    public function encode( $data )
    {
        //skip  if external resource
        if (filter_var( $data, FILTER_VALIDATE_URL )) {
            return $data;
        }

        if ($this->directory->has($this->lang . '/' . $data)) {
            $file = $this->directory->read($this->lang . '/' . $data);
            return sprintf(self::DATA_PREFIX, \tao_helpers_File::getMimeType($data, false), base64_encode($file));
        }

        throw new ExceptionMissingAsset('Assets ' . $data . ' not found at ' . $this->directory->getPath() . ' for ' . $this->lang . ' locale');
    }
}

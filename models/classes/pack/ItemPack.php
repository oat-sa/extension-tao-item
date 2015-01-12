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

use \InvalidArgumentException;
use \JsonSerializable;

/**
 * The Item Pack represents the item package data produced by the compilation.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class ItemPack implements JsonSerializable 
{

    private static $assetTypes = array('js', 'css', 'font', 'img');

    private $type;
    private $data = array();
    private $assets = array();
  

    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function getType()
    {
        return $this->type;   
    } 
    
    public function getData()
    {
        return $this->data;   
    }

    public function setAssets($type, $assets)
    {
        if(!in_array($type, self::$assetTypes)){
            throw new InvalidArgumentException('Unknow asset type "' . $type . '", it should be either ' . implode(', ', self::$assetTypes));
        }
        if(!is_array($assets)){
            throw new InvalidArgumentException('Assests should be an array, "' . typeof($assets) . '" given');
        }

        $this->assets[$type] = $assets;
    }
 
    public function getAssets($type)
    {
        return $this->assets[$type];
    }

    public function JsonSerialize()
    {
        return array(
            'type'      => $this->type,
            'data'      => $this->data,
            'assests'   => $this->assets
        );
    }
}
?>
